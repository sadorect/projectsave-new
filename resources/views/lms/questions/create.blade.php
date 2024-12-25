<x-layouts.app>
    <div class="container py-4">
        <h2>Add Question to {{ $exam->title }}</h2>
        
        <form action="{{ route('lms.questions.store', $exam) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Question Text</label>
                <textarea name="question_text" class="form-control" required rows="3"></textarea>
            </div>

            <div id="options-container" class="mb-3">
                <label class="form-label">Options</label>
                <div class="option-inputs">
                    <div class="input-group mb-2">
                        <input type="text" name="options[]" class="form-control" required>
                        <button type="button" class="btn btn-outline-danger remove-option">Remove</button>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-secondary" id="add-option">Add Option</button>
            </div>

            <div class="mb-3">
                <label class="form-label">Correct Answer</label>
                <select name="correct_answer" class="form-control" required id="correct-answer-select">
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Points</label>
                <input type="number" name="points" class="form-control" required min="1">
            </div>

            <button type="submit" class="btn btn-primary">Add Question</button>
        </form>
    </div>

    <script>
        const optionsContainer = document.querySelector('.option-inputs');
        const addOptionBtn = document.getElementById('add-option');
        const correctAnswerSelect = document.getElementById('correct-answer-select');

        function updateCorrectAnswerOptions() {
            correctAnswerSelect.innerHTML = '';
            document.querySelectorAll('input[name="options[]"]').forEach(input => {
                if (input.value) {
                    const option = document.createElement('option');
                    option.value = input.value;
                    option.textContent = input.value;
                    correctAnswerSelect.appendChild(option);
                }
            });
        }

        addOptionBtn.addEventListener('click', () => {
            const newOption = document.createElement('div');
            newOption.className = 'input-group mb-2';
            newOption.innerHTML = `
                <input type="text" name="options[]" class="form-control" required>
                <button type="button" class="btn btn-outline-danger remove-option">Remove</button>
            `;
            optionsContainer.appendChild(newOption);
        });

        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-option')) {
                e.target.parentElement.remove();
                updateCorrectAnswerOptions();
            }
        });

        document.addEventListener('input', (e) => {
            if (e.target.name === 'options[]') {
                updateCorrectAnswerOptions();
            }
        });
    </script>
</x-layouts.app>
