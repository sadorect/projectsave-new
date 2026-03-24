<?php

namespace App\Http\Controllers\Admin;

use Log;
use Exception;
use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\FileUploadService;
use App\Services\HtmlSanitizer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AdminCourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:viewAny,' . Course::class)->only('index');
        $this->middleware('can:create,' . Course::class)->only(['create', 'store']);
        $this->middleware('can:view,course')->only('show');
        $this->middleware('can:update,course')->only(['edit', 'update']);
        $this->middleware('can:delete,course')->only('destroy');
    }

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
        $path = FileUploadService::uploadImage(
            $request->file('featured_image'),
            'projectsave/lms/courses/images',
            's3',
            ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'featured_image'
        );
        $validated['featured_image'] = $path;
    }

    if ($request->hasFile('documents')) {
        $documents = [];
        foreach($request->file('documents') as $file) {
            $docPath = FileUploadService::uploadDocument(
                $file,
                'projectsave/lms/courses/documents',
                's3',
                ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
                'documents'
            );
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
    $validated['description'] = HtmlSanitizer::clean($validated['description']);
    $validated['objectives'] = HtmlSanitizer::clean($validated['objectives']);
    $validated['outcomes'] = HtmlSanitizer::clean($validated['outcomes']);
    $validated['evaluation'] = HtmlSanitizer::clean($validated['evaluation']);
    if (isset($validated['recommended_books'])) {
        $validated['recommended_books'] = HtmlSanitizer::clean($validated['recommended_books']);
    }

   
    
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
            $path = FileUploadService::uploadImage(
                $request->file('featured_image'),
                'projectsave/lms/courses/images',
                's3',
                ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'featured_image'
            );
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
                $docPath = FileUploadService::uploadDocument(
                    $file,
                    'projectsave/lms/courses/documents',
                    's3',
                    ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
                    'documents'
                );
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
        $validated['description'] = HtmlSanitizer::clean($validated['description']);
        $validated['objectives'] = HtmlSanitizer::clean($validated['objectives']);
        $validated['outcomes'] = HtmlSanitizer::clean($validated['outcomes']);
        $validated['evaluation'] = HtmlSanitizer::clean($validated['evaluation']);
        if (isset($validated['recommended_books'])) {
            $validated['recommended_books'] = HtmlSanitizer::clean($validated['recommended_books']);
        }
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

        $featuredImageUrl = $course->featured_image
            ? Storage::disk('s3')->temporaryUrl($course->featured_image, now()->addMinutes(10))
            : asset('frontend/img/course-placeholder.jpg');
        
        return view('admin.courses.show', compact('course', 'featuredImageUrl'));
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
