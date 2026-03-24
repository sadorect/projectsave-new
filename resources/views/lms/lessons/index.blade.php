<x-layouts.asom-auth
    :page-title="$course->title"
    :subtitle="Str::limit(trim(strip_tags((string) $course->description)), 120)"
>
    <nav class="breadcrumb-nav mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('lms.dashboard') }}">Student workspace</a></li>
            <li class="breadcrumb-item active">{{ $course->title }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="course-stats mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="stat-number">{{ $lessonCards->count() }}</div>
                            <div class="text-muted">Lessons</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="stat-number">{{ $completedLessonIds->count() }}</div>
                            <div class="text-muted">Completed</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="stat-number">{{ $courseProgress }}%</div>
                            <div class="text-muted">Progress</div>
                        </div>
                    </div>
                </div>
            </div>

            @forelse($lessonCards as $lessonCard)
                <div class="lesson-card {{ $lessonCard['is_completed'] ? 'completed' : '' }}">
                    <a href="{{ $lessonCard['url'] }}" class="lesson-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="lesson-number {{ $lessonCard['is_completed'] ? 'completed' : '' }}">
                                    @if($lessonCard['is_completed'])
                                        <i class="fas fa-check"></i>
                                    @else
                                        {{ $lessonCard['model']->order }}
                                    @endif
                                </div>
                            </div>
                            <div class="col">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <h3 class="h5 mb-2">{{ $lessonCard['title'] }}</h3>
                                        <p class="text-muted mb-2">{{ $lessonCard['excerpt'] }}</p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="lesson-meta">{{ $lessonCard['type_label'] }}</span>
                                            @if($lessonCard['is_completed'])
                                                <span class="lesson-meta text-success">Completed</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-brand-600">
                                        <i class="fas {{ $lessonCard['is_completed'] ? 'fa-check-circle text-success' : 'fa-play-circle' }} fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <x-ui.empty-state
                    title="No lessons available yet"
                    message="Lessons for this course will appear here as soon as they are published."
                />
            @endforelse
        </div>

        <div class="col-xl-4">
            <div class="d-grid gap-4">
                <aside class="progress-sidebar">
                    <h3 class="h5 mb-3">Course progress</h3>
                    <div class="lms-progress-bar mb-3">
                        <div class="lms-progress-fill" style="width: {{ $courseProgress }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <small class="text-muted">{{ $completedLessonIds->count() }} of {{ $lessonCards->count() }} completed</small>
                        <small class="fw-semibold">{{ $courseProgress }}%</small>
                    </div>

                    <div class="d-grid gap-2">
                        @if($nextLesson)
                            <a href="{{ route('lms.lessons.show', [$course->slug, $nextLesson->slug]) }}" class="surface-button-primary justify-content-center">
                                {{ $completedLessonIds->isNotEmpty() ? 'Continue learning' : 'Start course' }}
                            </a>
                        @endif
                        <a href="{{ route('lms.courses.show', $course->slug) }}" class="surface-button-secondary justify-content-center">Course details</a>
                        <a href="{{ route('lms.dashboard') }}" class="surface-button-ghost justify-content-center">Back to workspace</a>
                    </div>
                </aside>

                <aside class="sidebar-card">
                    <h3 class="h5 mb-3">Course support</h3>
                    <div class="d-grid gap-3">
                        <div>
                            <small class="text-muted d-block">Instructor</small>
                            <strong>{{ $course->instructor?->name ?? 'ASOM Team' }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Assessments</small>
                            <strong>{{ $availableExams->count() }} available exam{{ $availableExams->count() === 1 ? '' : 's' }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Program certificate</small>
                            <strong>
                                @if($diplomaCertificate)
                                    {{ $diplomaCertificate->is_approved ? 'Approved diploma available' : 'Diploma certificate pending approval' }}
                                @else
                                    {{ $diplomaStatus['completed_requirements'] }} of {{ $diplomaStatus['required_count'] }} requirements complete
                                @endif
                            </strong>
                            @if($manualCourseCertificate)
                                <small class="text-muted d-block mt-1">An individual course certificate is also on file.</small>
                            @endif
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-layouts.asom-auth>
