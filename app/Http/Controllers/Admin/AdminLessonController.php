<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminLessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with('course')
            ->orderBy('course_id')
            ->orderBy('order')
            ->paginate(10);
            
        return view('admin.lessons.index', compact('lessons'));
    }

    public function create()
    {
        $courses = Course::pluck('title', 'id');
        return view('admin.lessons.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'video_url' => 'nullable|url',
            'order' => 'required|integer|min:1',
            'course_id' => 'required|exists:courses,id'
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        
        Lesson::create($validated);

        return redirect()->route('admin.lessons.index')
                        ->with('success', 'Lesson created successfully');
    }

    public function edit(Lesson $lesson)
    {
        $courses = Course::pluck('title', 'id');
        return view('admin.lessons.edit', compact('lesson', 'courses'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'video_url' => 'nullable|url',
            'order' => 'required|integer|min:1',
            'course_id' => 'required|exists:courses,id'
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        
        $lesson->update($validated);

        return redirect()->route('admin.lessons.index')
                        ->with('success', 'Lesson updated successfully');
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        
        return redirect()->route('admin.lessons.index')
                        ->with('success', 'Lesson deleted successfully');
    }
}
