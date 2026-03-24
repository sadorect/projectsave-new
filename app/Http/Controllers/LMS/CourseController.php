<?php

namespace App\Http\Controllers\LMS;

use App\Models\Course;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Support\Lms\AsomPageSettings;
use App\Support\Lms\DiplomaProgramService;

class CourseController extends Controller
{
    public function index()
    {
        $courseQuery = $this->catalogQuery();
        $pageContent = AsomPageSettings::current();
        $catalogStats = $this->catalogStats();

        $courses = $courseQuery->latest()->paginate(9);
        $courses = $this->transformCatalogPaginator($courses);

        return view('lms.courses.index', [
            'courses' => $courses,
            'catalogStats' => $catalogStats,
            'isLanding' => false,
            'pageContent' => $pageContent,
        ]);
    }

    public function landing()
    {
        $courseQuery = $this->catalogQuery();
        $pageContent = AsomPageSettings::current();
        $featuredCourses = $this->transformCatalogCollection(
            (clone $courseQuery)->latest()->take(3)->get()
        );
        $catalogPreview = $this->transformCatalogCollection(
            (clone $courseQuery)->latest()->take(6)->get()
        );

        $catalogStats = $this->catalogStats();

        return view('lms.courses.landing', [
            'featuredCourses' => $featuredCourses,
            'catalogPreview' => $catalogPreview,
            'catalogStats' => $catalogStats,
            'pageContent' => $pageContent,
        ]);
    }

    public function create()
    {
        return view('lms.courses.create');
    }

