<x-layouts.lms>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>{{ $exam->title }}</h3>
            <span class="badge bg-info">
                Attempt {{ \App\Models\ExamAttempt::forUserAndExam(auth()->id(), $exam->id)->count() }} 
                of {{ $exam->max_attempts }}
            </span>
            <div id="examTimer" class="h4"></div>
        </div>

        <form id="examForm" action="{{ route('lms.exams.submit', [$exam, $attempt]) }}" method="POST">
            @csrf
            <div id="mainContent">
                <div id="questionContainer">
                    @foreach($exam->questions as $index => $question)
                        <div class="question-slide" data-question="{{ $index + 1 }}" style="{{ $index > 0 ? 'display:none' : '' }}">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5>Question {{ $index + 1 }} of {{ $exam->questions->count() }}</h5>
                                        <span class="badge bg-primary">{{ $question->points }} points</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="mb-3">{{ $question->question_text }}</p>
                                    
                                    <div class="options-list">
                                        @foreach(json_decode($question->options) as $optionIndex => $option)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input answer-input" 
                                                       type="radio" 
                                                       name="answers[{{ $question->id }}]" 
                                                       value="{{ $option }}"
                                                       data-question="{{ $index + 1 }}"
                                                       id="q{{ $question->id }}_{{ $optionIndex }}">
                                                <label class="form-check-label" for="q{{ $question->id }}_{{ $optionIndex }}">
                                                    {{ chr(65 + $optionIndex) }}. {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div id="reviewContainer" style="display:none">
                    <div class="card">
                        <div class="card-header">
                            <h5>Review Your Answers</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="d-flex flex-wrap gap-2" id="questionNav">
                                        @foreach($exam->questions as $index => $question)
                                            <button type="button" 
                                                    class="btn btn-outline-secondary question-nav-btn" 
                                                    data-question="{{ $index + 1 }}">
                                                {{ $index + 1 }}
                                            </button>
                                        @endforeach
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge bg-success">Answered</span>
                                        <span class="badge bg-danger">Unanswered</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="answerSummary">
                                @foreach($exam->questions as $index => $question)
                                    <div class="answer-summary-item mb-3">
                                        <h6>Question {{ $index + 1 }}</h6>
                                        <p>{{ $question->question_text }}</p>
                                        <p class="selected-answer text-primary" data-question="{{ $index + 1 }}">
                                            No answer selected
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-secondary" id="prevBtn" disabled>Previous</button>
                <div>
                    <span id="questionProgress">Question 1 of {{ $exam->questions->count() }}</span>
                    <button type="button" class="btn btn-info ms-3" id="reviewBtn">Review Answers</button>
                </div>
                <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                <button type="submit" class="btn btn-success" id="submitBtn" style="display:none">Submit Exam</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalQuestions = {{ $exam->questions->count() }};
            let currentQuestion = 1;
            let isReviewMode = false;
            
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            const reviewBtn = document.getElementById('reviewBtn');
            const questionContainer = document.getElementById('questionContainer');
            const reviewContainer = document.getElementById('reviewContainer');
            
            function updateAnswerStatus() {
                document.querySelectorAll('.question-nav-btn').forEach(btn => {
                    const questionNum = btn.dataset.question;
                    const answered = document.querySelector(`.answer-input[data-question="${questionNum}"]:checked`) !== null;
                    btn.classList.toggle('btn-success', answered);
                    btn.classList.toggle('btn-danger', !answered);
                });

                document.querySelectorAll('.answer-input:checked').forEach(input => {
                    const questionNum = input.dataset.question;
                    const answerText = input.nextElementSibling.textContent;
                    document.querySelector(`.selected-answer[data-question="${questionNum}"]`).textContent = 
                        `Selected: ${answerText}`;
                });
            }

            function showQuestion(questionNumber) {
                document.querySelectorAll('.question-slide').forEach(slide => {
                    slide.style.display = 'none';
                });
                
                document.querySelector(`[data-question="${questionNumber}"]`).style.display = 'block';
                document.getElementById('questionProgress').textContent = `Question ${questionNumber} of ${totalQuestions}`;
                
                prevBtn.disabled = questionNumber === 1;
                nextBtn.style.display = 'block';
                submitBtn.style.display = 'none';
                
                if (questionNumber === totalQuestions) {
                    nextBtn.style.display = 'none';
                    reviewBtn.style.display = 'block';
                }
            }

            function toggleReviewMode() {
                isReviewMode = !isReviewMode;
                questionContainer.style.display = isReviewMode ? 'none' : 'block';
                reviewContainer.style.display = isReviewMode ? 'block' : 'none';
                
                if (isReviewMode) {
                    prevBtn.style.display = 'none';
                    nextBtn.style.display = 'none';
                    reviewBtn.textContent = 'Back to Questions';
                    submitBtn.style.display = 'block';
                    updateAnswerStatus();
                } else {
                    prevBtn.style.display = 'block';
                    nextBtn.style.display = currentQuestion === totalQuestions ? 'none' : 'block';
                    reviewBtn.textContent = 'Review Answers';
                    submitBtn.style.display = 'none';
                    showQuestion(currentQuestion);
                }
            }
            
            prevBtn.addEventListener('click', () => {
                if (currentQuestion > 1) {
                    currentQuestion--;
                    showQuestion(currentQuestion);
                }
            });
            
            nextBtn.addEventListener('click', () => {
                if (currentQuestion < totalQuestions) {
                    currentQuestion++;
                    showQuestion(currentQuestion);
                }
            });

            reviewBtn.addEventListener('click', toggleReviewMode);

            document.querySelectorAll('.question-nav-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    currentQuestion = parseInt(btn.dataset.question);
                    toggleReviewMode();
                });
            });

            document.querySelectorAll('.answer-input').forEach(input => {
                input.addEventListener('change', updateAnswerStatus);
            });
            
            // Timer functionality
            const endTime = new Date().getTime() + ({{ $exam->duration_minutes }} * 60 * 1000);
            
            const timer = setInterval(function() {
                const now = new Date().getTime();
                const distance = endTime - now;
                
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                document.getElementById("examTimer").innerHTML = 
                    minutes + "m " + seconds + "s ";
                    
                if (distance < 0) {
                    clearInterval(timer);
                    document.getElementById("examForm").submit();
                }
            }, 1000);
        });
    </script>
    @endpush
</x-layouts.lms>
