<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Support\Lms\DiplomaProgramService;

class LessonProgressController extends Controller
{
    public function __construct(private DiplomaProgramService $diplomaProgram)
    {
    }

    public function markComplete(Course $course, Lesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            return response()->json(['success' => false, 'message' => 'Lesson does not belong to this course.'], 400);
        }

        $user = auth()->user();

        $user->lessonProgress()->updateOrCreate(
            [
                'lesson_id' => $lesson->id,
                'user_id' => $user->id,
            ],
            [
                'completed' => true,
                'completed_at' => now(),
            ]
        );

        $totalLessons = $course->lessons()->count();
        $completedLessons = $user->lessonProgress()
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->where('user_id', $user->id)
            ->where('completed', true)
            ->count();

        $progressPercentage = $totalLessons > 0 ? ($completedLessons / $totalLessons) * 100 : 0;

        if ($progressPercentage === 100.0) {
            $user->courses()->updateExistingPivot($course->id, [
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $this->diplomaProgram->ensurePendingCertificate(
                $user,
                'Program certificate generated after the learner completed all ASOM diploma lesson requirements.'
            );
        }

        $nextLesson = $course->lessons()
            ->where('order', '>', $lesson->order)
            ->orderBy('order')
            ->first();

        if (request()->expectsJson() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lesson marked as complete.',
                'newProgress' => round($progressPercentage),
                'nextLessonUrl' => $nextLesson
                    ? route('lms.lessons.show', [$course->slug, $nextLesson->slug])
                    : route('lms.dashboard'),
            ]);
        }

        return redirect()->back()
            ->with('success', 'Lesson marked as complete!')
            ->with('progressPercentage', $progressPercentage);
    }
}
