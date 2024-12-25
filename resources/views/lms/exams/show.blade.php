<x-layouts.app>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>{{ $exam->title }}</h2>
            <div>
                <a href="{{ route('lms.exams.edit', $exam) }}" class="btn btn-primary">Edit Exam</a>
                <a href="{{ route('lms.exams.attempt.start', $exam) }}" class="btn btn-success">Take Exam</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5>Exam Details</h5>
                <p><strong>Course:</strong> {{ $exam->course->title }}</p>
                <p><strong>Duration:</strong> {{ $exam->duration_minutes }} minutes</p>
                <p><strong>Passing Score:</strong> {{ $exam->passing_score }}%</p>
                <p><strong>Total Questions:</strong> {{ $exam->questions->count() }}</p>
            </div>
        </div>

        @if($exam->questions->count() > 0)
            <div class="mt-4">
                <h3>Questions Preview</h3>
                @foreach($exam->questions as $index => $question)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>Question {{ $index + 1 }}</h5>
                            <p>{{ $question->question_text }}</p>
                            <p><strong>Points:</strong> {{ $question->points }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
