<x-layouts.asom-auth page-title="{{ $course->title }}" subtitle="{{ $course->description ?? 'Master the fundamentals and advance your ministry skills' }}">
    <style>
        .lesson-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .lesson-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        
        .lesson-item {
            padding: 1.5rem;
            border: none;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: all 0.3s ease;
        }
        
        .lesson-item:hover {
            color: inherit;
            text-decoration: none;
        }
        
        .lesson-item.completed {
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
            border-left: 4px solid #28a745;
        }
        
        .lesson-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .lesson-number.completed {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .lesson-meta {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            display: inline-block;
        }
        
        .progress-sidebar {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            padding: 2rem;
            position: sticky;
            top: 2rem;
        }
        
        .course-stats {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
        }
        
        @media (max-width: 768px) {
            .lesson-item {
                padding: 1rem;
            }
            
            .lesson-number {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            
            .progress-sidebar {
                margin-top: 2rem;
                position: static;
            }
        }
    </style>

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('asom.welcome') }}">ASOM Dashboard</a></li>
            <li class="breadcrumb-item active">{{ $course->title }}</li>
        </ol>
    </nav>

    <div class="row">
            <div class="col-lg-8">
                <!-- Course Stats -->
                <div class="course-stats">
                    <div class="row">
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number">{{ $lessons->count() }}</div>
                                <div class="text-muted">Lessons</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number">{{ $lessons->filter(function($lesson) { return $lesson->isCompleted(auth()->user()); })->count() }}</div>
                                <div class="text-muted">Completed</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number">{{ $courseProgress ?? 0 }}%</div>
                                <div class="text-muted">Progress</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lessons List -->
                <div class="lessons-container">
                    @forelse($lessons as $index => $lesson)
                        <div class="lesson-card {{ $lesson->isCompleted(auth()->user()) ? 'completed' : '' }}">
                            <a href="{{ route('lms.lessons.show', [$course->slug, $lesson->slug]) }}" 
                               class="lesson-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="lesson-number {{ $lesson->isCompleted(auth()->user()) ? 'completed' : '' }}">
                                            @if($lesson->isCompleted(auth()->user()))
                                                <i class="fas fa-check"></i>
                                            @else
                                                {{ $lesson->order ?? $index + 1 }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="mb-1 fw-semibold">{{ $lesson->title }}</h5>
                                                <p class="text-muted mb-2 small">
                                                    {{ Str::limit(strip_tags($lesson->content), 120) }}
                                                </p>
                                                <div class="lesson-meta-container">
                                                    @if($lesson->video_url)
                                                        <span class="lesson-meta text-primary">
                                                            <i class="fas fa-play me-1"></i>Video Lesson
                                                        </span>
                                                    @else
                                                        <span class="lesson-meta text-info">
                                                            <i class="fas fa-file-text me-1"></i>Reading Material
                                                        </span>
                                                    @endif
                                                    
                                                    @if($lesson->isCompleted(auth()->user()))
                                                        <span class="lesson-meta text-success ms-2">
                                                            <i class="fas fa-check-circle me-1"></i>Completed
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="lesson-action">
                                                @if($lesson->isCompleted(auth()->user()))
                                                    <div class="text-success">
                                                        <i class="fas fa-check-circle fa-2x"></i>
                                                    </div>
                                                @else
                                                    <div class="text-primary">
                                                        <i class="fas fa-play-circle fa-2x"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No lessons available yet</h4>
                            <p class="text-muted">Lessons for this course will be added soon.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="progress-sidebar">
                    <h5 class="mb-4"><i class="fas fa-chart-line me-2 text-primary"></i>Course Progress</h5>
                    
                    <!-- Progress Bar -->
                    <div class="progress mb-3" style="height: 12px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" 
                             style="width: {{ $courseProgress ?? 0 }}%" 
                             aria-valuenow="{{ $courseProgress ?? 0 }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <small class="text-muted">{{ $lessons->filter(function($lesson) { return $lesson->isCompleted(auth()->user()); })->count() }} of {{ $lessons->count() }} completed</small>
                        <small class="fw-bold text-primary">{{ $courseProgress ?? 0 }}%</small>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="d-grid gap-2 mb-4">
                        @if($lessons->count() > 0)
                            @php
                                $nextLesson = $lessons->filter(function($lesson) { return !$lesson->isCompleted(auth()->user()); })->first() ?? $lessons->first();
                            @endphp
                            <a href="{{ route('lms.lessons.show', [$course->slug, $nextLesson->slug]) }}" 
                               class="btn btn-primary btn-lg">
                                <i class="fas fa-play me-2"></i>
                                {{ $lessons->filter(function($lesson) { return $lesson->isCompleted(auth()->user()); })->count() > 0 ? 'Continue Learning' : 'Start Course' }}
                            </a>
                        @endif
                        
                        <a href="{{ route('lms.courses.show', $course->slug) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-info-circle me-2"></i>Course Details
                        </a>
                        
                        <a href="{{ route('asom.welcome') }}" class="btn btn-outline-primary">
                            <i class="fas fa-tachometer-alt me-2"></i>ASOM Dashboard
                        </a>
                    </div>
                    
                    <!-- Course Info -->
                    <div class="border-top pt-3">
                        <h6 class="text-muted mb-3">Course Information</h6>
                        <div class="mb-2">
                            <small class="text-muted">Instructor:</small><br>
                            <span class="fw-semibold">{{ $course->instructor->name ?? 'ASOM Team' }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Total Lessons:</small><br>
                            <span class="fw-semibold">{{ $lessons->count() }}</span>
                        </div>
                        @if($course->created_at)
                            <div>
                                <small class="text-muted">Published:</small><br>
                                <span class="fw-semibold">{{ $course->created_at->format('M j, Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
</x-layouts.asom-auth>
