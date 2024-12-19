<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class AdminEnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = Course::with(['instructor', 'users'])
            ->whereHas('users')
            ->paginate(10);
            
        return view('admin.enrollments.index', compact('enrollments'));
    }

    public function create()
    {
        $courses = Course::pluck('title', 'id');
        $users = User::pluck('name', 'id');
        
        return view('admin.enrollments.create', compact('courses', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id'
        ]);

        $user = User::find($validated['user_id']);
        $course = Course::find($validated['course_id']);

        $user->courses()->attach($course->id, [
            'enrolled_at' => now(),
            'status' => 'active'
        ]);

        return redirect()->route('admin.enrollments.index')
                        ->with('success', 'Student enrolled successfully');
    }

    public function destroy(Course $course, User $user)
    {
        $user->courses()->detach($course->id);
        return redirect()->route('admin.enrollments.index')
                        ->with('success', 'Enrollment removed successfully');
    }

    public function updateStatus(Course $course, User $user, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,suspended,completed'
        ]);

        $user->courses()->updateExistingPivot($course->id, [
            'status' => $validated['status']
        ]);

        return redirect()->route('admin.enrollments.index')
                        ->with('success', 'Enrollment status updated');
    }

    public function show(Course $course, User $user)
    {
        $enrollment = $user->courses()->where('course_id', $course->id)->first();
        return view('admin.enrollments.show', compact('enrollment'));
    }

}
