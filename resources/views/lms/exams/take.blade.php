<x-layouts.asom-auth page-title="{{ $exam->title }}" subtitle="Exam in Progress">
    <style>
        .exam-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .exam-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 15px 15px 0 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .timer-display {
            font-size: 1.5rem;
            font-weight: 700;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .timer-warning {
            background: rgba(255, 193, 7, 0.9) !important;
            animation: pulse 1s infinite;
        }
        
        .timer-critical {
            background: rgba(220, 53, 69, 0.9) !important;
            animation: pulse 0.5s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .progress-bar {
            height: 8px;
            background: rgba(255,255,255,0.3);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 1rem;
        }
        
        .progress-fill {
            height: 100%;
            background: white;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .question-container {
            background: white;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            min-height: 500px;
        }
        
        .question-header {
            background: #f8f9fa;
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .question-content {
            padding: 2rem;
        }
        
        .question-text {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            color: #2c3e50;
        }
        
        .option-item {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .option-item:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }
        
        .option-item.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .option-radio {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #ccc;
            position: relative;
            flex-shrink: 0;
        }
        
        .option-item.selected .option-radio {
            border-color: white;
            background: white;
        }
        
        .option-item.selected .option-radio::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background: #667eea;
            border-radius: 50%;
        }
        
        .navigation-container {
            background: white;
            padding: 1.5rem;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .question-nav {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            max-width: 400px;
        }
        
        .question-dot {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #e9ecef;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .question-dot.current {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }
        
        .question-dot.answered {
            border-color: #28a745;
            background: #28a745;
            color: white;
        }
        
        .question-dot:hover {
            transform: scale(1.1);
        }
        
        .btn-nav {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .btn-nav:disabled {
            background: #6c757d;
            transform: none;
            box-shadow: none;
            cursor: not-allowed;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 1rem 3rem;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.3);
            color: white;
        }
        
        .auto-save-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1000;
        }
        
        .auto-save-indicator.show {
            opacity: 1;
        }
        
        @media (max-width: 768px) {
            .exam-header {
                padding: 1rem;
            }
            
            .timer-display {
                font-size: 1.2rem;
                padding: 0.5rem 1rem;
            }
            
            .question-content {
                padding: 1.5rem;
            }
            
            .navigation-container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .question-nav {
                justify-content: center;
                max-width: none;
            }
        }
    </style>

    <div class="exam-container">
        <div class="exam-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $exam->title }}</h4>
                    <p class="mb-0 opacity-75">Question <span id="currentQuestionNumber">1</span> of {{ $questions->count() }}</p>
                </div>
                <div class="timer-display" id="timerDisplay">
                    <i class="fas fa-clock"></i>
                    <span id="timeRemaining">{{ floor($remainingTime / 60) }}:{{ sprintf('%02d', $remainingTime % 60) }}</span>
                </div>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: {{ $questions->count() > 0 ? (1 / $questions->count()) * 100 : 0 }}%"></div>
            </div>
        </div>
        
        <div class="question-container">
            <form id="examForm" action="{{ route('lms.exams.submit', [$exam, $attempt]) }}" method="POST">
                @csrf
                <div id="questionsContainer">
                    @foreach($questions as $index => $question)
                        <div class="question-slide {{ $index === 0 ? 'active' : 'd-none' }}" data-question-id="{{ $question->id }}">
                            <div class="question-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Question {{ $index + 1 }}</h5>
                                    <span class="badge bg-primary">{{ $question->points }} point{{ $question->points !== 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                            
                            <div class="question-content">
                                <div class="question-text">
                                    {!! nl2br(e($question->question_text)) !!}
                                </div>
                                
                                <div class="options-container">
                                    @php
                                        $options = is_array($question->options) ? $question->options : json_decode($question->options, true) ?? [];
                                    @endphp
                                    @foreach($options as $optionKey => $option)
                                        <div class="option-item" data-value="{{ $option }}" onclick="selectOption(this, {{ $question->id }}, '{{ $option }}')">
                                            <div class="option-radio"></div>
                                            <div class="option-text">{{ $option }}</div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <input type="hidden" name="answers[{{ $question->id }}]" id="answer_{{ $question->id }}" value="{{ old('answers.' . $question->id, (is_array($attempt->answers) && isset($attempt->answers[$question->id])) ? $attempt->answers[$question->id]['answer'] : '') }}">
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="navigation-container">
                    <div class="question-nav">
                        @foreach($questions as $index => $question)
                            <div class="question-dot {{ $index === 0 ? 'current' : '' }}" 
                                 data-question-index="{{ $index }}" 
                                 onclick="goToQuestion({{ $index }})">
                                {{ $index + 1 }}
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="nav-buttons">
                        <button type="button" id="prevBtn" class="btn-nav me-2" onclick="previousQuestion()" disabled>
                            <i class="fas fa-chevron-left me-2"></i>Previous
                        </button>
                        <button type="button" id="nextBtn" class="btn-nav me-3" onclick="nextQuestion()">
                            Next<i class="fas fa-chevron-right ms-2"></i>
                        </button>
                        <button type="button" id="submitBtn" class="btn-submit d-none" onclick="submitExam()">
                            <i class="fas fa-check me-2"></i>Submit Exam
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="auto-save-indicator" id="autoSaveIndicator">
        <i class="fas fa-save me-2"></i>Answer saved
    </div>

    <script>
        let currentQuestion = 0;
        let totalQuestions = {{ $questions->count() }};
        let timeRemaining = {{ $remainingTime }};
        let examSubmitted = false;
        let answers = @json(is_array($attempt->answers) ? $attempt->answers : []);
        let questionStartTimes = {};
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            startTimer();
            loadSavedAnswers();
            questionStartTimes[getCurrentQuestionId()] = Date.now();
            
            // Prevent browser back button and page refresh
            window.history.pushState(null, "", window.location.href);
            window.onpopstate = function() {
                window.history.pushState(null, "", window.location.href);
            };
            
            window.addEventListener('beforeunload', function(e) {
                if (!examSubmitted) {
                    e.preventDefault();
                    e.returnValue = 'You have an exam in progress. Are you sure you want to leave?';
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
                
                // Auto-save every 30 seconds
                if (timeRemaining % 30 === 0) {
                    autoSaveCurrentAnswer();
                }
            }, 1000);
        }
        
        function updateTimerDisplay() {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            document.getElementById('timeRemaining').textContent = display;
            
            const timerElement = document.getElementById('timerDisplay');
            if (timeRemaining <= 300) { // 5 minutes
                timerElement.classList.add('timer-critical');
            } else if (timeRemaining <= 600) { // 10 minutes
                timerElement.classList.add('timer-warning');
            }
        }
        
        function selectOption(element, questionId, value) {
            // Remove selection from other options in this question
            element.parentElement.querySelectorAll('.option-item').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Select this option
            element.classList.add('selected');
            
            // Update hidden input
            document.getElementById(`answer_${questionId}`).value = value;
            
            // Track time spent on this question
            const timeSpent = questionStartTimes[questionId] ? Date.now() - questionStartTimes[questionId] : 0;
            
            // Save answer
            saveAnswer(questionId, value, timeSpent);
            
            // Update navigation dot
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
            .catch(error => {
                console.error('Error saving answer:', error);
            });
        }
        
        function showAutoSaveIndicator() {
            const indicator = document.getElementById('autoSaveIndicator');
            indicator.classList.add('show');
            setTimeout(() => {
                indicator.classList.remove('show');
            }, 2000);
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
            // Save time spent on current question
            const currentQuestionId = getCurrentQuestionId();
            if (questionStartTimes[currentQuestionId]) {
                const timeSpent = Date.now() - questionStartTimes[currentQuestionId];
                // You could save this time spent data if needed
            }
            
            // Hide current question
            document.querySelectorAll('.question-slide').forEach(slide => {
                slide.classList.add('d-none');
                slide.classList.remove('active');
            });
            
            // Show target question
            const targetSlide = document.querySelectorAll('.question-slide')[index];
            targetSlide.classList.remove('d-none');
            targetSlide.classList.add('active');
            
            // Update current question index
            currentQuestion = index;
            
            // Track start time for new question
            const newQuestionId = getCurrentQuestionId();
            questionStartTimes[newQuestionId] = Date.now();
            
            // Update UI
            updateNavigation();
            updateProgress();
            updateQuestionNumber();
        }
        
        function updateNavigation() {
            // Update navigation dots
            document.querySelectorAll('.question-dot').forEach((dot, index) => {
                dot.classList.remove('current');
                if (index === currentQuestion) {
                    dot.classList.add('current');
                }
            });
            
            // Update navigation buttons
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
            if (dot && !dot.classList.contains('answered')) {
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
                if (answer && answer.answer) {
                    const hiddenInput = document.getElementById(`answer_${questionId}`);
                    if (hiddenInput) {
                        hiddenInput.value = answer.answer;
                        
                        // Find and select the option
                        const questionSlide = document.querySelector(`[data-question-id="${questionId}"]`);
                        if (questionSlide) {
                            const options = questionSlide.querySelectorAll('.option-item');
                            options.forEach(option => {
                                if (option.dataset.value === answer.answer) {
                                    option.classList.add('selected');
                                }
                            });
                            
                            // Update question dot
                            const questionIndex = Array.from(document.querySelectorAll('.question-slide')).indexOf(questionSlide);
                            updateQuestionDot(questionIndex);
                        }
                    }
                }
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
                autoSaveCurrentAnswer(); // Save current answer before submitting
                document.getElementById('examForm').submit();
            }
        }
        
        function autoSubmitExam() {
            examSubmitted = true;
            alert('Time is up! Your exam will be submitted automatically.');
            autoSaveCurrentAnswer();
            document.getElementById('examForm').submit();
        }
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' && currentQuestion > 0) {
                previousQuestion();
            } else if (e.key === 'ArrowRight' && currentQuestion < totalQuestions - 1) {
                nextQuestion();
            }
        });
    </script>
</x-layouts.asom-auth>
