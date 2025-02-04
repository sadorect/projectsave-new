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

public function landing()
{
    $courses = Course::where('status', 'published')
        ->with('instructor')
        ->latest()
        ->paginate(9);
            
    return view('lms.courses.landing', compact('courses'));
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
        
    $isEnrolled = auth()->check() ? auth()->user()->courses()->where('course_id', $course->id)->exists() : false;        
    $courseDetails = [
        'objectives' => $course->objectives,
        'outcomes' => $course->outcomes,
        'evaluation' => $course->evaluation,
        'recommended_books' => $course->recommended_books,
        'documents' => $course->documents
    ];

    return view('lms.courses.show', compact('course', 'isEnrolled', 'courseDetails'));
}
public function store(Request $request)
{dd($request->all());
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required',
        'objectives' => 'required',
        'outcomes' => 'required',
        'evaluation' => 'required',
        'recommended_books' => 'nullable',
        'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'documents.*' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        'status' => 'required|in:draft,published,archived'
    ]);

    if ($request->hasFile('featured_image')) {
        $path = $request->file('featured_image')->store('courses', 'public');
        $validated['featured_image'] = Storage::url($path);
    }

    if ($request->hasFile('documents')) {
        $documents = [];
        foreach($request->file('documents') as $file) {
            $path = $file->store('course-documents', 'public');
            $documents[] = [
                'name' => $file->getClientOriginalName(),
                'path' => Storage::url($path),
                'size' => $file->getSize(),
                'type' => $file->getClientMimeType()
            ];
        }
        $validated['documents'] = json_encode($documents);
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
        'objectives' => 'required',
        'outcomes' => 'required',
        'evaluation' => 'required',
        'recommended_books' => 'nullable',
        'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'documents.*' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        'status' => 'required|in:draft,published,archived'
    ]);

    if ($request->hasFile('featured_image')) {
        if ($course->featured_image) {
            Storage::delete(str_replace('/storage/', 'public/', $course->featured_image));
        }
        $path = $request->file('featured_image')->store('courses', 'public');
        $validated['featured_image'] = Storage::url($path);
    }

    if ($request->hasFile('documents')) {
        if ($course->documents) {
            $oldDocs = json_decode($course->documents, true);
            foreach($oldDocs as $doc) {
                Storage::delete(str_replace('/storage/', 'public/', $doc['path']));
            }
        }

        $documents = [];
        foreach($request->file('documents') as $file) {
            $path = $file->store('course-documents', 'public');
            $documents[] = [
                'name' => $file->getClientOriginalName(),
                'path' => Storage::url($path),
                'size' => $file->getSize(),
                'type' => $file->getClientMimeType()
            ];
        }
        $validated['documents'] = json_encode($documents);
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
