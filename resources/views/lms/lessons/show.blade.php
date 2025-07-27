<x-layouts.asom-auth page-title="{{ $lesson->title }}" subtitle="Lesson {{ $lesson->order ?? 1 }} of {{ $course->lessons->count() }} • {{ round($courseProgress) }}% Course Progress">
    <style>
        .lesson-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .video-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .lesson-content {
            font-size: 1.1rem;
            line-height: 1.7;
            color: #4a5568;
        }
        
        .progress-actions {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 15px;
            margin-top: 2rem;
        }
        
        .mark-complete-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .mark-complete-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.3);
        }
        
        .mark-complete-btn.completing {
            background: #6c757d;
        }
        
        .mark-complete-btn.completed {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            cursor: default;
        }
        
        .btn-next-lesson {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-next-lesson:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 123, 255, 0.3);
            color: white;
            text-decoration: none;
        }
        
        .progress-indicator {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        
        .lesson-navigation {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .lesson-nav-item {
            border: none;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: between;
        }
        
        .lesson-nav-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }
        
        .lesson-nav-item.current {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .lesson-nav-item.completed {
            background: #e8f5e8;
            color: #28a745;
        }
        
        .completion-check {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #28a745;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .progress-actions {
                text-align: center;
            }
            
            .progress-actions .btn {
                width: 100%;
                margin-bottom: 1rem;
            }
            
            .lesson-nav-item {
                padding: 0.75rem 1rem;
            }
        }
        
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .success-animation {
            animation: successPulse 0.6s ease-out;
        }
        
        @keyframes successPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('asom.welcome') }}">ASOM Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.lessons.index', $course->slug) }}">{{ $course->title }}</a></li>
            <li class="breadcrumb-item active">{{ $lesson->title }}</li>
        </ol>
    </nav>

    <div class="row">
            <div class="col-lg-8">
                <div class="lesson-card">
                    <div class="card-body p-4">
                        @if($lesson->video_url)
                            <div class="video-container">
                                <iframe src="{{ $lesson->embed_video_url }}" 
                                        frameborder="0" 
                                        allowfullscreen
                                        loading="lazy">
                                </iframe>
                            </div>
                        @endif

                        <div class="lesson-content">
                            {!! $lesson->content !!}
                        </div>
                    </div>
                </div>

                <!-- Progress Actions -->
                <div class="progress-actions">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            @if(!$lesson->isCompleted(auth()->user()))
                                <form method="POST" action="{{ route('lessons.complete', [$course->slug, $lesson->slug]) }}" class="completion-form">
                                    @csrf
                                    <button type="submit" class="mark-complete-btn">
                                        <span class="btn-text">
                                            <i class="fas fa-check-circle me-2"></i>Mark as Complete
                                        </span>
                                        <span class="loading-spinner d-none"></span>
                                    </button>
                                </form>
                            @else
                                <button class="mark-complete-btn completed" disabled>
                                    <i class="fas fa-check-circle me-2"></i>Completed ✓
                                </button>
                            @endif
                        </div>
                        <div class="col-md-6 text-md-end">
                            @if($nextLesson)
                                <a href="{{ route('lms.lessons.show', [$course->slug, $nextLesson->slug]) }}" 
                                   class="btn-next-lesson">
                                    Next Lesson <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            @else
                                <a href="{{ route('asom.welcome') }}" class="btn-next-lesson">
                                    <i class="fas fa-graduation-cap me-2"></i>Back to Dashboard
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Progress Indicator -->
                <div class="progress-indicator">
                    <h5 class="mb-3"><i class="fas fa-chart-line me-2 text-primary"></i>Course Progress</h5>
                    <div class="progress mb-3" style="height: 12px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" 
                             style="width: {{ $courseProgress }}%" 
                             aria-valuenow="{{ $courseProgress }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">{{ $course->lessons->filter(function($lesson) { return $lesson->isCompleted(auth()->user()); })->count() }} of {{ $course->lessons->count() }} completed</small>
                        <small class="fw-bold text-primary">{{ round($courseProgress) }}%</small>
                    </div>
                </div>

                <!-- Lesson Navigation -->
                <div class="lesson-navigation">
                    <div class="p-3 border-bottom">
                        <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Course Lessons</h5>
                    </div>
                    <div class="lesson-nav-list">
                        @foreach($course->lessons as $index => $courseLesson)
                            <a href="{{ route('lms.lessons.show', [$course->slug, $courseLesson->slug]) }}" 
                               class="lesson-nav-item text-decoration-none 
                                      {{ $courseLesson->id === $lesson->id ? 'current' : '' }}
                                      {{ $courseLesson->isCompleted(auth()->user()) ? 'completed' : '' }}">
                                <div class="d-flex align-items-center w-100">
                                    <div class="lesson-number me-3">
                                        @if($courseLesson->isCompleted(auth()->user()))
                                            <div class="completion-check">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        @else
                                            <div class="lesson-counter bg-light text-dark rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 24px; height: 24px; font-size: 12px; font-weight: 600;">
                                                {{ $index + 1 }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="lesson-info flex-grow-1">
                                        <div class="fw-semibold">{{ $courseLesson->title }}</div>
                                        @if($courseLesson->video_url)
                                            <small class="text-muted">
                                                <i class="fas fa-play me-1"></i>Video Lesson
                                            </small>
                                        @else
                                            <small class="text-muted">
                                                <i class="fas fa-file-text me-1"></i>Reading Material
                                            </small>
                                        @endif
                                    </div>
                                    @if($courseLesson->id === $lesson->id)
                                        <div class="current-indicator">
                                            <i class="fas fa-play-circle"></i>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-3 d-grid gap-2">
                    <a href="{{ route('lms.lessons.index', $course->slug) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-2"></i>All Lessons
                    </a>
                    <a href="{{ route('asom.welcome') }}" class="btn btn-outline-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>ASOM Dashboard
                    </a>
                </div>
            </div>
        </div>
        @include('lms.lessons._completion_modal')
    </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
      
   


    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
        @vite(['resources/js/lms-progress.js'])
    @endpush

</x-layouts.asom-auth>