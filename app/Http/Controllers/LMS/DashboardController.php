<?php

namespace App\Http\Controllers\LMS;

use App\Models\Course;
use App\Http\Controllers\Controller;
use App\Support\Lms\StudentWorkspaceBuilder;

class DashboardController extends Controller
{
    public function index(StudentWorkspaceBuilder $workspaceBuilder)
    {
        return view('lms.dashboard.index', $workspaceBuilder->build(auth()->user()));
    }

    public function enroll(Course $course)
    {
        if (! auth()->user()->courses()->where('course_id', $course->id)->exists()) {
            auth()->user()->courses()->attach($course->id, [
                'enrolled_at' => now(),
                'status' => 'active',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Successfully enrolled in course')
            ->withFragment('courses-tab');
    }

    public function unenroll(Course $course)
    {
        auth()->user()->courses()->detach($course->id);

        auth()->user()->lessonProgress()
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->delete();

        return redirect()->route('lms.dashboard')
            ->with('success', 'Successfully unenrolled from course');
    }
}
