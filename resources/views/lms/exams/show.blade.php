<x-layouts.asom-auth page-title="{{ $exam->title }}" subtitle="Course: {{ $exam->course->title }}">
    <style>
        .exam-detail-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .exam-info-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        
        .info-item {
            text-align: center;
            padding: 1.5rem;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
        
        .info-number {
            font-size: 2rem;
            font-weight: 700;
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .info-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .exam-content {
            padding: 2rem;
        }
        
        .attempt-card {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            border-radius: 0 10px 10px 0;
            margin-bottom: 1rem;
        }
        
        .attempt-card.passed {
            border-left-color: #28a745;
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
        }
        
        .attempt-card.failed {
            border-left-color: #dc3545;
            background: linear-gradient(135deg, #fde8e8 0%, #fff0f0 100%);
        }
        
        .score-display {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }
        
        .score-display.passed {
            color: #28a745;
        }
        
        .score-display.failed {
            color: #dc3545;
        }
        
        .btn-start-exam {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 1rem 3rem;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .btn-start-exam:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.3);
            color: white;
            text-decoration: none;
        }
        
        .btn-start-exam.retake {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        
        .btn-start-exam.retake:hover {
            box-shadow: 0 10px 25px rgba(255, 193, 7, 0.3);
        }
        
        .warning-box {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        
        .instructions-box {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 1px solid #2196f3;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        
        .sidebar-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 2rem;
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .exam-info-header,
            .exam-content {
                padding: 1.5rem;
            }
            
            .btn-start-exam {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('asom.welcome') }}">ASOM Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item active">{{ $exam->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="exam-detail-card">
                <div class="exam-info-header">
                    <h2 class="mb-3">{{ $exam->title }}</h2>
                    @if($exam->description)
                        <p class="mb-4 opacity-90">{{ $exam->description }}</p>
                    @endif
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-number">{{ $exam->duration_minutes }}</span>
                            <span class="info-label">Minutes Duration</span>
                        </div>
                        <div class="info-item">
                            <span class="info-number">{{ $exam->questions()->count() }}</span>
                            <span class="info-label">Total Questions</span>
                        </div>
                        <div class="info-item">
                            <span class="info-number">{{ $exam->passing_score }}%</span>
                            <span class="info-label">Passing Score</span>
                        </div>
                        <div class="info-item">
                            <span class="info-number">{{ $remainingAttempts ?? $exam->max_attempts }}</span>
                            <span class="info-label">Attempts Remaining</span>
                        </div>
                    </div>
                </div>
                
                <div class="exam-content">
                    @if($attempts->count() > 0)
                        <h4 class="mb-3"><i class="fas fa-history me-2"></i>Your Attempt History</h4>
                        @foreach($attempts as $attempt)
                            <div class="attempt-card {{ $attempt->completed_at && $attempt->score >= $exam->passing_score ? 'passed' : ($attempt->completed_at ? 'failed' : '') }}">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h6 class="mb-1">
                                            Attempt #{{ $loop->iteration }}
                                            @if($attempt->completed_at && $attempt->score >= $exam->passing_score)
                                                <span class="badge bg-success ms-2">Passed</span>
                                            @elseif($attempt->completed_at)
                                                <span class="badge bg-danger ms-2">Failed</span>
                                            @else
                                                <span class="badge bg-warning ms-2">Incomplete</span>
                                            @endif
                                        </h6>
                                        <small class="text-muted">
                                            @if($attempt->completed_at)
                                                Completed: {{ $attempt->completed_at->format('M j, Y g:i A') }}
                                            @else
                                                Started: {{ $attempt->started_at->format('M j, Y g:i A') }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        @if($attempt->completed_at)
                                            <div class="score-display {{ $attempt->score >= $exam->passing_score ? 'passed' : 'failed' }}">
                                                {{ $attempt->score }}%
                                            </div>
                                        @else
                                            <div class="text-warning">
                                                <i class="fas fa-clock fa-2x"></i>
                                                <div>In Progress</div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-3 text-end">
                                        @if($attempt->completed_at)
                                            <a href="{{ route('lms.exams.results', [$exam, $attempt]) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-chart-line me-1"></i>View Details
                                            </a>
                                        @else
                                            <a href="{{ route('lms.exams.take', [$exam, $attempt]) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-play me-1"></i>Continue
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    
                    @if($canRetake)
                        <div class="instructions-box">
                            <h5><i class="fas fa-info-circle me-2"></i>Exam Instructions</h5>
                            <ul class="mb-0">
                                <li>You have <strong>{{ $exam->duration_minutes }} minutes</strong> to complete the exam</li>
                                <li>The exam contains <strong>{{ $exam->questions()->count() }} questions</strong></li>
                                <li>You need <strong>{{ $exam->passing_score }}%</strong> to pass</li>
                                <li>Your answers are automatically saved as you progress</li>
                                <li>You can navigate between questions freely</li>
                                <li>Make sure you have a stable internet connection</li>
                                @if($exam->allow_retakes)
                                    <li>You can retake this exam if you don't pass</li>
                                @endif
                            </ul>
                        </div>
                        
                        @if($remainingAttempts <= 2 && $attempts->count() > 0)
                            <div class="warning-box">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Limited Attempts Remaining</h6>
                                <p class="mb-0">You have <strong>{{ $remainingAttempts }}</strong> attempt(s) remaining. Make sure you're ready before starting the exam.</p>
                            </div>
                        @endif
                        
                        <div class="text-center mt-4">
                            <form action="{{ route('lms.exams.start', $exam) }}" method="POST" onsubmit="return confirm('Are you ready to start the exam? The timer will begin immediately.')">
                                @csrf
                                <button type="submit" class="btn-start-exam {{ $attempts->count() > 0 ? 'retake' : '' }}">
                                    <i class="fas fa-play"></i>
                                    {{ $attempts->count() > 0 ? 'Start New Attempt' : 'Start Exam' }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-center py-4">
                            @if($remainingAttempts <= 0)
                                <div class="text-danger">
                                    <i class="fas fa-ban fa-3x mb-3"></i>
                                    <h5>No Attempts Remaining</h5>
                                    <p>You have used all {{ $exam->max_attempts }} allowed attempts for this exam.</p>
                                </div>
                            @elseif($lastAttempt && $lastAttempt->score >= $exam->passing_score)
                                <div class="text-success">
                                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                                    <h5>Exam Completed Successfully!</h5>
                                    <p>You have passed this exam with a score of {{ $lastAttempt->score }}%.</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="sidebar-card">
                <h5 class="mb-3"><i class="fas fa-book me-2"></i>Course Information</h5>
                <div class="mb-3">
                    <strong>Course:</strong><br>
                    <a href="{{ route('lms.lessons.index', $exam->course->slug) }}" class="text-decoration-none">
                        {{ $exam->course->title }}
                    </a>
                </div>
                @if($exam->course->instructor)
                    <div class="mb-3">
                        <strong>Instructor:</strong><br>
                        {{ $exam->course->instructor->name }}
                    </div>
                @endif
                <div class="mb-3">
                    <strong>Total Lessons:</strong><br>
                    {{ $exam->course->lessons->count() }}
                </div>
            </div>
            
            <div class="sidebar-card mt-3">
                <h6 class="mb-3">Quick Actions</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('lms.exams.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>All Exams
                    </a>
                    <a href="{{ route('lms.lessons.index', $exam->course->slug) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-book-open me-2"></i>Course Lessons
                    </a>
                    <a href="{{ route('asom.welcome') }}" class="btn btn-outline-info">
                        <i class="fas fa-tachometer-alt me-2"></i>ASOM Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.asom-auth>
