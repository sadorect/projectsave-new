<x-layouts.asom-auth
    :page-title="$exam->title"
    :subtitle="'Course: ' . $exam->course->title"
>
    <nav class="breadcrumb-nav mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('lms.dashboard') }}">Student workspace</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item active">{{ $exam->title }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-xl-8">
            <article class="exam-detail-card">
                <div class="exam-info-header">
                    <h2 class="mb-3">{{ $exam->title }}</h2>
                    @if($exam->description)
                        <p class="mb-4 text-white-50">{{ $exam->description }}</p>
                    @endif
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-number">{{ $exam->duration_minutes }}</span>
                            <span class="info-label">Minutes</span>
                        </div>
                        <div class="info-item">
                            <span class="info-number">{{ $questionCount }}</span>
                            <span class="info-label">Questions</span>
                        </div>
                        <div class="info-item">
                            <span class="info-number">{{ $exam->passing_score }}%</span>
                            <span class="info-label">Passing score</span>
                        </div>
                        <div class="info-item">
                            <span class="info-number">{{ $remainingAttempts }}</span>
                            <span class="info-label">Attempts left</span>
                        </div>
                    </div>
                </div>

                <div class="exam-content">
                    @if($attempts->isNotEmpty())
                        <h3 class="h5 mb-3">Attempt history</h3>
                        <div class="d-grid gap-3 mb-4">
                            @foreach($attempts as $attempt)
                                <div class="attempt-card {{ $attempt->completed_at && $attempt->score >= $exam->passing_score ? 'passed' : ($attempt->completed_at ? 'failed' : '') }}">
                                    <div class="row align-items-center g-3">
                                        <div class="col-md-6">
                                            <div class="fw-semibold">
                                                Attempt #{{ $loop->iteration }}
                                                <span class="badge ms-2 {{ $attempt->completed_at && $attempt->score >= $exam->passing_score ? 'bg-success' : ($attempt->completed_at ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                    {{ $attempt->completed_at ? ($attempt->score >= $exam->passing_score ? 'Passed' : 'Failed') : 'In progress' }}
                                                </span>
                                            </div>
                                            <small class="text-muted">
                                                {{ ($attempt->completed_at ?? $attempt->started_at)?->format('M j, Y g:i A') }}
                                            </small>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            @if($attempt->completed_at)
                                                <div class="score-display {{ $attempt->score >= $exam->passing_score ? 'passed' : 'failed' }}">
                                                    {{ $attempt->score }}%
                                                </div>
                                            @else
                                                <span class="text-warning fw-semibold">In progress</span>
                                            @endif
                                        </div>
                                        <div class="col-md-3 text-md-end">
                                            @if($attempt->completed_at)
                                                <a href="{{ route('lms.exams.results', [$exam, $attempt]) }}" class="surface-button-secondary">View results</a>
                                            @else
                                                <a href="{{ route('lms.exams.take', [$exam, $attempt]) }}" class="surface-button-primary">Continue</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($canRetake)
                        <div class="instructions-box mb-4">
                            <h3 class="h6 mb-3">Before you begin</h3>
                            <ul class="mb-0">
                                <li>You have {{ $exam->duration_minutes }} minutes to complete this exam.</li>
                                <li>You need at least {{ $exam->passing_score }}% to pass.</li>
                                <li>Answers are saved as you work through the questions.</li>
                                <li>Use a stable connection and finish in one sitting whenever possible.</li>
                            </ul>
                        </div>

                        @if($remainingAttempts <= 2 && $attempts->isNotEmpty())
                            <div class="warning-box mb-4">
                                <strong>Limited attempts remaining.</strong>
                                <p class="mb-0 mt-2">You have {{ $remainingAttempts }} attempt{{ $remainingAttempts === 1 ? '' : 's' }} left for this exam.</p>
                            </div>
                        @endif

                        <form action="{{ route('lms.exams.start', $exam) }}" method="POST" onsubmit="return confirm('Are you ready to start the exam? The timer will begin immediately.')">
                            @csrf
                            <button type="submit" class="btn-start-exam {{ $attempts->isNotEmpty() ? 'retake' : '' }}">
                                <i class="fas fa-play"></i>
                                {{ $attempts->isNotEmpty() ? 'Start new attempt' : 'Start exam' }}
                            </button>
                        </form>
                    @else
                        <div class="warning-box">
                            @if($remainingAttempts <= 0)
                                <strong>No attempts remaining.</strong>
                                <p class="mb-0 mt-2">You have used all allowed attempts for this assessment.</p>
                            @elseif($lastAttempt && $lastAttempt->score >= $exam->passing_score)
                                <strong>Exam passed.</strong>
                                <p class="mb-0 mt-2">You have already met the pass mark for this exam.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </article>
        </div>

        <div class="col-xl-4">
            <div class="d-grid gap-4">
                <aside class="sidebar-card">
                    <h3 class="h5 mb-3">Course context</h3>
                    <div class="d-grid gap-3">
                        <div>
                            <small class="text-muted d-block">Course</small>
                            <strong>{{ $exam->course->title }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Instructor</small>
                            <strong>{{ $exam->course->instructor?->name ?? 'ASOM Team' }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Lessons completed</small>
                            <strong>{{ $exam->course->lessons->count() }} lessons in this course</strong>
                        </div>
                    </div>
                </aside>

                <aside class="sidebar-card">
                    <h3 class="h5 mb-3">Quick links</h3>
                    <div class="d-grid gap-2">
                        <a href="{{ route('lms.exams.index') }}" class="surface-button-secondary justify-content-center">All exams</a>
                        <a href="{{ route('lms.lessons.index', $exam->course->slug) }}" class="surface-button-secondary justify-content-center">Course lessons</a>
                        <a href="{{ route('lms.dashboard') }}" class="surface-button-ghost justify-content-center">Back to workspace</a>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-layouts.asom-auth>
