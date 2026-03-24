<x-layouts.asom-auth
    page-title="Available Exams"
    subtitle="Complete course assessments, review attempt history, and move toward certification with a clearer exam flow."
>
    <nav class="breadcrumb-nav mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('lms.dashboard') }}">Student workspace</a></li>
            <li class="breadcrumb-item active">Exams</li>
        </ol>
    </nav>

    <div class="d-grid gap-4">
        <section class="lms-summary-grid">
            <article class="lms-summary-card">
                <span class="label">Available exams</span>
                <span class="value">{{ $examStats['available'] }}</span>
            </article>
            <article class="lms-summary-card">
                <span class="label">Passed exams</span>
                <span class="value">{{ $examStats['passed'] }}</span>
            </article>
            <article class="lms-summary-card">
                <span class="label">Still pending</span>
                <span class="value">{{ $examStats['pending'] }}</span>
            </article>
        </section>

        @if($examCatalog->isNotEmpty())
            <section class="lms-exam-grid">
                @foreach($examCatalog as $entry)
                    <article class="exam-card">
                        <div class="exam-header">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <h3 class="h4 mb-1">{{ $entry['model']->title }}</h3>
                                    <p class="mb-0 text-white-50">{{ $entry['course']->title }}</p>
                                </div>
                                <span class="score-badge {{ $entry['hasPassed'] ? 'score-pass' : 'score-fail' }}">
                                    @if($entry['hasPassed'])
                                        <i class="fas fa-check-circle"></i>Passed
                                    @elseif($entry['bestScore'] !== null)
                                        {{ $entry['bestScore'] }}%
                                    @else
                                        Pending
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="exam-content">
                            @if($entry['model']->description)
                                <p class="text-muted mb-3">{{ $entry['model']->description }}</p>
                            @endif

                            <div class="exam-stats mb-4">
                                <div class="stat-item text-center">
                                    <span class="stat-number d-block">{{ $entry['model']->duration_minutes }}</span>
                                    <span class="text-muted small">Minutes</span>
                                </div>
                                <div class="stat-item text-center">
                                    <span class="stat-number d-block">{{ $entry['model']->questions_count }}</span>
                                    <span class="text-muted small">Questions</span>
                                </div>
                                <div class="stat-item text-center">
                                    <span class="stat-number d-block">{{ $entry['model']->passing_score }}%</span>
                                    <span class="text-muted small">Pass score</span>
                                </div>
                                <div class="stat-item text-center">
                                    <span class="stat-number d-block">{{ $entry['remainingAttempts'] }}</span>
                                    <span class="text-muted small">Attempts left</span>
                                </div>
                            </div>

                            @if($entry['attempts']->isNotEmpty())
                                <div class="attempt-history">
                                    <h4 class="h6 mb-3">Recent attempts</h4>
                                    <div class="d-grid gap-2">
                                        @foreach($entry['attempts']->take(3) as $attempt)
                                            <div class="d-flex justify-content-between align-items-center gap-3">
                                                <small>{{ $attempt->completed_at ? $attempt->completed_at->format('M j, Y g:i A') : 'In progress' }}</small>
                                                <span class="badge {{ $attempt->completed_at && $attempt->score >= $entry['model']->passing_score ? 'bg-success' : ($attempt->completed_at ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                    {{ $attempt->completed_at ? $attempt->score . '%' : 'Incomplete' }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">
                                <a href="{{ route('lms.exams.show', $entry['model']) }}" class="btn-exam">
                                    {{ $entry['attemptCount'] > 0 ? 'Review exam' : 'Start exam' }}
                                </a>

                                @if($entry['lastAttempt'] && $entry['lastAttempt']->completed_at)
                                    <a href="{{ route('lms.exams.results', [$entry['model'], $entry['lastAttempt']]) }}" class="surface-button-secondary">
                                        View results
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>
        @else
            <x-ui.empty-state
                title="No exams available yet"
                message="Complete your course lessons to unlock the assessments that belong to finished courses."
            >
                <x-slot:actions>
                    <a href="{{ route('lms.dashboard') }}" class="surface-button-primary">Back to workspace</a>
                </x-slot:actions>
            </x-ui.empty-state>
        @endif
    </div>
</x-layouts.asom-auth>
