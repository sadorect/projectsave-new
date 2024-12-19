<?php

namespace App\Http\Controllers\LMS;

use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
public function index()
{
    $courses = Course::where('status', 'published')
        ->with('instructor')
        ->latest()
        ->paginate(9);
            
    return view('lms.courses.index', compact('courses'));
}

public function create()
{
    return view('lms.courses.create');
}

public function show(Course $course)
{
    $course->load(['instructor', 'lessons' => function($query) {
        $query->orderBy('order');
    }]);
        
    $isEnrolled = auth()->user()->courses()->where('course_id', $course->id)->exists();
        
    return view('lms.courses.show', compact('course', 'isEnrolled'));
}
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,archived'
        ]);

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('courses', 'public');
            $validated['featured_image'] = Storage::url($path);
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['instructor_id'] = auth()->id();

        $course = Course::create($validated);

        return redirect()->route('courses.show', $course)
                        ->with('success', 'Course created successfully');
    }


    public function edit(Course $course)
    {
        $this->authorize('update', $course);
        return view('lms.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,archived'
        ]);

        if ($request->hasFile('featured_image')) {
            if ($course->featured_image) {
                Storage::delete(str_replace('/storage/', 'public/', $course->featured_image));
            }
            $path = $request->file('featured_image')->store('courses', 'public');
            $validated['featured_image'] = Storage::url($path);
        }

        $validated['slug'] = Str::slug($validated['title']);
        
        $course->update($validated);

        return redirect()->route('courses.show', $course)
                        ->with('success', 'Course updated successfully');
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);

        if ($course->featured_image) {
            Storage::delete(str_replace('/storage/', 'public/', $course->featured_image));
        }

        $course->delete();

        return redirect()->route('courses.index')
                        ->with('success', 'Course deleted successfully');
    }

    public function enroll(Course $course)
    {
        auth()->user()->enrolledCourses()->attach($course->id);
        
        return redirect()->route('lessons.index', $course)
                        ->with('success', 'Successfully enrolled in the course');
    }
}
