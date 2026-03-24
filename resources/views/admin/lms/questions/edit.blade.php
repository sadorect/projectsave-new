@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Edit Question - {{ $exam->title }}</h3>
            <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-secondary">Back to Exam</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.questions.update', [$exam, $question]) }}" method="POST" id="questionForm" data-admin-question-builder>
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Question Text</label>
                    <textarea name="question_text" class="form-control" rows="3" required>{{ $question->question_text }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Options</label>
                    <div id="optionsContainer" data-question-options>
                        @foreach($question->options as $index => $option)
                            <div class="option-row mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">{{ chr(65 + (int)$index) }}</span>
                                    <input type="text" name="options[]" class="form-control" required value="{{ $option }}">
                                    <button type="button" class="btn btn-danger remove-option" {{ $index < 2 ? 'disabled' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-secondary mt-2" id="addOption" data-question-add-option>
                        <i class="bi bi-plus"></i> Add Option
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label">Correct Answer</label>
                    <select name="correct_answer" class="form-select" required data-question-correct-answer data-selected-answer="{{ $question->correct_answer }}">
                        <option value="">Select correct answer</option>
                        @foreach($question->options as $index => $option)
                            <option value="{{ $option }}" {{ $question->correct_answer === $option ? 'selected' : '' }}>
                                {{ chr(65 + (int)$index) }}: {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Points</label>
                    <input type="number" name="points" class="form-control" required min="1" value="{{ $question->points }}">
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Update Question</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
