<x-layouts.app>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Exam: {{ $exam->title }}</h2>
            <a href="{{ route('lms.questions.create', $exam) }}" class="btn btn-primary">Add Question</a>
        </div>

        <form action="{{ route('lms.exams.update', $exam) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Course</label>
                <select name="course_id" class="form-select" required>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $exam->course_id == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="{{ $exam->title }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Duration (minutes)</label>
                <input type="number" name="duration_minutes" class="form-control" value="{{ $exam->duration_minutes }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Passing Score (%)</label>
                <input type="number" name="passing_score" class="form-control" value="{{ $exam->passing_score }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Exam</button>
        </form>

        @if($exam->questions->count() > 0)
            <div class="mt-5">
                <h3>Questions</h3>
                @foreach($exam->questions as $question)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>{{ $question->question_text }}</h5>
                            <ul>
                                @foreach(json_decode($question->options) as $option)
                                    <li>{{ $option }}</li>
                                @endforeach
                            </ul>
                            <p><strong>Correct Answer:</strong> {{ $question->correct_answer }}</p>
                            <p><strong>Points:</strong> {{ $question->points }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
