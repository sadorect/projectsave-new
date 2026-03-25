<?php

namespace App\Support\Lms;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Collection;

class DiplomaProgramService
{
    /**
     * Course titles that constitute the ASOM Diploma in Ministry program.
     */
    private const REQUIRED_COURSE_TITLES = [
        'Bible Introduction',
        'Hermeneutics',
        'Ministry Vitals',
        'Spiritual Gifts & Ministry',
        'Biblical Counseling',
        'Homiletics',
    ];

    /**
     * Return the list of required course titles for the diploma program.
     */
    public function requiredCourseTitles(): array
    {
        return self::REQUIRED_COURSE_TITLES;
    }

    /**
     * Assess a user's eligibility for the Diploma in Ministry certificate.
     *
     * Returns:
     *  - eligible               (bool)
     *  - certificate            (?Certificate) — existing diploma cert if any
     *  - completed_requirements (int)
     *  - required_count         (int)
     *  - requirements           (array) — one entry per required course
     *  - remaining_course_titles (array) — titles of courses not yet satisfied
     */
    public function eligibility(User $user): array
    {
        $titles = self::REQUIRED_COURSE_TITLES;

        $programCourses = Course::whereIn('title', $titles)
            ->with('exams')
            ->get()
            ->keyBy('title');

        $requirements      = [];
        $completedCount    = 0;
        $remainingTitles   = [];

        foreach ($titles as $title) {
            $course = $programCourses->get($title);

            if (! $course) {
                $requirements[]  = ['title' => $title, 'is_satisfied' => false, 'reason' => 'Not available yet'];
                $remainingTitles[] = $title;
                continue;
            }

            $progress      = $user->getCourseProgress($course);
            $lessonsComplete = $progress >= 100;
            $passedExam    = $this->hasPassedAnExam($user, $course);
            $isSatisfied   = $lessonsComplete || $passedExam;

            $requirements[] = [
                'title'       => $title,
                'course_id'   => $course->id,
                'is_satisfied' => $isSatisfied,
                'progress'    => round($progress),
                'passed_exam' => $passedExam,
            ];

            if ($isSatisfied) {
                $completedCount++;
            } else {
                $remainingTitles[] = $title;
            }
        }

        $requiredCount = count($titles);
        $eligible      = $completedCount >= $requiredCount;

        $existingCertificate = Certificate::where('user_id', $user->id)
            ->whereNull('course_id')
            ->latest()
            ->first();

        return [
            'eligible'                => $eligible,
            'certificate'             => $existingCertificate,
            'completed_requirements'  => $completedCount,
            'required_count'          => $requiredCount,
            'requirements'            => $requirements,
            'remaining_course_titles' => $remainingTitles,
        ];
    }

    /**
     * Find or create a pending Diploma in Ministry certificate for the user.
     * Returns null when the user is not yet eligible.
     */
    public function ensurePendingCertificate(User $user, string $notes = ''): ?Certificate
    {
        $eligibility = $this->eligibility($user);

        if (! $eligibility['eligible']) {
            return null;
        }

        $existing = Certificate::where('user_id', $user->id)
            ->whereNull('course_id')
            ->first();

        if ($existing) {
            return $existing;
        }

        return Certificate::create([
            'user_id'      => $user->id,
            'course_id'    => null,
            'is_approved'  => false,
            'completed_at' => now(),
            'issued_at'    => now(),
            'notes'        => $notes,
        ]);
    }

    /**
     * Return per-course completion details for a user (for admin certificate views).
     */
    public function userCourseDetails(User $user): Collection
    {
        $titles = self::REQUIRED_COURSE_TITLES;

        $programCourses = Course::whereIn('title', $titles)
            ->with(['exams', 'lessons'])
            ->get()
            ->keyBy('title');

        return collect($titles)->map(function (string $title) use ($programCourses, $user) {
            $course = $programCourses->get($title);

            if (! $course) {
                return [
                    'title'         => $title,
                    'course'        => null,
                    'progress'      => 0,
                    'lessons_count' => 0,
                    'exams_passed'  => false,
                    'is_complete'   => false,
                ];
            }

            $progress    = $user->getCourseProgress($course);
            $passedExam  = $this->hasPassedAnExam($user, $course);

            return [
                'title'         => $title,
                'course'        => $course,
                'progress'      => round($progress),
                'lessons_count' => $course->lessons->count(),
                'exams_passed'  => $passedExam,
                'is_complete'   => $progress >= 100 || $passedExam,
            ];
        });
    }

    /**
     * Return approved/pending course certificates for a user, keyed by course_id.
     */
    public function manualCourseCertificatesFor(User $user): Collection
    {
        return Certificate::where('user_id', $user->id)
            ->whereNotNull('course_id')
            ->get()
            ->keyBy('course_id');
    }

    // ---------------------------------------------------------------

    private function hasPassedAnExam(User $user, Course $course): bool
    {
        foreach ($course->exams as $exam) {
            $passed = $user->examAttempts()
                ->where('exam_id', $exam->id)
                ->whereNotNull('completed_at')
                ->where('score', '>=', $exam->passing_score)
                ->exists();

            if ($passed) {
                return true;
            }
        }

        return false;
    }
}
