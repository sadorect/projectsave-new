<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Notifications\CourseCompletionNotification;
use App\Notifications\ExamAvailableNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProgressController extends Controller
{
    public function markComplete(Lesson $lesson)
    {
        $user = auth()->user();
        $course = $lesson->course;
        
        // Get progress before marking complete
        $progressBefore = $user->getCourseProgress($course);
        
        // Mark lesson as completed
        $user->markLessonAsCompleted($lesson);
        
        // Get progress after marking complete
        $progressAfter = $user->getCourseProgress($course);
        
        // Check if course was just completed (reached 100%)
        $justCompleted = $progressBefore < 100 && $progressAfter >= 100;
        
        if ($justCompleted) {
            try {
                // Send course completion notification
                $user->notify(new CourseCompletionNotification($course));
                
                // Check for available exams and notify
                $availableExams = $course->exams()
                    ->where('is_active', true)
                    ->whereHas('questions', function($query) {
                        $query->havingRaw('COUNT(*) >= 5');
                    })
                    ->get();
                
                if ($availableExams->count() > 0) {
                    $user->notify(new ExamAvailableNotification($availableExams, $course));
                }
                
                // Update course completion status in pivot table
                $user->courses()->updateExistingPivot($course->id, [
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to send course completion notifications', [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return response()->json([
            'message' => 'Lesson marked as completed',
            'progress' => $progressAfter,
            'course_completed' => $justCompleted,
            'exams_unlocked' => $justCompleted ? $course->exams()->where('is_active', true)->count() : 0
        ]);
    }
}
