<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(Course $course)
    {
        auth()->user()->courses()->attach($course->id, [
            'status' => 'active',
            'enrolled_at' => now()
        ]);

        return redirect()->route('lessons.index', $course)
                        ->with('success', 'Successfully enrolled in the course');
    }


public function destroy(Course $course)
{
    auth()->user()->courses()->detach($course->id);
    
    return redirect()->route('courses.show', $course)
                    ->with('success', 'Successfully unenrolled from the course');
}


}