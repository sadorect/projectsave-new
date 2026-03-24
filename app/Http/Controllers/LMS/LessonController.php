<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Support\Lms\DiplomaProgramService;

class LessonController extends Controller
{
    protected $videoService;
    protected $diplomaProgram;

    public function __construct(VideoService $videoService, DiplomaProgramService $diplomaProgram)
    {
        $this->videoService = $videoService;
        $this->diplomaProgram = $diplomaProgram;
    }

    public function index(Course $course)
    {
        if (! $this->studentIsEnrolled($course)) {
            return redirect()->route('lms.courses.show', $course->slug)
                ->with('error', 'Enroll in the course to access the lesson outline.');
        }

        $course->load([
            'instructor',
            'lessons' => fn ($query) => $query->orderBy('order'),
            'exams' => fn ($query) => $query->where('is_active', true)->withCount('questions'),
        ]);

        $completedLessonIds = auth()->user()->lessonProgress()
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->where('completed', true)
            ->pluck('lesson_id');

        $courseProgress = round(auth()->user()->getCourseProgress($course));
        $nextLesson = $course->lessons->first(fn ($lesson) => ! $completedLessonIds->contains($lesson->id)) ?? $course->lessons->first();
        $availableExams = $course->exams->filter(fn ($exam) => $exam->questions_count >= 5)->values();
        $diplomaStatus = $this->diplomaProgram->eligibility(auth()->user());
        $diplomaCertificate = $diplomaStatus['certificate'];
        $manualCourseCertificate = $this->diplomaProgram->manualCourseCertificatesFor(auth()->user())->get($course->id);

        $lessonCards = $course->lessons->map(function (Lesson $lesson) use ($completedLessonIds, $course) {
            $isCompleted = $completedLessonIds->contains($lesson->id);

            return [
                'model' => $lesson,
                'title' => $lesson->title,
                'excerpt' => Str::limit(trim(strip_tags((string) $lesson->content)), 140),
                'is_completed' => $isCompleted,
                'type_label' => $lesson->video_url ? 'Video lesson' : 'Reading lesson',
                'url' => route('lms.lessons.show', [$course->slug, $lesson->slug]),
            ];
        })->values();

        return view('lms.lessons.index', compact(
            'course',
            'courseProgress',
            'completedLessonIds',
            'nextLesson',
            'availableExams',
            'diplomaStatus',
            'diplomaCertificate',
            'manualCourseCertificate',
            'lessonCards'
        ));
    }

    public function show(Course $course, Lesson $lesson)
    {
        if (! $this->studentIsEnrolled($course)) {
            return redirect()->route('lms.courses.show', $course->slug)
                ->with('error', 'Enroll in the course to access the lesson player.');
        }

        $course->load([
            'instructor',
            'lessons' => fn ($query) => $query->orderBy('order'),
            'exams' => fn ($query) => $query->where('is_active', true)->withCount('questions'),
        ]);

        abort_unless($lesson->course_id === $course->id, 404);

        $completedLessonIds = auth()->user()->lessonProgress()
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->where('completed', true)
            ->pluck('lesson_id');

        $courseProgress = round(auth()->user()->getCourseProgress($course));
        $currentIndex = $course->lessons->search(fn (Lesson $courseLesson) => $courseLesson->id === $lesson->id);
        $previousLesson = $currentIndex !== false && $currentIndex > 0 ? $course->lessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex !== false && $currentIndex < ($course->lessons->count() - 1)
            ? $course->lessons[$currentIndex + 1]
            : null;
        $availableExams = $course->exams->filter(fn ($exam) => $exam->questions_count >= 5)->values();
        $diplomaStatus = $this->diplomaProgram->eligibility(auth()->user());
        $diplomaCertificate = $diplomaStatus['certificate'];
        $manualCourseCertificate = $this->diplomaProgram->manualCourseCertificatesFor(auth()->user())->get($course->id);
        $lessonCompleted = $completedLessonIds->contains($lesson->id);

        $courseOutline = $course->lessons->map(function (Lesson $courseLesson) use ($completedLessonIds, $course, $lesson) {
            return [
                'model' => $courseLesson,
                'is_current' => $courseLesson->id === $lesson->id,
                'is_completed' => $completedLessonIds->contains($courseLesson->id),
                'url' => route('lms.lessons.show', [$course->slug, $courseLesson->slug]),
                'type_label' => $courseLesson->video_url ? 'Video lesson' : 'Reading lesson',
            ];
        })->values();

        return view('lms.lessons.show', compact(
            'course',
            'lesson',
            'nextLesson',
            'previousLesson',
            'courseProgress',
            'completedLessonIds',
            'availableExams',
            'diplomaStatus',
            'diplomaCertificate',
            'manualCourseCertificate',
            'lessonCompleted',
            'courseOutline'
        ));
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
            'video' => 'required|file|mimes:mp4,mov,ogg,webm,webp|max:512000',
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
            'video' => 'nullable|file|mimes:mp4,mov,ogg,webm,webp|max:512000',
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

    private function studentIsEnrolled(Course $course): bool
    {
        return auth()->user()
            ->courses()
            ->where('course_id', $course->id)
            ->exists();
    }
}
