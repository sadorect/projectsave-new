<x-layouts.asom-auth page-title="Available Exams" subtitle="Complete your course assessments to demonstrate your knowledge">
    <style>
        .exam-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .exam-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .exam-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
        }
        
        .exam-content {
            padding: 2rem;
        }
        
        .attempt-history {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .score-badge {
            font-size: 1.1rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
        
        .score-pass {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        .score-fail {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
        }
        
        .exam-stats {
            display: flex;
            gap: 2rem;
            margin: 1rem 0;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
            display: block;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .btn-exam {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-exam:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            color: white;
            text-decoration: none;
        }
        
        .btn-exam.disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .exam-stats {
                gap: 1rem;
            }
            
            .exam-content {
                padding: 1.5rem;
            }
        }
    </style>

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('asom.welcome') }}">ASOM Dashboard</a></li>
            <li class="breadcrumb-item active">Available Exams</li>
        </ol>
    </nav>

    @if($completedCoursesWithExams->count() > 0)
        <div class="row">
            @foreach($completedCoursesWithExams as $course)
                @foreach($course->exams as $exam)
                    @php
                        $userAttempts = $examAttempts[$exam->id] ?? collect();
                        $lastAttempt = $userAttempts->first();
                        $attemptCount = $userAttempts->count();
                        $remainingAttempts = $exam->max_attempts - $attemptCount;
                        $canTakeExam = $remainingAttempts > 0 && ($exam->allow_retakes || !$lastAttempt || $lastAttempt->score < $exam->passing_score);
                        $bestScore = $userAttempts->max('score');
                        $hasPassed = $bestScore >= $exam->passing_score;
                    @endphp
                    
                    <div class="col-lg-6 mb-4">
                        <div class="exam-card">
                            <div class="exam-header">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h4 class="mb-1">{{ $exam->title }}</h4>
                                        <p class="mb-0 opacity-75">{{ $course->title }}</p>
                                    </div>
                                    @if($hasPassed)
                                        <div class="score-badge score-pass">
                                            <i class="fas fa-check-circle me-1"></i>Passed
                                        </div>
                                    @elseif($lastAttempt)
                                        <div class="score-badge score-fail">
                                            {{ $lastAttempt->score }}%
                                        </div>
                                    @else
                                        <div class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="exam-content">
                                @if($exam->description)
                                    <p class="text-muted mb-3">{{ $exam->description }}</p>
                                @endif
                                
                                <div class="exam-stats">
                                    <div class="stat-item">
                                        <span class="stat-number">{{ $exam->duration_minutes }}</span>
                                        <span class="stat-label">Minutes</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number">{{ $exam->questions()->count() }}</span>
                                        <span class="stat-label">Questions</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number">{{ $exam->passing_score }}%</span>
                                        <span class="stat-label">Pass Score</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number">{{ $remainingAttempts }}</span>
                                        <span class="stat-label">Attempts Left</span>
                                    </div>
                                </div>
                                
                                @if($userAttempts->count() > 0)
                                    <div class="attempt-history">
                                        <h6 class="mb-2"><i class="fas fa-history me-2"></i>Previous Attempts</h6>
                                        @foreach($userAttempts->take(3) as $attempt)
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small>{{ $attempt->completed_at ? $attempt->completed_at->format('M j, Y g:i A') : 'In Progress' }}</small>
                                                @if($attempt->completed_at)
                                                    <span class="badge {{ $attempt->score >= $exam->passing_score ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $attempt->score }}%
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">Incomplete</span>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if($userAttempts->count() > 3)
                                            <small class="text-muted">...and {{ $userAttempts->count() - 3 }} more attempts</small>
                                        @endif
                                    </div>
                                @endif
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        @if($canTakeExam)
                                            <a href="{{ route('lms.exams.show', $exam) }}" class="btn-exam">
                                                <i class="fas fa-play me-2"></i>
                                                {{ $lastAttempt ? 'Retake Exam' : 'Start Exam' }}
                                            </a>
                                        @else
                                            <button class="btn-exam disabled" disabled>
                                                <i class="fas fa-ban me-2"></i>
                                                @if($remainingAttempts <= 0)
                                                    No Attempts Left
                                                @elseif($hasPassed)
                                                    Exam Completed
                                                @else
                                                    Unavailable
                                                @endif
                                            </button>
                                        @endif
                                    </div>
                                    
                                    @if($lastAttempt && $lastAttempt->completed_at)
                                        <a href="{{ route('lms.exams.results', [$exam, $lastAttempt]) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-chart-line me-1"></i>View Results
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-graduation-cap"></i>
            <h4>No Exams Available</h4>
            <p class="mb-4">Complete your courses to unlock their final exams and earn your certificates.</p>
            <a href="{{ route('asom.welcome') }}" class="btn-exam">
                <i class="fas fa-arrow-left me-2"></i>Back to Courses
            </a>
        </div>
    @endif

    <!-- Quick Actions Sidebar -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex gap-2 flex-wrap justify-content-center">
                <a href="{{ route('asom.welcome') }}" class="btn btn-outline-primary">
                    <i class="fas fa-tachometer-alt me-2"></i>ASOM Dashboard
                </a>
                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-2"></i>Main Dashboard
                </a>
            </div>
        </div>
    </div>
</x-layouts.asom-auth>
