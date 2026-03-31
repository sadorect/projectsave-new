<?php

namespace App\Support\Lms;

use App\Models\Course;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Support\Collection;

class StudentWorkspaceBuilder
{
    public function __construct(
        private readonly DiplomaProgramService $diplomaService
    ) {}

    /**
     * Build all view data for the student dashboard.
     *
     * @return array<string, mixed>
     */
    public function build(User $user): array
    {
        $enrolledCourses = $user->courses()
            ->with(['lessons', 'instructor', 'exams.questions'])
            ->get();

        $allCourses = Course::with(['lessons', 'instructor', 'exams'])
            ->where('status', 'published')
            ->get();

        $continueLearning  = $this->buildContinueLearning($user, $enrolledCourses);
        $availableCourses  = $this->buildAvailableCourses($user, $allCourses, $enrolledCourses);
        $examSummaries     = $this->buildExamSummaries($user, $enrolledCourses);
        $stats             = $this->buildStats($user, $enrolledCourses, $examSummaries);
        $diplomaEligibility = $this->diplomaService->eligibility($user);
        $featuredCertificates = $user->certificates()->latest()->take(3)->get();

        return [
            'stats'               => $stats,
            'continueLearning'    => $continueLearning,
            'availableCourses'    => $availableCourses,
            'examSummaries'       => $examSummaries,
            'communityGroups'     => $this->buildCommunityGroups(),
            'featuredCertificates' => $featuredCertificates,
            'diplomaCertificate'  => $diplomaEligibility['certificate'],
            'diplomaStatus'       => [
                'completed_requirements' => $diplomaEligibility['completed_requirements'],
                'required_count'         => $diplomaEligibility['required_count'],
                'progress_percentage'    => $diplomaEligibility['required_count'] > 0
                    ? round(($diplomaEligibility['completed_requirements'] / $diplomaEligibility['required_count']) * 100)
                    : 0,
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────

    private function buildStats(User $user, Collection $enrolledCourses, Collection $examSummaries): array
    {
        $totalLessons = 0;
        $completedLessons = 0;
        $inProgressCount = 0;
        $completedCourseCount = 0;

        foreach ($enrolledCourses as $course) {
            $progress = $user->getCourseProgress($course);
            $totalLessons    += $course->lessons->count();
            $completedLessons += (int) round(($progress / 100) * $course->lessons->count());

            if ($progress >= 100) {
                $completedCourseCount++;
            } elseif ($progress > 0) {
                $inProgressCount++;
            }
        }

        $overallProgress = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100)
            : 0;

        $passedExams = $examSummaries->filter(fn ($e) => $e['has_passed'])->count();

        return [
            'overall_progress'  => $overallProgress,
            'in_progress_courses' => $inProgressCount,
            'available_exams'   => $examSummaries->count(),
            'certificates'      => $user->certificates()->count(),
            'total_courses'     => Course::where('status', 'published')->count(),
            'enrolled_courses'  => $enrolledCourses->count(),
            'completed_courses' => $completedCourseCount,
            'passed_exams'      => $passedExams,
        ];
    }

    private function buildContinueLearning(User $user, Collection $enrolledCourses): Collection
    {
        return $enrolledCourses
            ->filter(fn ($course) => $user->getCourseProgress($course) < 100)
            ->map(function (Course $course) use ($user) {
                $progress         = round($user->getCourseProgress($course));
                $lessonCount      = $course->lessons->count();
                $completedLessons = (int) round(($progress / 100) * $lessonCount);

                // Find the next incomplete lesson
                $completedLessonIds = $user->lessonProgress()
                    ->whereHas('lesson', fn ($q) => $q->where('course_id', $course->id))
                    ->where('completed', true)
                    ->pluck('lesson_id');

                $nextLesson = $course->lessons
                    ->whereNotIn('id', $completedLessonIds)
                    ->sortBy('order')
                    ->first();

                return [
                    'id'                  => $course->id,
                    'title'               => $course->title,
                    'description_excerpt' => \Str::limit(strip_tags($course->description ?? ''), 85),
                    'featured_image_url'  => $course->featured_image_url,
                    'lesson_count'        => $lessonCount,
                    'completed_lessons'   => $completedLessons,
                    'progress'            => $progress,
                    'instructor_name'     => $course->instructor_name ?: ($course->instructor?->name ?? 'ASOM Faculty'),
                    'next_lesson_url'     => $nextLesson
                        ? route('lms.lessons.show', [$course->slug, $nextLesson->slug])
                        : null,
                ];
            })
            ->values();
    }

    private function buildAvailableCourses(User $user, Collection $allCourses, Collection $enrolledCourses): Collection
    {
        $enrolledIds = $enrolledCourses->pluck('id');

        return $allCourses
            ->whereNotIn('id', $enrolledIds)
            ->map(fn (Course $course) => [
                'id'                  => $course->id,
                'title'               => $course->title,
                'description_excerpt' => \Str::limit(strip_tags($course->description ?? ''), 85),
                'featured_image_url'  => $course->featured_image_url,
                'lesson_count'        => $course->lessons->count(),
                'exam_count'          => $course->exams->count(),
                'instructor_name'     => $course->instructor_name ?: ($course->instructor?->name ?? 'ASOM Faculty'),
                'course_url'          => route('lms.courses.show', $course->slug),
            ])
            ->values();
    }

    private function buildExamSummaries(User $user, Collection $enrolledCourses): Collection
    {
        $summaries = collect();

        foreach ($enrolledCourses as $course) {
            foreach ($course->exams as $exam) {
                if (! $exam->is_active && $exam->questions->count() < 5) {
                    continue;
                }

                $attempts = $user->examAttempts()
                    ->where('exam_id', $exam->id)
                    ->whereNotNull('completed_at')
                    ->orderByDesc('score')
                    ->get();

                $bestScore         = $attempts->max('score');
                $hasPassed         = $attempts->some(fn ($a) => $a->score >= $exam->passing_score);
                $totalAttempts     = $user->examAttempts()->where('exam_id', $exam->id)->count();
                $remainingAttempts = max(0, ($exam->max_attempts ?? 3) - $totalAttempts);

                $summaries->push([
                    'id'                => $exam->id,
                    'title'             => $exam->title,
                    'course_title'      => $course->title,
                    'has_passed'        => $hasPassed,
                    'question_count'    => $exam->questions->count(),
                    'remaining_attempts' => $remainingAttempts,
                    'best_score'        => $bestScore !== null ? round($bestScore) : null,
                    'action_url'        => route('lms.exams.show', $exam->id),
                    'action_label'      => $hasPassed ? 'Review exam' : ($totalAttempts > 0 ? 'Retake exam' : 'Start exam'),
                ]);
            }
        }

        return $summaries;
    }

    private function buildCommunityGroups(): array
    {
        return [
            [
                'name'        => 'ASOM General',
                'description' => 'Stay updated with announcements, new course releases, and ministry news.',
                'icon'        => 'fab fa-whatsapp',
                'url'         => 'https://chat.whatsapp.com/asom-general',
            ],
            [
                'name'        => 'Bible & Theology',
                'description' => 'Discuss Bible Introduction, Hermeneutics, and core theology with classmates.',
                'icon'        => 'fas fa-book-open',
                'url'         => 'https://chat.whatsapp.com/asom-bible',
            ],
            [
                'name'        => 'Ministry Practice',
                'description' => 'A space for Homiletics, Counseling, and Spiritual Gifts module discussions.',
                'icon'        => 'fas fa-hands-helping',
                'url'         => 'https://chat.whatsapp.com/asom-ministry',
            ],
            [
                'name'        => 'Prayer & Support',
                'description' => 'Share prayer requests, testimonies, and encouragement with fellow students.',
                'icon'        => 'fas fa-pray',
                'url'         => 'https://chat.whatsapp.com/asom-prayer',
            ],
        ];
    }
}
