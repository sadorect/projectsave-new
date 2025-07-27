<x-layouts.app>
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('asom') }}">ASOM</a></li>
                            <li class="breadcrumb-item active">{{ $course->title }}</li>
                        </ol>
                    </nav>
                    <h1>{{ $course->title }}</h1>
                    @if($course->instructor)
                        <p class="text-muted">Instructor: {{ $course->instructor->name }}</p>
                    @endif
                </div>
                <div class="col-md-4 text-md-end">
                    @if($isEnrolled)
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="fas fa-check-circle me-2"></i>Enrolled
                        </span>
                    @else
                        <span class="badge bg-primary fs-6 px-3 py-2">
                            <i class="fas fa-graduation-cap me-2"></i>Available
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('components.alerts')

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="course-detail-card">
                    @if($course->featured_image)
                        <div class="course-image-container">
                            <img src="{{ Storage::disk('s3')->url($course->featured_image) }}" 
                                 alt="{{ $course->title }}" 
                                 class="img-fluid course-image"
                                 onerror="this.src='{{ asset('frontend/img/course-placeholder.jpg') }}'; this.onerror=null;">
                        </div>
                    @else
                        <div class="course-image-container">
                            <img src="{{ asset('frontend/img/course-placeholder.jpg') }}" 
                                 alt="{{ $course->title }}" 
                                 class="img-fluid course-image">
                        </div>
                    @endif

                    <!-- Course Overview Tabs -->
                    <div class="course-tabs mb-4">
                        <ul class="nav nav-tabs nav-tabs-custom" id="courseTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#description">
                                    <i class="fas fa-info-circle me-2"></i>Description
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#objectives">
                                    <i class="fas fa-bullseye me-2"></i>Objectives
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#outcomes">
                                    <i class="fas fa-trophy me-2"></i>Outcomes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#evaluation">
                                    <i class="fas fa-clipboard-check me-2"></i>Evaluation
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content course-tab-content">
                            <div class="tab-pane fade show active" id="description">
                                <div class="course-content">
                                    {!! $course->description !!}
                                </div>
                            </div>
                            <div class="tab-pane fade" id="objectives">
                                <div class="course-content">
                                    {!! $course->objectives !!}
                                </div>
                            </div>
                            <div class="tab-pane fade" id="outcomes">
                                <div class="course-content">
                                    {!! $course->outcomes !!}
                                </div>
                            </div>
                            <div class="tab-pane fade" id="evaluation">
                                <div class="course-content">
                                    {!! $course->evaluation !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Resources Section -->
                    @if($isEnrolled)
                        <div class="course-resources">
                            <h4 class="mb-4"><i class="fas fa-book-reader me-2"></i>Course Resources</h4>
                            
                            <!-- Recommended Books -->
                            @if($course->recommended_books)
                                <div class="resource-card mb-4">
                                    <div class="resource-header">
                                        <h5 class="mb-0"><i class="fas fa-book me-2"></i>Recommended Books</h5>
                                    </div>
                                    <div class="resource-content">
                                        {!! $course->recommended_books !!}
                                    </div>
                                </div>
                            @endif

                            <!-- Course Materials -->
                            @if($course->documents)
                                <div class="resource-card">
                                    <div class="resource-header">
                                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Course Materials</h5>
                                    </div>
                                    <div class="resource-content">
                                        <div class="row">
                                            @foreach(json_decode($course->documents) as $document)
                                                <div class="col-md-6 mb-3">
                                                    <div class="document-card">
                                                        <i class="fas fa-file-pdf document-icon"></i>
                                                        <div class="document-info">
                                                            <h6>{!! $document->name !!}</h6>
                                                            <small class="text-muted">{{ number_format($document->size / 1048576, 2) }} MB</small>
                                                            <a href="{{ $document->path }}" class="btn btn-sm btn-primary mt-2" download>
                                                                <i class="fas fa-download me-1"></i>Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="course-sidebar">
                    <!-- Course Stats -->
                    <div class="sidebar-card mb-4">
                        <h5 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Course Info</h5>
                        <div class="course-stats">
                            <div class="stat-item mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Total Lessons:</span>
                                    <strong>{{ $course->lessons->count() }}</strong>
                                </div>
                            </div>
                            @if($course->instructor)
                                <div class="stat-item mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Instructor:</span>
                                        <strong>{{ $course->instructor->name }}</strong>
                                    </div>
                                </div>
                            @endif
                            <div class="stat-item mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Status:</span>
                                    <span class="badge bg-{{ $course->status === 'published' ? 'success' : 'warning' }}">
                                        {{ ucfirst($course->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enrollment Actions -->
                    <div class="sidebar-card">
                        <div class="d-grid gap-2">
                            @if($isEnrolled)
                                <a href="{{ route('lms.lessons.index', $course->slug) }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-play me-2"></i>Continue Learning
                                </a>
                                @auth
                                    <a href="{{ route('asom.welcome') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-tachometer-alt me-2"></i>ASOM Dashboard
                                    </a>
                                @endauth
                                <form action="{{ route('lms.courses.unenroll', $course->slug) }}" method="POST" class="mt-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Are you sure you want to unenroll from this course?')">
                                        <i class="fas fa-sign-out-alt me-2"></i>Unenroll
                                    </button>
                                </form>
                            @else
                                @auth
                                    <form action="{{ route('lms.courses.enroll', $course->slug) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-lg w-100">
                                            <i class="fas fa-graduation-cap me-2"></i>Enroll Now
                                        </button>
                                    </form>
                                    <a href="{{ route('asom.welcome') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-tachometer-alt me-2"></i>ASOM Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('register') }}" class="btn btn-success btn-lg">
                                        <i class="fas fa-user-plus me-2"></i>Register to Enroll
                                    </a>
                                    <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login
                                    </a>
                                @endauth
                            @endif
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="sidebar-card mt-4">
                        <h6 class="mb-3">Quick Links</h6>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('asom') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-home me-2"></i>All ASOM Courses
                            </a>
                            @if($isEnrolled && $course->lessons->count() > 0)
                                <a href="{{ route('lms.lessons.index', $course->slug) }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-list me-2"></i>Course Lessons
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0 2rem;
            margin-bottom: 0;
        }
        
        .page-header .breadcrumb {
            background: rgba(255,255,255,0.1);
            border-radius: 25px;
            padding: 0.5rem 1rem;
        }
        
        .page-header .breadcrumb-item a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }
        
        .page-header .breadcrumb-item.active {
            color: white;
        }
        
        .course-detail-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .course-image-container {
            position: relative;
            height: 300px;
            overflow: hidden;
        }
        
        .course-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .nav-tabs-custom {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 0;
        }
        
        .nav-tabs-custom .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: #6c757d;
            font-weight: 500;
            padding: 1rem 1.5rem;
        }
        
        .nav-tabs-custom .nav-link.active {
            color: #667eea;
            border-bottom-color: #667eea;
            background: none;
        }
        
        .course-tab-content {
            background: white;
            padding: 2rem;
            border-radius: 0 0 15px 15px;
        }
        
        .course-content {
            font-size: 1.1rem;
            line-height: 1.7;
            color: #4a5568;
        }
        
        .resource-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .resource-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .resource-content {
            padding: 1.5rem;
        }
        
        .document-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .document-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .document-icon {
            font-size: 2.5rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        
        .sidebar-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 2rem;
        }
        
        .course-stats .stat-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .course-stats .stat-item:last-child {
            border-bottom: none;
        }
        
        .list-group-item {
            border: none;
            border-radius: 10px !important;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .list-group-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }
        
        .btn {
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-lg {
            padding: 0.75rem 2rem;
        }
        
        @media (max-width: 768px) {
            .page-header {
                padding: 2rem 0 1rem;
                text-align: center;
            }
            
            .course-image-container {
                height: 200px;
            }
            
            .nav-tabs-custom .nav-link {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }
            
            .course-tab-content {
                padding: 1.5rem;
            }
            
            .sidebar-card {
                margin-top: 2rem;
            }
        }
    </style>
</x-layouts.app>
