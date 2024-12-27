<x-layouts.lms>
    <div class="container py-4">
        <div class="card">
            <div class="card-body text-center">
                <h2 class="card-title">Exam Results</h2>
                <h3 class="mb-4">{{ $exam->title }}</h3>

                <div class="display-4 mb-4">
                    Score: {{ number_format($attempt->score, 1) }}%
                </div>

                <div class="h4 mb-4">
                    @if($attempt->passed)
                        <span class="text-success">Passed!</span>
                    @else
                        <span class="text-danger">Failed</span>
                    @endif
                </div>

                <div class="text-muted mb-4">
                    Time taken: {{ $attempt->started_at->diffInMinutes($attempt->completed_at) }} minutes
                </div>

                <a href="{{ route('lms.courses.show', $exam->course) }}" 
                   class="btn btn-primary">Back to Course</a>
            </div>
        </div>

        @if($attempt->passed)
        <div class="alert alert-success mt-4">
            <h4>Congratulations!</h4>
            <p>You have successfully passed this exam. Continue with your learning journey!</p>
        </div>
        @else
        <div class="alert alert-info mt-4">
            <h4>Keep Learning!</h4>
            <p>Review the course material and try again. You need {{ $exam->passing_score }}% to pass.</p>
        </div>
        @endif
    </div>
</x-layouts.lms>
