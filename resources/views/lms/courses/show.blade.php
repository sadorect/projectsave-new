<x-layouts.lms
    :title="$course->title . ' | ASOM'"
    :show-sidebar="false"
    :flush-top="true"
>
    <div class="d-grid gap-4">
        <section class="lms-course-hero-section">
            <div class="lms-course-hero-shell" style="--lms-course-hero-image: url('{{ $course->featured_image_url }}');">
                <div class="lms-hero-frame">
                    <div class="lms-course-hero">
                        <div class="row g-4 align-items-end">
                            <div class="col-xl-7">
                                <div class="lms-course-hero-copy">
                                    <span class="surface-eyebrow border-0 bg-white/10 text-white">Course Detail</span>
                                    <h1>{{ $course->title }}</h1>
                                    <p class="lead mb-0 text-white-50">{{ $courseLead }}</p>

                                    <div class="lms-dashboard-actions mt-4">
                                        @if($isEnrolled && $nextLesson)
                                            <a href="{{ route('lms.lessons.show', [$course->slug, $nextLesson->slug]) }}" class="btn btn-light rounded-pill px-4">
                                                {{ $progress > 0 ? 'Continue learning' : 'Start course' }}
                                            </a>
                                            <a href="{{ route('lms.lessons.index', $course->slug) }}" class="surface-button-ghost text-white">Open lesson outline</a>
                                        @elseif(auth()->check())
                                            <form action="{{ route('lms.courses.enroll', $course->slug) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-light rounded-pill px-4">Enroll now</button>
                                            </form>
                                            <a href="{{ route('lms.dashboard') }}" class="surface-button-ghost text-white">Open my workspace</a>
                                        @else
                                            <a href="{{ route('asom.register') }}" class="btn btn-light rounded-pill px-4">Register to enroll</a>
                                            <a href="{{ route('login') }}" class="surface-button-ghost text-white">Sign in</a>
                                        @endif
                                    </div>

                                    <div class="lms-course-hero-metrics mt-4">
                                        @foreach($courseHeroStats as $stat)
                                            <article class="lms-course-hero-metric">
                                                <span class="label">{{ $stat['label'] }}</span>
                                                <span class="value">{{ $stat['value'] }}</span>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-5">
                                <div class="lms-course-hero-panel">
                                    <span class="surface-eyebrow border-0 bg-white/10 text-white">Inside this learning track</span>
                                    <div class="d-grid gap-3 mt-3">
                                        @forelse($courseHeroHighlights as $highlight)
                                            <div class="lms-course-hero-highlight">
                                                <strong>{{ $highlight['title'] }}</strong>
                                                <span>{{ $highlight['meta'] }}</span>
                                            </div>
                                        @empty
                                            <div class="lms-course-hero-highlight">
                                                <strong>Lesson outline coming soon</strong>
                                                <span>The first teaching checkpoints will appear here as soon as lessons are published for this course.</span>
                                            </div>
                                        @endforelse
                                    </div>

                                    <div class="lms-course-hero-footnote">
                                        @if($availableExams->isNotEmpty())
                                            <strong>{{ $availableExams->count() }} assessment {{ $availableExams->count() === 1 ? 'path' : 'paths' }} available</strong>
                                            <span>Students complete lessons first, then move into qualifying exams as they finish the course flow.</span>
                                        @else
                                            <strong>Lesson-led progression</strong>
                                            <span>This course currently emphasizes guided lessons and course completion before any later assessment expansion.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-xl-8">
                <article class="course-detail-card">
                    @if($courseSections->isNotEmpty())
                        <div class="course-tabs">
                            <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                                @foreach($courseSections as $section)
                                    <li class="nav-item" role="presentation">
                                        <button
                                            class="nav-link {{ $loop->first ? 'active' : '' }}"
                                            data-bs-toggle="tab"
                                            data-bs-target="#course-section-{{ $loop->index }}"
                                            type="button"
                                            role="tab"
                                        >
                                            {{ $section['title'] }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content course-tab-content">
                                @foreach($courseSections as $section)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="course-section-{{ $loop->index }}" role="tabpanel">
                                        <div class="course-content">
                                            {!! $section['content'] !!}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($isEnrolled && $courseMaterials->isNotEmpty())
                        <div class="resource-content pt-0">
                            <div class="resource-card">
                                <div class="resource-header">
                                    <h4 class="mb-0">Course materials</h4>
                                </div>
                                <div class="resource-content">
                                    <div class="row g-3">
                                        @foreach($courseMaterials as $material)
                                            <div class="col-md-6">
                                                <div class="document-card">
                                                    <i class="fas fa-file-alt document-icon"></i>
                                                    <div>
                                                        <h5 class="h6 mb-1">{{ $material['name'] }}</h5>
                                                        @if(! empty($material['size']))
                                                            <small class="text-muted d-block mb-2">{{ number_format($material['size'] / 1048576, 2) }} MB</small>
                                                        @endif
                                                        <a href="{{ $material['path'] }}" class="surface-button-secondary" download>Download</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </article>
            </div>

            <div class="col-xl-4">
                <div class="d-grid gap-4">
                    <aside class="sidebar-card">
                        <h3 class="h5 mb-3">Course snapshot</h3>
                        <div class="course-stats">
                            <div class="stat-item d-flex justify-content-between align-items-center">
                                <span>Total lessons</span>
                                <strong>{{ $course->lessons->count() }}</strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between align-items-center">
                                <span>Available exams</span>
                                <strong>{{ $availableExams->count() }}</strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between align-items-center">
                                <span>Instructor</span>
                                <strong>{{ $course->instructor?->name ?? 'ASOM Team' }}</strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between align-items-center">
                                <span>Status</span>
                                <strong>{{ $isEnrolled ? 'Enrolled' : 'Open for enrollment' }}</strong>
                            </div>
                        </div>
                    </aside>

                    <aside class="sidebar-card">
                        <h3 class="h5 mb-3">Student action</h3>

                        @if($isEnrolled)
                            <div class="d-grid gap-3">
                                <div>
                                    <div class="d-flex justify-content-between small text-muted mb-2">
                                        <span>Progress</span>
                                        <span>{{ $progress }}%</span>
                                    </div>
                                    <div class="lms-course-progress">
                                        <div class="lms-course-progress-fill" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>

                                @if($nextLesson)
                                    <a href="{{ route('lms.lessons.show', [$course->slug, $nextLesson->slug]) }}" class="surface-button-primary justify-content-center">
                                        {{ $progress > 0 ? 'Continue learning' : 'Start course' }}
                                    </a>
                                @endif

                                <a href="{{ route('lms.lessons.index', $course->slug) }}" class="surface-button-secondary justify-content-center">Open lesson outline</a>

                                @if($availableExams->isNotEmpty() && $progress >= 100)
                                    <a href="{{ route('lms.exams.index') }}" class="surface-button-secondary justify-content-center">Go to exams</a>
                                @endif

                                @if($diplomaCertificate)
                                    <a href="{{ route('lms.certificates.show', $diplomaCertificate) }}" class="surface-button-secondary justify-content-center">View program certificate</a>
                                @elseif($diplomaStatus)
                                    <div class="rounded-4 border p-3 bg-light-subtle">
                                        <small class="text-muted d-block mb-1">Diploma in Ministry progress</small>
                                        <strong>{{ $diplomaStatus['completed_requirements'] }} of {{ $diplomaStatus['required_count'] }} requirements complete</strong>
                                    </div>
                                @endif

                                @if($manualCourseCertificate)
                                    <a href="{{ route('lms.certificates.show', $manualCourseCertificate) }}" class="surface-button-ghost justify-content-center">View course recognition</a>
                                @endif

                                <form action="{{ route('lms.courses.unenroll', $course->slug) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100 rounded-pill" onclick="return confirm('Are you sure you want to unenroll from this course?')">
                                        Unenroll
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="d-grid gap-3">
                                @auth
                                    <form action="{{ route('lms.courses.enroll', $course->slug) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="surface-button-primary w-100 justify-content-center">Enroll now</button>
                                    </form>
                                    <a href="{{ route('lms.dashboard') }}" class="surface-button-secondary justify-content-center">Open my workspace</a>
                                @else
                                    <a href="{{ route('asom.register') }}" class="surface-button-primary justify-content-center">Register to enroll</a>
                                    <a href="{{ route('login') }}" class="surface-button-secondary justify-content-center">Sign in</a>
                                @endauth
                            </div>
                        @endif
                    </aside>

                    <aside class="sidebar-card">
                        <h3 class="h5 mb-3">What you will cover</h3>
                        <div class="d-grid gap-3">
                            @forelse($course->lessons as $lesson)
                                <div class="rounded-4 border p-3 bg-light-subtle">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div>
                                            <div class="fw-semibold">{{ $lesson->title }}</div>
                                            <small class="text-muted">{{ $lesson->video_url ? 'Video lesson' : 'Reading lesson' }}</small>
                                        </div>
                                        @if($isEnrolled && $completedLessons->contains($lesson->id))
                                            <i class="fas fa-check-circle text-success mt-1"></i>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">The lesson outline for this course will appear here when it is available.</p>
                            @endforelse
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</x-layouts.lms>
