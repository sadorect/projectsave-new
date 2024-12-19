<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LessonController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    public function index(Course $course)
    {
        $lessons = $course->lessons()->orderBy('order')->paginate(20);
        return view('lms.lessons.index', compact('course', 'lessons'));
    }

    public function show(Course $course, Lesson $lesson)
    {
        return view('lms.lessons.show', compact('course', 'lesson'));
    }

    public function create(Course $course)
    {
        return view('lms.lessons.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'video' => 'required|file|mimes:mp4,mov,ogg,webm|max:512000',
            'order' => 'required|integer|min:1'
        ]);

        try {
            $videoData = $this->videoService->handleVideo(
                $request->video,
                $course->id,
                null
            );

            $lesson = Lesson::create([
                'course_id' => $course->id,
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']),
                'content' => $validated['content'],
                'order' => $validated['order'],
                'video_url' => $videoData['video_url'],
                'video_type' => $videoData['video_type']
            ]);

            return redirect()->route('lessons.show', [$course, $lesson])
                           ->with('success', 'Lesson created successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit(Course $course, Lesson $lesson)
    {
        return view('lms.lessons.edit', compact('course', 'lesson'));
    }

    public function update(Request $request, Course $course, Lesson $lesson)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'video' => 'nullable|file|mimes:mp4,mov,ogg,webm|max:512000',
            'order' => 'required|integer|min:1'
        ]);

        try {
            $updateData = [
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']),
                'content' => $validated['content'],
                'order' => $validated['order']
            ];

            if ($request->hasFile('video')) {
                $videoData = $this->videoService->handleVideo(
                    $request->video,
                    $course->id,
                    $lesson->id
                );
                $updateData['video_url'] = $videoData['video_url'];
                $updateData['video_type'] = $videoData['video_type'];
            }

            $lesson->update($updateData);

            return redirect()->route('lessons.show', [$course, $lesson])
                           ->with('success', 'Lesson updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Course $course, Lesson $lesson)
    {
        $lesson->delete();
        return redirect()->route('lessons.index', $course)
                        ->with('success', 'Lesson deleted successfully');
    }
}
