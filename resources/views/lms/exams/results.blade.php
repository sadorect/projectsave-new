<x-layouts.asom-auth page-title="Exam Results" subtitle="{{ $exam->title }}">
    <style>
        .results-header {
            background: linear-gradient(135deg, {{ $passed ? '#28a745 0%, #20c997 100%' : '#dc3545 0%, #fd7e14 100%' }});
            color: white;
            padding: 3rem 2rem;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .results-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            animation: float 20s infinite linear;
        }
        
        @keyframes float {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        .results-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }
        
        .results-score {
            font-size: 4rem;
            font-weight: 700;
            margin: 1rem 0;
            position: relative;
            z-index: 1;
        }
        
        .results-status {
            font-size: 1.5rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }
        
        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .analytics-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .analytics-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .analytics-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #667eea;
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .analytics-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .question-breakdown {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .breakdown-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .question-item {
            padding: 1.5rem;
            border-bottom: 1px solid #f1f3f4;
            transition: background 0.3s ease;
        }
        
        .question-item:last-child {
            border-bottom: none;
        }
        
        .question-item:hover {
            background: #f8f9fa;
        }
        
        .question-item.correct {
            border-left: 4px solid #28a745;
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
        }
        
        .question-item.incorrect {
            border-left: 4px solid #dc3545;
            background: linear-gradient(135deg, #fde8e8 0%, #fff0f0 100%);
        }
        
        .question-item.unanswered {
            border-left: 4px solid #ffc107;
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        }
        
        .question-number {
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .question-text {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #2c3e50;
        }
        
        .answer-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .answer-box {
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .answer-box.user-answer {
            background: #f0f4ff;
            border-color: #667eea;
        }
        
        .answer-box.correct-answer {
            background: #e8f5e8;
            border-color: #28a745;
        }
        
        .answer-box.incorrect-feedback {
            background: #fff3cd;
            border-color: #ffc107;
        }
        
        .answer-box.user-answer.incorrect {
            background: #fde8e8;
            border-color: #dc3545;
        }
        
        .answer-label {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            opacity: 0.8;
        }
        
        .time-badge {
            background: #667eea;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .points-badge {
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.85rem;
        }
        
        .points-earned {
            background: #28a745;
            color: white;
        }
        
        .points-lost {
            background: #dc3545;
            color: white;
        }
        
        .action-buttons {
            text-align: center;
            margin-top: 3rem;
        }
        
        .btn-action {
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary-action {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        
        .btn-success-action {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
        }
        
        .btn-warning-action {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
            border: none;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
            text-decoration: none;
        }
        
        @media (max-width: 768px) {
            .results-header {
                padding: 2rem 1rem;
            }
            
            .results-score {
                font-size: 3rem;
            }
            
            .analytics-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .answer-section {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                margin-top: 2rem;
            }
            
            .btn-action {
                display: block;
                width: 100%;
                text-align: center;
                margin: 0.5rem 0;
            }
        }
    </style>

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('asom.welcome') }}">ASOM Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.exams.show', $exam) }}">{{ $exam->title }}</a></li>
            <li class="breadcrumb-item active">Results</li>
        </ol>
    </nav>

    <!-- Results Header -->
    <div class="results-header">
        <div class="results-icon">
            @if($passed)
                <i class="fas fa-trophy"></i>
            @else
                <i class="fas fa-times-circle"></i>
            @endif
        </div>
        <div class="results-score">{{ $attempt->score }}%</div>
        <div class="results-status">
            @if($passed)
                Congratulations! You passed the exam!
            @else
                You didn't pass this time, but don't give up!
            @endif
        </div>
        <p class="mt-3 mb-0 opacity-90">
            {{ $exam->title }} • Completed on {{ $attempt->completed_at->format('M j, Y \a\t g:i A') }}
        </p>
    </div>

    <!-- Analytics Overview -->
    <div class="analytics-grid">
        <div class="analytics-card">
            <span class="analytics-number">{{ $analytics['total_questions'] }}</span>
            <span class="analytics-label">Total Questions</span>
        </div>
        <div class="analytics-card">
            <span class="analytics-number">{{ $analytics['correct_answers'] }}</span>
            <span class="analytics-label">Correct Answers</span>
        </div>
        <div class="analytics-card">
            <span class="analytics-number">{{ $analytics['accuracy_percentage'] }}%</span>
            <span class="analytics-label">Accuracy</span>
        </div>
        <div class="analytics-card">
            <span class="analytics-number">{{ gmdate('i:s', $analytics['exam_duration']) }}</span>
            <span class="analytics-label">Time Taken</span>
        </div>
    </div>

    <!-- Detailed Question Breakdown -->
    <div class="question-breakdown">
        <div class="breakdown-header">
            <h4 class="mb-2"><i class="fas fa-list-alt me-2"></i>Question-by-Question Review</h4>
            <p class="mb-0 text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                Review your answers and see which questions you got right or wrong. Correct answers are not shown to maintain exam integrity.
            </p>
        </div>
        
        @foreach($analytics['question_breakdown'] as $index => $questionData)
            <div class="question-item {{ $questionData['is_correct'] ? 'correct' : ($questionData['user_answer'] ? 'incorrect' : 'unanswered') }}">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="question-number">Question {{ $index + 1 }}</div>
                    <div class="d-flex gap-2">
                        @if($questionData['time_spent'] > 0)
                            <span class="time-badge">
                                <i class="fas fa-clock"></i>
                                {{ gmdate('i:s', $questionData['time_spent'] / 1000) }}
                            </span>
                        @endif
                        <span class="points-badge {{ $questionData['is_correct'] ? 'points-earned' : 'points-lost' }}">
                            {{ $questionData['points_earned'] }}/{{ $questionData['question']->points }} pts
                        </span>
                    </div>
                </div>
                
                <div class="question-text">
                    {!! nl2br(e($questionData['question']->question_text)) !!}
                </div>
                
                <div class="answer-section">
                    <div class="answer-box user-answer {{ $questionData['is_correct'] ? '' : 'incorrect' }}">
                        <div class="answer-label">Your Answer</div>
                        <div>
                            @if($questionData['user_answer'])
                                {{ $questionData['user_answer'] }}
                                @if($questionData['is_correct'])
                                    <i class="fas fa-check text-success ms-2"></i>
                                @else
                                    <i class="fas fa-times text-danger ms-2"></i>
                                @endif
                            @else
                                <em class="text-muted">No answer provided</em>
                                <i class="fas fa-minus text-warning ms-2"></i>
                            @endif
                        </div>
                    </div>
                    
                    <div class="answer-box {{ $questionData['is_correct'] ? 'correct-answer' : 'incorrect-feedback' }}">
                        <div class="answer-label">Result</div>
                        <div>
                            @if($questionData['is_correct'])
                                <i class="fas fa-thumbs-up text-success me-2"></i>
                                <strong class="text-success">Correct!</strong>
                                <div class="mt-1"><small class="text-muted">Well done, you got this right.</small></div>
                            @elseif($questionData['user_answer'])
                                <i class="fas fa-thumbs-down text-danger me-2"></i>
                                <strong class="text-danger">Incorrect</strong>
                                <div class="mt-1"><small class="text-muted">This answer was not correct. Review the course material for this topic.</small></div>
                            @else
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                <strong class="text-warning">Not Answered</strong>
                                <div class="mt-1"><small class="text-muted">You didn't provide an answer for this question.</small></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        @if($passed)
            <a href="{{ route('lms.exams.index') }}" class="btn-action btn-success-action">
                <i class="fas fa-graduation-cap"></i>
                View All Exams
            </a>
            <a href="{{ route('asom.welcome') }}" class="btn-action btn-primary-action">
                <i class="fas fa-tachometer-alt"></i>
                ASOM Dashboard
            </a>
        @else
            @php
                $userAttempts = \App\Models\ExamAttempt::where('user_id', Auth::id())
                    ->where('exam_id', $exam->id)
                    ->count();
                $canRetake = $userAttempts < $exam->max_attempts && $exam->allow_retakes;
            @endphp
            
            <a href="{{ route('lms.lessons.index', $exam->course->slug) }}" class="btn-action btn-primary-action">
                <i class="fas fa-book-open"></i>
                Review Course Material
            </a>
            
            @if($canRetake)
                <a href="{{ route('lms.exams.show', $exam) }}" class="btn-action btn-warning-action">
                    <i class="fas fa-redo"></i>
                    Retake Exam ({{ $exam->max_attempts - $userAttempts }} attempts left)
                </a>
            @else
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Study Tip:</strong> Review the course material thoroughly, focusing on the topics from questions you got wrong. 
                    @if($userAttempts >= $exam->max_attempts)
                        You have used all available attempts for this exam.
                    @endif
                </div>
            @endif
            
            <a href="{{ route('lms.exams.index') }}" class="btn-action btn-primary-action">
                <i class="fas fa-list"></i>
                All Exams
            </a>
        @endif
    </div>

    @if($passed)
        <!-- Celebration Animation -->
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Trigger confetti animation
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 }
                });
                
                setTimeout(() => {
                    confetti({
                        particleCount: 50,
                        angle: 60,
                        spread: 55,
                        origin: { x: 0 }
                    });
                }, 250);
                
                setTimeout(() => {
                    confetti({
                        particleCount: 50,
                        angle: 120,
                        spread: 55,
                        origin: { x: 1 }
                    });
                }, 400);
            });
        </script>
    @endif
</x-layouts.asom-auth>