    public function show(Course $course, DiplomaProgramService $diplomaProgram)
    {
        $course->load([
            'instructor',
            'lessons' => fn ($query) => $query->orderBy('order'),
            'exams' => fn ($query) => $query->where('is_active', true)->withCount('questions'),
        ]);

        $user = auth()->user();
        $isEnrolled = $user?->courses()->where('course_id', $course->id)->exists() ?? false;
        $completedLessons = $isEnrolled ? $user->lessonProgress()
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->where('completed', true)
            ->pluck('lesson_id') : collect();
        $progress = $isEnrolled ? round($user->getCourseProgress($course)) : 0;
        $nextLesson = $isEnrolled
            ? $course->lessons->first(fn ($lesson) => ! $completedLessons->contains($lesson->id)) ?? $course->lessons->first()
            : null;

        $courseSections = collect([
            ['title' => 'Course description', 'content' => $course->description],
            ['title' => 'Learning objectives', 'content' => $course->objectives],
            ['title' => 'Outcomes', 'content' => $course->outcomes],
            ['title' => 'Evaluation', 'content' => $course->evaluation],
            ['title' => 'Recommended books', 'content' => $course->recommended_books],
        ])->filter(fn (array $section) => filled($section['content']))->values();

        $courseMaterials = collect($course->documents ?? [])
            ->filter(fn ($document) => ! empty($document['name']) && ! empty($document['path']))
            ->values();

        $availableExams = $course->exams->filter(fn ($exam) => $exam->questions_count >= 5)->values();
        $diplomaStatus = $user ? $diplomaProgram->eligibility($user) : null;
        $diplomaCertificate = $diplomaStatus['certificate'] ?? null;
        $manualCourseCertificate = $user ? $diplomaProgram->manualCourseCertificatesFor($user)->get($course->id) : null;
        $courseLead = Str::limit(trim(strip_tags((string) ($course->description ?: ($courseSections->first()['content'] ?? '')))), 220);
        $courseHeroStats = collect([
            ['label' => 'Lessons', 'value' => $course->lessons->count()],
            ['label' => 'Exams', 'value' => $availableExams->count()],
            ['label' => 'Instructor', 'value' => $course->instructor?->name ?? 'ASOM Team'],
            ['label' => $isEnrolled ? 'Progress' : 'Enrollment', 'value' => $isEnrolled ? $progress . '%' : 'Open now'],
        ]);
        $courseHeroHighlights = $course->lessons
            ->take(3)
            ->map(fn ($lesson) => [
                'title' => $lesson->title,
                'meta' => $lesson->video_url ? 'Video lesson' : 'Reading lesson',
            ])
            ->values();

        return view('lms.courses.show', [
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'progress' => $progress,
            'completedLessons' => $completedLessons,
            'completedLessonCount' => $completedLessons->count(),
            'nextLesson' => $nextLesson,
            'courseSections' => $courseSections,
            'courseMaterials' => $courseMaterials,
            'availableExams' => $availableExams,
            'diplomaStatus' => $diplomaStatus,
            'diplomaCertificate' => $diplomaCertificate,
            'manualCourseCertificate' => $manualCourseCertificate,
            'courseLead' => $courseLead,
            'courseHeroStats' => $courseHeroStats,
            'courseHeroHighlights' => $courseHeroHighlights,
        ]);
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
            'lms/courses/images',
            null,
            ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'featured_image'
        );
        $validated['featured_image'] = Storage::url($path);
    }

    if ($request->hasFile('documents')) {
        $documents = [];
        foreach($request->file('documents') as $file) {
            $path = FileUploadService::uploadDocument(
                $file,
                'lms/courses/documents',
                null,
                ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
                'documents'
            );
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
            'status' => 'required|in:draft,published,archived',
        ]);

        if ($request->hasFile('featured_image')) {
            if ($course->featured_image) {
                $oldPath = str_replace(Storage::url(''), '', $course->featured_image);
                Storage::delete($oldPath);
            }

            $path = FileUploadService::uploadImage(
                $request->file('featured_image'),
                'lms/courses/images',
                null,
                ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'featured_image'
            );
            $validated['featured_image'] = Storage::url($path);
        }

        if ($request->hasFile('documents')) {
            if ($course->documents) {
                $oldDocs = json_decode($course->documents, true);
                foreach ($oldDocs as $doc) {
                    $oldPath = str_replace(Storage::url(''), '', $doc['path']);
                    Storage::delete($oldPath);
                }
            }

            $documents = [];
            foreach ($request->file('documents') as $file) {
                $path = FileUploadService::uploadDocument(
                    $file,
                    'lms/courses/documents',
                    null,
                    ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
                    'documents'
                );
                $documents[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => Storage::url($path),
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType(),
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

    private function catalogQuery()
    {
        return Course::query()
            ->where('status', 'published')
            ->with('instructor')
            ->withCount('lessons')
            ->withCount(['exams as active_exams_count' => fn ($query) => $query->where('is_active', true)]);
    }

    private function transformCatalogPaginator($courses)
    {
        $courses->setCollection(
            $this->transformCatalogCollection($courses->getCollection())
        );

        return $courses;
    }

    private function transformCatalogCollection($courses)
    {
        $user = auth()->user();
        $enrollments = collect();

        if ($user) {
            $enrollments = $user->courses()
                ->withPivot('status', 'enrolled_at', 'completed_at')
                ->whereIn('courses.id', collect($courses)->pluck('id'))
                ->get()
                ->keyBy('id');
        }

        return collect($courses)->map(function (Course $course) use ($enrollments, $user) {
            $enrollment = $enrollments->get($course->id);
            $progress = $user && $enrollment ? round($user->getCourseProgress($course)) : 0;

            return (object) [
                'id' => $course->id,
                'slug' => $course->slug,
                'title' => $course->title,
                'description_excerpt' => Str::limit(trim(strip_tags((string) $course->description)), 140),
                'featured_image_url' => $course->featured_image_url,
                'instructor_name' => $course->instructor?->name ?? 'ASOM Team',
                'lessons_count' => $course->lessons_count,
                'active_exams_count' => $course->active_exams_count,
                'is_enrolled' => $enrollment !== null,
                'progress' => $progress,
                'status' => $enrollment?->pivot?->status,
                'course_url' => route('lms.courses.show', $course->slug),
            ];
        });
    }

    private function catalogStats(): array
    {
        return Cache::remember('lms.catalog.stats.v1', now()->addMinutes(10), function (): array {
            return [
                'total_courses' => Course::query()->where('status', 'published')->count(),
                'total_lessons' => DB::table('lessons')
                    ->join('courses', 'courses.id', '=', 'lessons.course_id')
                    ->where('courses.status', 'published')
                    ->count(),
                'total_exams' => DB::table('exams')
                    ->join('courses', 'courses.id', '=', 'exams.course_id')
                    ->where('courses.status', 'published')
                    ->where('exams.is_active', true)
                    ->count(),
            ];
        });
    }
}
