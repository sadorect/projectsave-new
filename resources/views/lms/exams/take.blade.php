<x-layouts.asom-auth
    :page-title="$exam->title"
    subtitle="Exam in progress"
>
    <div class="exam-container">
        <div class="exam-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h3 class="mb-1">{{ $exam->title }}</h3>
                    <p class="mb-0 text-white-50">Question <span id="currentQuestionNumber">1</span> of {{ $questions->count() }}</p>
                </div>
                <div class="timer-display" id="timerDisplay">
                    <i class="fas fa-clock"></i>
                    <span id="timeRemaining">{{ floor($remainingTime / 60) }}:{{ sprintf('%02d', $remainingTime % 60) }}</span>
                </div>
            </div>
            <div class="exam-progress-track">
                <div class="exam-progress-fill" id="progressFill" style="width: {{ $questions->count() > 0 ? (1 / $questions->count()) * 100 : 0 }}%"></div>
            </div>
        </div>

        <div class="question-container">
            <form id="examForm" action="{{ route('lms.exams.submit', [$exam, $attempt]) }}" method="POST">
                @csrf
                <div id="questionsContainer">
                    @foreach($questions as $index => $question)
                        <div class="question-slide {{ $index === 0 ? 'active' : 'd-none' }}" data-question-id="{{ $question->id }}">
                            <div class="question-header">
                                <div class="d-flex justify-content-between align-items-center gap-3">
                                    <h4 class="h5 mb-0">Question {{ $index + 1 }}</h4>
                                    <span class="badge bg-primary">{{ $question->points }} point{{ $question->points !== 1 ? 's' : '' }}</span>
                                </div>
                            </div>

                            <div class="question-content">
                                <div class="question-text">
                                    {!! nl2br(e($question->question_text)) !!}
                                </div>

                                @php
                                    $options = is_array($question->options) ? $question->options : json_decode($question->options, true) ?? [];
                                @endphp
                                <div class="options-container">
                                    @foreach($options as $option)
                                        <button type="button" class="option-item" data-value="{{ $option }}" onclick="selectOption(this, {{ $question->id }}, '{{ addslashes($option) }}')">
                                            <div class="option-radio"></div>
                                            <div class="option-text">{{ $option }}</div>
                                        </button>
                                    @endforeach
                                </div>

                                <input
                                    type="hidden"
                                    name="answers[{{ $question->id }}]"
                                    id="answer_{{ $question->id }}"
                                    value="{{ old('answers.' . $question->id, (is_array($attempt->answers) && isset($attempt->answers[$question->id])) ? $attempt->answers[$question->id]['answer'] : '') }}"
                                >
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="navigation-container">
                    <div class="question-nav">
                        @foreach($questions as $index => $question)
                            <button type="button" class="question-dot {{ $index === 0 ? 'current' : '' }}" data-question-index="{{ $index }}" onclick="goToQuestion({{ $index }})" aria-label="Go to question {{ $index + 1 }}">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" id="prevBtn" class="btn-nav" onclick="previousQuestion()" disabled>
                            <i class="fas fa-chevron-left"></i>
                            Previous
                        </button>
                        <button type="button" id="nextBtn" class="btn-nav" onclick="nextQuestion()">
                            Next
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button type="button" id="submitBtn" class="btn-submit d-none" onclick="submitExam()">
                            <i class="fas fa-check"></i>
                            Submit exam
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="auto-save-indicator" id="autoSaveIndicator">
        <i class="fas fa-save me-2"></i>Answer saved
    </div>

    @push('scripts')
        <script>
            let currentQuestion = 0;
            let totalQuestions = {{ $questions->count() }};
            let timeRemaining = {{ (int) $remainingTime }};
            let examSubmitted = false;
            let answers = @json(is_array($attempt->answers) ? $attempt->answers : []);
            let questionStartTimes = {};

            document.addEventListener('DOMContentLoaded', function() {
                startTimer();
                loadSavedAnswers();
                questionStartTimes[getCurrentQuestionId()] = Date.now();

                window.history.pushState(null, '', window.location.href);
                window.onpopstate = function() {
                    window.history.pushState(null, '', window.location.href);
                };

                window.addEventListener('beforeunload', function(event) {
                    if (!examSubmitted) {
                        event.preventDefault();
                        event.returnValue = 'You have an exam in progress. Are you sure you want to leave?';
                    }
                });
            });

            function startTimer() {
                const timer = setInterval(function() {
                    if (timeRemaining <= 0 || examSubmitted) {
                        clearInterval(timer);
                        if (!examSubmitted) {
                            autoSubmitExam();
                        }
                        return;
                    }

                    timeRemaining--;
                    updateTimerDisplay();

                    if (timeRemaining % 30 === 0) {
                        autoSaveCurrentAnswer();
                    }
                }, 1000);
            }

            function updateTimerDisplay() {
                const minutes = Math.floor(timeRemaining / 60);
                const seconds = Math.floor(timeRemaining) % 60;
                document.getElementById('timeRemaining').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

                const timerElement = document.getElementById('timerDisplay');
                if (timeRemaining <= 300) {
                    timerElement.classList.add('timer-critical');
                } else if (timeRemaining <= 600) {
                    timerElement.classList.add('timer-warning');
                }
            }

            function selectOption(element, questionId, value) {
                element.parentElement.querySelectorAll('.option-item').forEach(option => {
                    option.classList.remove('selected');
                });

                element.classList.add('selected');
                document.getElementById(`answer_${questionId}`).value = value;

                const timeSpent = questionStartTimes[questionId] ? Date.now() - questionStartTimes[questionId] : 0;
                saveAnswer(questionId, value, timeSpent);
                updateQuestionDot(currentQuestion);
            }

            function saveAnswer(questionId, answer, timeSpent = 0) {
                fetch(`{{ route('lms.exams.save-answer', [$exam, $attempt]) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        answer: answer,
                        time_spent: timeSpent
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAutoSaveIndicator();
                    }
                })
                .catch(error => console.error('Error saving answer:', error));
            }

            function showAutoSaveIndicator() {
                const indicator = document.getElementById('autoSaveIndicator');
                indicator.classList.add('show');
                setTimeout(() => indicator.classList.remove('show'), 2000);
            }

            function nextQuestion() {
                if (currentQuestion < totalQuestions - 1) {
                    goToQuestion(currentQuestion + 1);
                }
            }

            function previousQuestion() {
                if (currentQuestion > 0) {
                    goToQuestion(currentQuestion - 1);
                }
            }

            function goToQuestion(index) {
                document.querySelectorAll('.question-slide').forEach(slide => {
                    slide.classList.add('d-none');
                    slide.classList.remove('active');
                });

                const targetSlide = document.querySelectorAll('.question-slide')[index];
                targetSlide.classList.remove('d-none');
                targetSlide.classList.add('active');

                currentQuestion = index;
                questionStartTimes[getCurrentQuestionId()] = Date.now();
                updateNavigation();
                updateProgress();
                updateQuestionNumber();
            }

            function updateNavigation() {
                document.querySelectorAll('.question-dot').forEach((dot, index) => {
                    dot.classList.toggle('current', index === currentQuestion);
                });

                document.getElementById('prevBtn').disabled = currentQuestion === 0;

                if (currentQuestion === totalQuestions - 1) {
                    document.getElementById('nextBtn').classList.add('d-none');
                    document.getElementById('submitBtn').classList.remove('d-none');
                } else {
                    document.getElementById('nextBtn').classList.remove('d-none');
                    document.getElementById('submitBtn').classList.add('d-none');
                }
            }

            function updateProgress() {
                const progress = ((currentQuestion + 1) / totalQuestions) * 100;
                document.getElementById('progressFill').style.width = progress + '%';
            }

            function updateQuestionNumber() {
                document.getElementById('currentQuestionNumber').textContent = currentQuestion + 1;
            }

            function updateQuestionDot(questionIndex) {
                const dot = document.querySelectorAll('.question-dot')[questionIndex];
                if (dot) {
                    dot.classList.add('answered');
                }
            }

            function getCurrentQuestionId() {
                const activeSlide = document.querySelector('.question-slide.active');
                return activeSlide ? activeSlide.dataset.questionId : null;
            }

            function loadSavedAnswers() {
                Object.keys(answers).forEach(questionId => {
                    const answer = answers[questionId];
                    if (!answer || !answer.answer) {
                        return;
                    }

                    const hiddenInput = document.getElementById(`answer_${questionId}`);
                    if (!hiddenInput) {
                        return;
                    }

                    hiddenInput.value = answer.answer;
                    const questionSlide = document.querySelector(`[data-question-id="${questionId}"]`);
                    if (!questionSlide) {
                        return;
                    }

                    questionSlide.querySelectorAll('.option-item').forEach(option => {
                        if (option.dataset.value === answer.answer) {
                            option.classList.add('selected');
                        }
                    });

                    const questionIndex = Array.from(document.querySelectorAll('.question-slide')).indexOf(questionSlide);
                    updateQuestionDot(questionIndex);
                });
            }

            function autoSaveCurrentAnswer() {
                const currentQuestionId = getCurrentQuestionId();
                const answerInput = document.getElementById(`answer_${currentQuestionId}`);
                if (answerInput && answerInput.value) {
                    const timeSpent = questionStartTimes[currentQuestionId] ? Date.now() - questionStartTimes[currentQuestionId] : 0;
                    saveAnswer(currentQuestionId, answerInput.value, timeSpent);
                }
            }

            function submitExam() {
                if (confirm('Are you sure you want to submit your exam? You cannot change your answers after submission.')) {
                    examSubmitted = true;
                    autoSaveCurrentAnswer();
                    document.getElementById('examForm').submit();
                }
            }

            function autoSubmitExam() {
                examSubmitted = true;
                alert('Time is up. Your exam will be submitted automatically.');
                autoSaveCurrentAnswer();
                document.getElementById('examForm').submit();
            }

            document.addEventListener('keydown', function(event) {
                if (event.key === 'ArrowLeft' && currentQuestion > 0) {
                    previousQuestion();
                } else if (event.key === 'ArrowRight' && currentQuestion < totalQuestions - 1) {
                    nextQuestion();
                }
            });
        </script>
    @endpush
</x-layouts.asom-auth>
