<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function markComplete(Lesson $lesson)
    {
        auth()->user()->markLessonAsCompleted($lesson);
        
        return response()->json([
            'message' => 'Lesson marked as completed',
            'progress' => auth()->user()->getCourseProgress($lesson->course)
        ]);
    }
}
