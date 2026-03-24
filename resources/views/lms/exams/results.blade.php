<x-layouts.asom-auth
    page-title="Exam Results"
    :subtitle="$exam->title"
>
    <nav class="breadcrumb-nav mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('lms.dashboard') }}">Student workspace</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.exams.show', $exam) }}">{{ $exam->title }}</a></li>
            <li class="breadcrumb-item active">Results</li>
        </ol>
    </nav>

    <div class="results-header">
        <div class="results-icon">
            <i class="fas {{ $passed ? 'fa-trophy' : 'fa-rotate-left' }}"></i>
        </div>
        <div class="results-score">{{ $attempt->score }}%</div>
        <div class="results-status">
            {{ $passed ? 'Congratulations, you passed this exam.' : 'This attempt did not meet the pass mark yet.' }}
        </div>
        <p class="mt-3 mb-0 text-white-50">
            Completed on {{ $attempt->completed_at->format('M j, Y \a\t g:i A') }}
        </p>
    </div>

    <div class="analytics-grid mb-4">
        <article class="analytics-card">
            <span class="analytics-number">{{ $analytics['total_questions'] }}</span>
            <span class="analytics-label">Questions</span>
        </article>
        <article class="analytics-card">
            <span class="analytics-number">{{ $analytics['correct_answers'] }}</span>
            <span class="analytics-label">Correct</span>
        </article>
        <article class="analytics-card">
            <span class="analytics-number">{{ $analytics['accuracy_percentage'] }}%</span>
            <span class="analytics-label">Accuracy</span>
        </article>
        <article class="analytics-card">
            <span class="analytics-number">{{ gmdate('i:s', $analytics['exam_duration']) }}</span>
            <span class="analytics-label">Time taken</span>
        </article>
    </div>

    <section class="question-breakdown">
        <div class="breakdown-header">
            <h3 class="h5 mb-2">Question review</h3>
            <p class="text-muted mb-0 small">Review how this attempt went without exposing the protected answer key.</p>
        </div>

        @foreach($analytics['question_breakdown'] as $index => $questionData)
            <div class="question-item {{ $questionData['is_correct'] ? 'correct' : ($questionData['user_answer'] ? 'incorrect' : 'unanswered') }}">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                    <div class="question-number">Question {{ $index + 1 }}</div>
                    <div class="d-flex flex-wrap gap-2">
                        @if($questionData['time_spent'] > 0)
                            <span class="time-badge">
                                <i class="fas fa-clock"></i>
                                {{ gmdate('i:s', $questionData['time_spent'] / 1000) }}
                            </span>
                        @endif
                        <span class="points-badge {{ $questionData['is_correct'] ? 'points-earned' : 'points-lost' }}">
                            {{ $questionData['points_earned'] }}/{{ $questionData['question']->points }} pts
                        </span>
                    </div>
                </div>

                <div class="question-text">
                    {!! nl2br(e($questionData['question']->question_text)) !!}
                </div>

                <div class="answer-section">
                    <div class="answer-box user-answer {{ $questionData['is_correct'] ? '' : 'incorrect' }}">
                        <div class="answer-label">Your answer</div>
                        <div>
                            @if($questionData['user_answer'])
                                {{ $questionData['user_answer'] }}
                            @else
                                <em class="text-muted">No answer submitted</em>
                            @endif
                        </div>
                    </div>

                    <div class="answer-box {{ $questionData['is_correct'] ? 'correct-answer' : 'incorrect-feedback' }}">
                        <div class="answer-label">Result</div>
                        <div>
                            @if($questionData['is_correct'])
                                <strong class="text-success">Correct</strong>
                                <div class="small text-muted mt-1">This response earned full points.</div>
                            @elseif($questionData['user_answer'])
                                <strong class="text-danger">Incorrect</strong>
                                <div class="small text-muted mt-1">Review the related lesson material before your next attempt.</div>
                            @else
                                <strong class="text-warning">Not answered</strong>
                                <div class="small text-muted mt-1">No response was recorded for this question.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    <div class="action-buttons">
        @if($passed)
            <a href="{{ route('lms.exams.index') }}" class="btn-action btn-success-action">View all exams</a>
            <a href="{{ route('lms.dashboard') }}" class="btn-action btn-primary-action">Return to workspace</a>
        @else
            <a href="{{ route('lms.lessons.index', $exam->course->slug) }}" class="btn-action btn-primary-action">Review course lessons</a>
            @if($canRetake)
                <a href="{{ route('lms.exams.show', $exam) }}" class="btn-action btn-warning-action">
                    Retake exam ({{ $remainingAttempts }} left)
                </a>
            @endif
            <a href="{{ route('lms.exams.index') }}" class="btn-action btn-primary-action">Back to exams</a>
        @endif
    </div>

    @if($passed)
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof confetti !== 'function') {
                        return;
                    }

                    confetti({
                        particleCount: 90,
                        spread: 70,
                        origin: { y: 0.6 }
                    });
                });
            </script>
        @endpush
    @endif
</x-layouts.asom-auth>
