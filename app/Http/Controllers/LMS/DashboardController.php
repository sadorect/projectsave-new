<?php

namespace App\Http\Controllers\LMS;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $enrolledCourses = auth()->user()
            ->courses()
            ->withPivot('status', 'enrolled_at', 'completed_at')
            ->orderBy('pivot_created_at', 'desc')
            ->get();

            $stats = [
                'total' => $enrolledCourses->count(),
                'completed' => $enrolledCourses->where('pivot.status', 'completed')->count(),
                'in_progress' => $enrolledCourses->where('pivot.status', 'active')->count(),
                'overall_progress' => $this->calculateOverallProgress($enrolledCourses)
            ];
        return view('lms.dashboard.index', compact('enrolledCourses', 'stats'));
    }

   private function calculateOverallProgress($courses){

   
        if($courses->isEmpty()) {
            return 0;
        }

    $totalProgress = $courses->sum(function ($courses) {
        
            return $courses->progress;
        });
    }

    public function enroll(Course $course)
    {
    if (!auth()->user()->courses()->where('course_id', $course->id)->exists()) {
        auth()->user()->courses()->attach($course->id, [
            'enrolled_at' => now(),
            'status' => 'active'
        ]);
    }
    
    return redirect()->back()
                    ->with('success', 'Successfully enrolled in course')->withFragment('courses-tab');
    }

public function unenroll(Course $course)
{
    auth()->user()->courses()->detach($course->id);
    
    // Delete associated lesson progress
    auth()->user()->lessonProgress()
        ->whereIn('lesson_id', $course->lessons->pluck('id'))
        ->delete();
    
    return redirect()->route('lms.dashboard')
        ->with('success', 'Successfully unenrolled from course');
}


}
