<x-layouts.asom-auth
    :page-title="$lesson->title"
    :subtitle="'Lesson ' . ($lesson->order ?? 1) . ' of ' . $course->lessons->count() . ' • ' . round($courseProgress) . '% course progress'"
>
    <nav class="breadcrumb-nav mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('lms.dashboard') }}">Student workspace</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.lessons.index', $course->slug) }}">{{ $course->title }}</a></li>
            <li class="breadcrumb-item active">{{ $lesson->title }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-xl-8">
            <article class="course-detail-card">
                <div class="resource-content">
                    @if($lesson->video_url)
                        <div class="alert alert-info d-flex align-items-start gap-2 mb-4" role="alert">
                            <i class="fas fa-info-circle mt-1"></i>
                            <span>Watch the lesson carefully. Your progress updates here as you move through the course.</span>
                        </div>

                        <div class="mb-4">
                            @livewire('lesson-video-player', [
                                'lessonId' => $lesson->id,
                                'courseId' => $course->id,
                                'thumbnailUrl' => $lesson->thumbnail_url,
                            ])
                        </div>
                    @endif

                    <div class="lesson-content">
                        {!! $lesson->content !!}
                    </div>
                </div>
            </article>

            <div class="progress-actions mt-4">
                <div>
                    @if(!$lessonCompleted)
                        <form method="POST" action="{{ route('lessons.complete', [$course->slug, $lesson->slug]) }}" class="completion-form">
                            @csrf
                            <button type="submit" class="mark-complete-btn">
                                <span class="btn-text">
                                    <i class="fas fa-check-circle"></i>
                                    Mark as complete
                                </span>
                                <span class="loading-spinner d-none"></span>
                            </button>
                        </form>
                    @else
                        <button class="mark-complete-btn completed" disabled>
                            <i class="fas fa-check-circle"></i>
                            Completed
                        </button>
                    @endif
                </div>

                <div class="d-flex flex-wrap gap-2">
                    @if($previousLesson)
                        <a href="{{ route('lms.lessons.show', [$course->slug, $previousLesson->slug]) }}" class="surface-button-secondary">Previous lesson</a>
                    @endif

                    @if($nextLesson)
                        <a href="{{ route('lms.lessons.show', [$course->slug, $nextLesson->slug]) }}" class="btn-next-lesson">
                            Next lesson
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    @else
                        <a href="{{ route('lms.dashboard') }}" class="btn-next-lesson">
                            Return to workspace
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="d-grid gap-4">
                <aside class="progress-indicator">
                    <h3 class="h5 mb-3">Course progress</h3>
                    <div class="lms-progress-bar mb-3">
                        <div class="lms-progress-fill progress-bar" style="width: {{ $courseProgress }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">{{ $completedLessonIds->count() }} of {{ $course->lessons->count() }} completed</small>
                        <small class="fw-semibold progress-text">{{ round($courseProgress) }}%</small>
                    </div>
                </aside>

                <aside class="lesson-navigation">
                    <div class="p-3 border-bottom">
                        <h3 class="h5 mb-0">Course outline</h3>
                    </div>
                    <div>
                        @foreach($courseOutline as $outlineLesson)
                            <a href="{{ $outlineLesson['url'] }}" class="lesson-nav-item {{ $outlineLesson['is_current'] ? 'current' : '' }} {{ $outlineLesson['is_completed'] ? 'completed' : '' }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="lesson-number {{ $outlineLesson['is_completed'] ? 'completed' : '' }}">
                                        @if($outlineLesson['is_completed'])
                                            <i class="fas fa-check"></i>
                                        @else
                                            {{ $outlineLesson['model']->order }}
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ $outlineLesson['model']->title }}</div>
                                        <small class="text-muted">{{ $outlineLesson['type_label'] }}</small>
                                    </div>
                                    @if($outlineLesson['is_current'])
                                        <i class="fas fa-play-circle text-brand-600"></i>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </aside>

                <aside class="sidebar-card">
                    <h3 class="h5 mb-3">After this lesson</h3>
                    <div class="d-grid gap-3">
                        <div>
                            <small class="text-muted d-block">Next milestone</small>
                            <strong>
                                @if($nextLesson)
                                    {{ $nextLesson->title }}
                                @elseif($availableExams->isNotEmpty())
                                    Exam now available
                                @elseif($diplomaCertificate)
                                    {{ $diplomaCertificate->is_approved ? 'Program certificate approved' : 'Program certificate review in progress' }}
                                @elseif($manualCourseCertificate)
                                    Course recognition available
                                @else
                                    Continue remaining diploma modules
                                @endif
                            </strong>
                        </div>

                        @if($availableExams->isNotEmpty() && $courseProgress >= 100)
                            <a href="{{ route('lms.exams.index') }}" class="surface-button-secondary justify-content-center">Open exams</a>
                        @endif

                        @if($diplomaCertificate)
                            <a href="{{ route('lms.certificates.show', $diplomaCertificate) }}" class="surface-button-secondary justify-content-center">Open program certificate</a>
                        @endif

                        <a href="{{ route('lms.lessons.index', $course->slug) }}" class="surface-button-secondary justify-content-center">Back to lesson list</a>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    @include('lms.lessons._completion_modal')

    @push('scripts')
        @if (! app()->runningUnitTests())
            @vite('resources/js/lms-progress.js')
        @endif
    @endpush
</x-layouts.asom-auth>
