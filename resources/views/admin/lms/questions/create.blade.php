@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Add Question to: {{ $exam->title }}</h3>
            <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-secondary">Back to Exam</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.questions.store', $exam) }}" method="POST" id="questionForm" data-admin-question-builder>
                @csrf
                <div class="mb-3">
                    <label class="form-label">Question Text</label>
                    <textarea name="question_text" class="form-control" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Options</label>
                    <div id="optionsContainer" data-question-options>
                        <div class="option-row mb-2">
                            <div class="input-group">
                                <span class="input-group-text">A</span>
                                <input type="text" name="options[]" class="form-control" required>
                                <button type="button" class="btn btn-danger remove-option" disabled>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="option-row mb-2">
                            <div class="input-group">
                                <span class="input-group-text">B</span>
                                <input type="text" name="options[]" class="form-control" required>
                                <button type="button" class="btn btn-danger remove-option" disabled>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary mt-2" id="addOption" data-question-add-option>
                        <i class="bi bi-plus"></i> Add Option
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label">Correct Answer</label>
                    <select name="correct_answer" class="form-select" required data-question-correct-answer>
                        <option value="">Select correct answer</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Points</label>
                    <input type="number" name="points" class="form-control" required min="1" value="1">
                </div>
           
                   
                
                <div class="text-end">
                    <a href="{{ route('admin.exams.questions.import', $exam) }}" class="btn btn-secondary">Import Question</a>
                    <button type="submit" class="btn btn-primary">Save Question</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
