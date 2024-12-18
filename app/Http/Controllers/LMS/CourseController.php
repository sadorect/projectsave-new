<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('status', 'published')
                        ->latest()
                        ->paginate(12);
                        
        return view('lms.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        return view('lms.courses.show', compact('course'));
    }

    public function myCourses()
    {
        $enrolledCourses = auth()->user()->courses()->paginate(12);
        return view('lms.courses.enrolled', compact('enrolledCourses'));
    }
}
