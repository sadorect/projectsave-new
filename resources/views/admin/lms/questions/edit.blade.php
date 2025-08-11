@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Edit Question - {{ $exam->title }}</h3>
            <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-secondary">Back to Exam</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.questions.update', [$exam, $question]) }}" method="POST" id="questionForm">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Question Text</label>
                    <textarea name="question_text" class="form-control" rows="3" required>{{ $question->question_text }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Options</label>
                    <div id="optionsContainer">
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
                    <button type="button" class="btn btn-secondary mt-2" id="addOption">
                        <i class="bi bi-plus"></i> Add Option
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label">Correct Answer</label>
                    <select name="correct_answer" class="form-select" required>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const optionsContainer = document.getElementById('optionsContainer');
        const addOptionBtn = document.getElementById('addOption');
        const correctAnswerSelect = document.querySelector('select[name="correct_answer"]');
        let optionCount = document.querySelectorAll('.option-row').length;

        function updateCorrectAnswerOptions() {
            const options = document.querySelectorAll('input[name="options[]"]');
            correctAnswerSelect.innerHTML = '<option value="">Select correct answer</option>';
            
            options.forEach((option, index) => {
                if (option.value.trim()) {
                    const letter = String.fromCharCode(65 + index);
                    const optionElement = document.createElement('option');
                    optionElement.value = option.value;
                    optionElement.textContent = `${letter}: ${option.value}`;
                    if (option.value === '{{ $question->correct_answer }}') {
                        optionElement.selected = true;
                    }
                    correctAnswerSelect.appendChild(optionElement);
                }
            });
        }

        function addOption() {
            optionCount++;
            const letter = String.fromCharCode(64 + optionCount);
            
            const optionRow = document.createElement('div');
            optionRow.className = 'option-row mb-2';
            optionRow.innerHTML = `
                <div class="input-group">
                    <span class="input-group-text">${letter}</span>
                    <input type="text" name="options[]" class="form-control" required>
                    <button type="button" class="btn btn-danger remove-option">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;

            optionsContainer.appendChild(optionRow);
            updateCorrectAnswerOptions();
        }

        addOptionBtn.addEventListener('click', addOption);

        optionsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-option')) {
                const optionRow = e.target.closest('.option-row');
                optionRow.remove();
                updateCorrectAnswerOptions();
            }
        });

        optionsContainer.addEventListener('input', function(e) {
            if (e.target.matches('input[name="options[]"]')) {
                updateCorrectAnswerOptions();
            }
        });

        updateCorrectAnswerOptions();
    });
</script>
@endpush
