<?php

namespace App\Http\Controllers\Admin;

use Log;
use Exception;
use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        'objectives' => 'required',
        'outcomes' => 'required',
        'evaluation' => 'required',
        'recommended_books' => 'nullable',
        'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'documents.*' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        'status' => 'required|in:draft,published,archived'
    ]);

    if ($request->hasFile('featured_image')) {
         $path = $request->file('featured_image')->store('projectsave/lms/courses/images', [
            'disk' => 's3',
            'visibility' => 'private'
        ]);
        $validated['featured_image'] = $path;
    }

    if ($request->hasFile('documents')) {
        $documents = [];
        foreach($request->file('documents') as $file) {
            $docPath = $file->store('projectsave/lms/courses/documents', [
                'disk' => 's3',
                'visibility' => 'private'
            ]);
            $documents[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $docPath,
                'size' => $file->getSize(),
                'type' => $file->getClientMimeType()
            ];
        }
        $validated['documents'] = json_encode($documents);
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

     try {
        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            if ($course->featured_image) {
                Storage::disk('s3')->delete($course->featured_image);
            }
            $path = $request->file('featured_image')->store('projectsave/lms/courses/images', [
                'disk' => 's3',
                'visibility' => 'private'
            ]);
            $validated['featured_image'] = $path;
        }

        // Handle documents upload
        if ($request->hasFile('documents')) {
            if ($course->documents) {
                $oldDocs = json_decode($course->documents, true);
                foreach ($oldDocs as $doc) {
                    Storage::disk('s3')->delete($doc['path']);
                }
            }

            $documents = [];
            foreach ($request->file('documents') as $file) {
                $docPath = $file->store('projectsave/lms/courses/documents', [
                    'disk' => 's3',
                    'visibility' => 'private'
                ]);
                $documents[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $docPath,
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType()
                ];
            }
            $validated['documents'] = json_encode($documents);
        }

        $validated['slug'] = Str::slug($validated['title']);
        $course->update($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Course update failed: ' . $e->getMessage()]);
    }
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
