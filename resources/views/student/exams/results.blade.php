<x-layouts.lms>
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h3>Exam Results</h3>
        </div>
        <div class="card-body">
            <div class="text-center mb-4">
                <h4>Your Score</h4>
                <div class="display-4 mb-3">{{ number_format($attempt->score, 1) }}%</div>
                
                @if($attempt->score >= $attempt->exam->passing_score)
                    <div class="alert alert-success">
                        Congratulations! You have passed the exam.
                    </div>
                @else
                    <div class="alert alert-danger">
                        Unfortunately, you did not meet the passing score of {{ $attempt->exam->passing_score }}%.
                    </div>
                @endif
            </div>

            <div class="mt-4">
                <h5>Exam Details</h5>
                <ul class="list-unstyled">
                    <li>Duration: {{ $attempt->completed_at->diffInMinutes($attempt->started_at) }} minutes</li>
                    <li>Started: {{ $attempt->started_at->format('M d, Y h:i A') }}</li>
                    <li>Completed: {{ $attempt->completed_at->format('M d, Y h:i A') }}</li>
                </ul>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('lms.exams.index') }}" class="btn btn-primary">Back to Exams</a>
            </div>
        </div>
    </div>
</div>
</x-layouts.lms>
