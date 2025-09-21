<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Certificate;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;

class LessonProgressController extends Controller
{
    public function markComplete(Course $course, Lesson $lesson)
    {
        // Ensure the lesson belongs to the course
        if ($lesson->course_id !== $course->id) {
            return response()->json(['success' => false, 'message' => 'Lesson does not belong to this course.'], 400);
        }

        $user = auth()->user();

        // Mark lesson as complete for the user
         $user->lessonProgress()->updateOrCreate(
            [
                'lesson_id' => $lesson->id,
                'user_id' => $user->id
            ],
            [
                'completed' => true,
                'completed_at' => now()
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
                'completed_at' => now()
            ]);
            
            // Auto-generate certificate if course is completed and no certificate exists
            $existingCertificate = Certificate::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();
                
            if (!$existingCertificate) {
                // Get final grade from exams if available
                $finalGrade = null;
                $examAttempts = ExamAttempt::where('user_id', $user->id)
                    ->whereHas('exam', function($query) use ($course) {
                        $query->where('course_id', $course->id);
                    })
                    ->whereNotNull('completed_at')
                    ->get();

                if ($examAttempts->isNotEmpty()) {
                    $finalGrade = $examAttempts->avg('score');
                }

                Certificate::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'final_grade' => $finalGrade,
                    'issued_at' => now(),
                    'completed_at' => now(),
                    'is_approved' => false, // Requires admin approval
                ]);
            }
        }

        return redirect()->back()
            ->with('success', 'Lesson marked as complete!')
            ->with('progressPercentage', $progressPercentage);
        // Alternatively, you can return a JSON response
       /* return response()->json([
            'success' => true,
            'progress' => $progressPercentage
        ]);
        */
    }
}
