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
                'in_progress' => $enrolledCourses->where('pivot.status', 'active')->count()
            ];
        return view('lms.dashboard.index', compact('enrolledCourses', 'stats'));
    }

    public function enroll(Course $course)
{
    if (!auth()->user()->courses()->where('course_id', $course->id)->exists()) {
        auth()->user()->courses()->attach($course->id, [
            'enrolled_at' => now(),
            'status' => 'active'
        ]);
    }
    
    return redirect()->route('courses.show', $course->slug)
                    ->with('success', 'Successfully enrolled in course');
}

public function unenroll(Course $course)
{
    auth()->user()->courses()->detach($course->id);
    
    return redirect()->route('courses.index')
                    ->with('success', 'Successfully unenrolled from course');
}

}
