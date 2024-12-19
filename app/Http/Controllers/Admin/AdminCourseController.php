<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminCourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('instructor')
                        ->latest()
                        ->paginate(10);
                        
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,published,archived'
        ]);
    
        if ($request->hasFile('featured_image')) {
          $path = $request->file('featured_image')->store('courses', 'public');
          $validated['featured_image'] = $path;
      }
      
    
        $validated['slug'] = Str::slug($validated['title']);
        $validated['instructor_id'] = auth()->id();
    
        Course::create($validated);
    
        return redirect()->route('admin.courses.index')
                        ->with('success', 'Course created successfully');
    }
    

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,published,archived'
        ]);
    
        if ($request->hasFile('featured_image')) {
          // Delete old image if exists
          if ($course->featured_image) {
              Storage::disk('public')->delete($course->featured_image);
          }
          
          $path = $request->file('featured_image')->store('courses', 'public');
          $validated['featured_image'] = $path;
      }
      
    
        $validated['slug'] = Str::slug($validated['title']);
        
        $course->update($validated);
    
        return redirect()->route('admin.courses.index')
                        ->with('success', 'Course updated successfully');
    }
    
    public function show(Course $course)
    {
        $course->load(['instructor', 'lessons' => function($query) {
            $query->orderBy('order');
        }]);
        
        return view('admin.courses.show', compact('course'));
    }


    public function destroy(Course $course)
    {
        if ($course->featured_image) {
            Storage::delete(str_replace('/storage/', 'public/', $course->featured_image));
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Course deleted successfully');
    }
}
