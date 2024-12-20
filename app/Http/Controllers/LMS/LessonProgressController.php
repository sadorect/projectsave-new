<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonProgressController extends Controller
{
    public function markComplete(Course $course, Lesson $lesson)
    {
        $progress = auth()->user()->lessonProgress()->updateOrCreate(
            ['lesson_id' => $lesson->id],
            [
                'completed' => true,
                'completed_at' => now()
            ]
        );

        $totalLessons = $course->lessons()->count();
        $completedLessons = auth()->user()->lessonProgress()
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->where('completed', true)
            ->count();

        $progressPercentage = ($completedLessons / $totalLessons) * 100;

        if ($progressPercentage === 100) {
            auth()->user()->courses()->updateExistingPivot($course->id, [
                'status' => 'completed',
                'completed_at' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'progress' => $progressPercentage
        ]);
    }
}
