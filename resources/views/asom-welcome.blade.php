<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ASOM Dashboard - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            font-family: 'Figtree', sans-serif;
        }
        
        .dashboard-container {
            padding: 1rem 0;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
        
        .welcome-stats {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .progress-overview {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }
        
        .groups-container {
            padding: 2rem;
        }
        
        .nav-tabs-custom {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 2rem;
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
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 600;
            margin: 0;
        }
        
        .stat-card p {
            margin: 0;
            opacity: 0.9;
        }
        
        .course-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .course-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .group-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .group-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .group-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .group-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        
        .group-description {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }
        
        .whatsapp-btn {
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .instructions {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 0 5px 5px 0;
        }
        
        .verification-notice {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 0 5px 5px 0;
        }
        
        .dashboard-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }
        
        .progress-ring {
            width: 120px;
            height: 120px;
            margin: 0 auto;
        }
        
        .progress-circle {
            fill: none;
            stroke: #e9ecef;
            stroke-width: 8;
        }
        
        .progress-circle.filled {
            stroke: #28a745;
            stroke-linecap: round;
            transition: stroke-dasharray 0.6s ease;
        }
        
        .achievement-badge {
            background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
            color: white;
            border-radius: 50px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-block;
            margin: 0.25rem;
        }
        
        .btn-dashboard {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-dashboard:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
            text-decoration: none;
        }
        
        .btn-verify {
            background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-verify:hover {
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 1.5rem 0;
                text-align: center;
            }
            
            .dashboard-header h1 {
                font-size: 1.8rem;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
            
            .stat-card h3 {
                font-size: 2rem;
            }
            
            .nav-tabs-custom .nav-link {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }
            
            .course-card {
                margin-bottom: 1rem;
            }
            
            .progress-ring {
                width: 100px;
                height: 100px;
            }
            
            .group-card {
                margin-bottom: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .dashboard-container {
                padding: 0.5rem 0;
            }
            
            .welcome-stats,
            .progress-overview {
                padding: 1rem;
            }
            
            .nav-tabs-custom .nav-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
            }
            
            .stat-card h3 {
                font-size: 1.8rem;
            }
            
            .course-card,
            .group-card {
                padding: 1rem;
            }
        }
        
        /* Interactive Enhancements */
        .stat-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .course-card:hover .course-icon {
            transform: scale(1.1);
            transition: transform 0.3s ease;
        }
        
        .nav-tabs-custom .nav-link {
            transition: all 0.3s ease;
        }
        
        .nav-tabs-custom .nav-link:hover {
            color: #667eea;
            border-bottom-color: rgba(102, 126, 234, 0.3);
        }
        
        .course-card.enrolled {
            border-left: 4px solid #28a745;
            background: linear-gradient(135deg, #ffffff 0%, #f8fff8 100%);
        }
        
        .course-card.available:hover {
            border-left: 4px solid #667eea;
        }
        
        .btn-group .btn-check:checked + .btn {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2"><i class="fas fa-graduation-cap me-3"></i>ASOM Dashboard</h1>
                    <p class="mb-0 opacity-75">Welcome back, {{ Auth::user()->name }}! Continue your ministry training journey.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('user.dashboard') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-home me-2"></i>Main Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
@include('components.alerts')

    <div class="dashboard-container">
        <div class="container">
            <!-- Quick Stats Overview -->
            <div class="welcome-stats">
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card bg-primary">
                            <h3>{{ $stats['total_courses'] }}</h3>
                            <p>Total Courses</p>
                            <small><i class="fas fa-book-open me-1"></i>Available Modules</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-success">
                            <h3>{{ $stats['completed_courses'] }}</h3>
                            <p>Completed</p>
                            <small><i class="fas fa-check-circle me-1"></i>Finished Courses</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-warning">
                            <h3>{{ $stats['in_progress_courses'] }}</h3>
                            <p>In Progress</p>
                            <small><i class="fas fa-clock me-1"></i>Active Learning</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-info">
                            <h3>{{ $stats['overall_progress'] }}%</h3>
                            <p>Overall Progress</p>
                            <small><i class="fas fa-chart-line me-1"></i>Completion Rate</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs nav-tabs-custom" id="asomTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                        <i class="fas fa-tachometer-alt me-2"></i>Overview
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button" role="tab">
                        <i class="fas fa-book me-2"></i>Courses
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="groups-tab" data-bs-toggle="tab" data-bs-target="#groups" type="button" role="tab">
                        <i class="fab fa-whatsapp me-2"></i>WhatsApp Groups
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="exams-tab" data-bs-toggle="tab" data-bs-target="#exams" type="button" role="tab">
                        <i class="fas fa-clipboard-check me-2"></i>Exams
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="achievements-tab" data-bs-toggle="tab" data-bs-target="#achievements" type="button" role="tab">
                    <i class="fas fa-trophy me-2"></i>Achievements
                    </button>
                <button class="nav-link" id="certificates-tab" data-bs-toggle="tab" data-bs-target="#certificates" type="button" role="tab">
                    <i class="fas fa-certificate me-2"></i>Certificates
                </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="asomTabContent">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="progress-overview">
                                <h4 class="mb-4"><i class="fas fa-chart-line me-2 text-primary"></i>Learning Progress</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="progress-ring mb-3">
                                            <svg width="120" height="120">
                                                <circle class="progress-circle" cx="60" cy="60" r="52"></circle>
                                                <circle class="progress-circle filled" cx="60" cy="60" r="52" 
                                                        style="stroke-dasharray: 0 327; stroke-dashoffset: 0"></circle>
                                                <text x="60" y="65" text-anchor="middle" style="font-size: 24px; font-weight: bold; fill: #333;">{{ $stats['overall_progress'] }}%</text>
                                            </svg>
                                        </div>
                                        <h6 class="text-center">Overall Completion</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-3">Recent Activity</h6>
                                        <div class="activity-item mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="activity-icon bg-primary text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-user-plus fa-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="mb-0 fw-semibold">Welcome to ASOM!</p>
                                                    <small class="text-muted">You've successfully enrolled in the program</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h6 class="mb-3">Next Steps</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                                    <i class="fas fa-envelope-circle-check fa-2x text-warning me-3"></i>
                                                    <div>
                                                        <h6 class="mb-1">
                                                            @if(!Auth::user()->hasVerifiedEmail())
                                                                Verify Your Email
                                                            @else
                                                                ✓ Email Verified
                                                            @endif
                                                        </h6>
                                                        <small class="text-muted">
                                                            @if(!Auth::user()->hasVerifiedEmail())
                                                                Required to access course groups
                                                            @else
                                                                You can now access all features
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                                    <i class="fab fa-whatsapp fa-2x text-success me-3"></i>
                                                    <div>
                                                        <h6 class="mb-1">Join Course Groups</h6>
                                                        <small class="text-muted">Connect with instructors and students</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="progress-overview">
                                <h5 class="mb-4"><i class="fas fa-clipboard-check me-2 text-warning"></i>Exam Status</h5>
                                
                                <!-- Exam Stats -->
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="text-center p-2 bg-primary text-white rounded">
                                            <h6 class="mb-0">{{ $examData['available_exams'] }}</h6>
                                            <small>Available</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center p-2 bg-success text-white rounded">
                                            <h6 class="mb-0">{{ $examData['passed_exams'] }}</h6>
                                            <small>Passed</small>
                                        </div>
                                    </div>
                                </div>

                                @if(count($examData['pending_exams']) > 0)
                                    <h6 class="mb-3">Pending Exams</h6>
                                    @foreach($examData['pending_exams'] as $exam)
                                        <div class="upcoming-item mb-3 p-3 bg-light rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="date-badge bg-warning text-white rounded p-2 me-3 text-center" style="min-width: 50px;">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $exam['title'] }}</h6>
                                                    <small class="text-muted">{{ $exam['course'] }} • {{ $exam['duration'] }}min</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-graduation-cap fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0 small">Complete courses to unlock exams</p>
                                    </div>
                                @endif

                                @if($examData['recent_results']->count() > 0)
                                    <h6 class="mb-3 mt-4">Recent Results</h6>
                                    @foreach($examData['recent_results']->take(2) as $result)
                                        <div class="upcoming-item mb-2 p-2 bg-light rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="date-badge {{ $result['passed'] ? 'bg-success' : 'bg-danger' }} text-white rounded p-2 me-3 text-center" style="min-width: 40px;">
                                                    <i class="fas {{ $result['passed'] ? 'fa-check' : 'fa-times' }}"></i>
                                                </div>
                                                <div>
                                                    <small class="fw-bold">{{ $result['exam_title'] }}</small><br>
                                                    <small class="text-muted">{{ $result['score'] }}% • {{ $result['completed_at'] }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Courses Tab -->
                <div class="tab-pane fade" id="courses" role="tabpanel">
                    <div class="row">
                        <div class="col-12">
                            <!-- Sub Navigation for Courses -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0"><i class="fas fa-book me-2 text-primary"></i>ASOM Courses</h4>
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="courseFilter" id="myCoursesBtn" autocomplete="off" checked>
                                    <label class="btn btn-outline-primary" for="myCoursesBtn">My Courses ({{ $enrolledCoursesWithProgress->count() }})</label>
                                    
                                    <input type="radio" class="btn-check" name="courseFilter" id="allCoursesBtn" autocomplete="off">
                                    <label class="btn btn-outline-primary" for="allCoursesBtn">All Courses ({{ $allCourses->count() }})</label>
                                </div>
                            </div>
                            
                            <!-- My Enrolled Courses -->
                            <div id="myCourses" class="course-section">
                                @if($enrolledCoursesWithProgress->count() > 0)
                                    <div class="row">
                                        @foreach($enrolledCoursesWithProgress as $course)
                                        <div class="col-lg-6 mb-4">
                                            <div class="course-card enrolled">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="course-icon bg-success text-white rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="{{ $course['icon'] }}"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1">{{ $course['name'] }}</h5>
                                                        <small class="text-muted">{{ $course['lessons'] }} lesson{{ $course['lessons'] != 1 ? 's' : '' }} • Enrolled {{ $course['enrolled_at'] ? \Carbon\Carbon::parse($course['enrolled_at'])->diffForHumans() : '' }}</small>
                                                    </div>
                                                    @if($course['progress'] > 0)
                                                        <span class="badge bg-success">{{ $course['progress'] }}% Complete</span>
                                                    @else
                                                        <span class="badge bg-info">Just Started</span>
                                                    @endif
                                                </div>
                                                <div class="progress mb-3" style="height: 8px;">
                                                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: {{ $course['progress'] }}%"></div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    @if($course['lessons'] > 0)
                                                        <a href="{{ route('lms.lessons.index', $course['slug']) }}" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-play me-1"></i>{{ $course['progress'] > 0 ? 'Continue Learning' : 'Start Course' }}
                                                        </a>
                                                    @else
                                                        <button class="btn btn-secondary btn-sm" disabled>
                                                            <i class="fas fa-clock me-1"></i>Content Coming Soon
                                                        </button>
                                                    @endif
                                                    <small class="text-muted">{{ $course['instructor'] }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No Enrolled Courses Yet</h5>
                                        <p class="text-muted">Enroll in ASOM courses to start your ministry training journey.</p>
                                        <button class="btn btn-outline-primary" onclick="document.getElementById('allCoursesBtn').click()">
                                            <i class="fas fa-search me-2"></i>Browse All Courses
                                        </button>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- All Available Courses -->
                            <div id="allCourses" class="course-section d-none">
                                <div class="row">
                                    @foreach($allCourses as $course)
                                    <div class="col-lg-6 mb-4">
                                        <div class="course-card {{ $course['is_enrolled'] ? 'enrolled' : 'available' }}">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="course-icon {{ $course['is_enrolled'] ? 'bg-success' : 'bg-primary' }} text-white rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="{{ $course['icon'] }}"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1">{{ $course['name'] }}</h5>
                                                    <small class="text-muted">{{ $course['lessons'] }} lesson{{ $course['lessons'] != 1 ? 's' : '' }}</small>
                                                </div>
                                                @if($course['is_enrolled'])
                                                    @if($course['progress'] > 0)
                                                        <span class="badge bg-success">{{ $course['progress'] }}% Complete</span>
                                                    @else
                                                        <span class="badge bg-info">Enrolled</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Available</span>
                                                @endif
                                            </div>
                                            
                                            @if($course['is_enrolled'])
                                                <div class="progress mb-3" style="height: 8px;">
                                                    <div class="progress-bar progress-bar-striped" style="width: {{ $course['progress'] }}%"></div>
                                                </div>
                                            @endif
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                @if($course['is_enrolled'])
                                                    @if($course['lessons'] > 0)
                                                        <a href="{{ route('lms.lessons.index', $course['slug']) }}" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-play me-1"></i>{{ $course['progress'] > 0 ? 'Continue' : 'Start Course' }}
                                                        </a>
                                                    @else
                                                        <button class="btn btn-secondary btn-sm" disabled>
                                                            <i class="fas fa-clock me-1"></i>Content Coming Soon
                                                        </button>
                                                    @endif
                                                @else
                                                    <form action="{{ route('lms.courses.enroll', $course['slug']) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-plus me-1"></i>Enroll Now
                                                        </button>
                                                    </form>
                                                @endif
                                                <small class="text-muted">{{ $course['instructor'] }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp Groups Tab -->
                <div class="tab-pane fade" id="groups" role="tabpanel">
                    <div class="groups-container p-0">
                            @if(session('verified') || session('success') )
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Email Verified!</strong> Your account has been successfully verified and have been enrolled in the ASOM program. You can now join all WhatsApp groups below.
                                </div>
                            @endif

                            @if(!Auth::user()->hasVerifiedEmail())
                                <div class="verification-notice">
                                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Email Verification Required</h5>
                                    <p class="mb-2">
                                        Welcome, <strong>{{ Auth::user()->name }}</strong>! You can see your course groups below, but to join them and access all ASOM features, 
                                        please verify your email address first.
                                    </p>
                                    <a href="{{ route('verification.notice') }}" class="btn-verify">
                                        <i class="fas fa-envelope-circle-check me-2"></i>Verify Email
                                    </a>
                                </div>
                            @else
                                <div class="instructions">
                                    <h5><i class="fas fa-info-circle me-2"></i>Getting Started</h5>
                                    <p class="mb-0">
                                        Welcome, <strong>{{ Auth::user()->name }}</strong>! To begin your ASOM journey, please join the WhatsApp groups for your courses below. 
                                        These groups are where you'll receive course materials, interact with faculty, and connect with fellow students.
                                    </p>
                                </div>
                            @endif
                            
                            <h4 class="mb-4"><i class="fab fa-whatsapp me-2 text-success"></i>Your Course Groups</h4>
                            
                            <div class="row">
                                @foreach($whatsappGroups as $group)
                                <div class="col-md-6 col-lg-4">
                                    @if(Auth::user()->hasVerifiedEmail())
                                        <a href="{{ $group['url'] }}" target="_blank" class="group-card">
                                    @else
                                        <div class="group-card" style="opacity: 0.7; cursor: not-allowed;">
                                    @endif
                                        <div class="group-icon">
                                            <i class="{{ $group['icon'] }}"></i>
                                        </div>
                                        <div class="group-name">{{ $group['name'] }}</div>
                                        <div class="group-description">{{ $group['description'] }}</div>
                                        <span class="whatsapp-btn">
                                            <i class="fab fa-whatsapp"></i>
                                            @if(Auth::user()->hasVerifiedEmail())
                                                Join Group
                                            @else
                                                Verify Email First
                                            @endif
                                        </span>
                                    @if(Auth::user()->hasVerifiedEmail())
                                        </a>
                                    @else
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            
                            <div class="alert alert-info mt-4">
                                <h6><i class="fas fa-lightbulb me-2"></i>Important Notes:</h6>
                                <ul class="mb-0">
                                    <li>Join all relevant course groups for your program</li>
                                    <li>Make sure to introduce yourself when you join</li>
                                    <li>Keep group discussions respectful and on-topic</li>
                                    <li>Check the Info Desk group for general announcements</li>
                                    @if(!Auth::user()->hasVerifiedEmail())
                                        <li><strong>Remember to verify your email to access all features!</strong></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exams Tab -->
                <div class="tab-pane fade" id="exams" role="tabpanel">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="mb-4"><i class="fas fa-clipboard-check me-2 text-primary"></i>ASOM Examinations</h4>
                            
                            <!-- Exam Stats Overview -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="stat-card bg-primary">
                                        <h3>{{ $examData['available_exams'] }}</h3>
                                        <p>Available Exams</p>
                                        <small><i class="fas fa-clipboard-list me-1"></i>Ready to Take</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-card bg-success">
                                        <h3>{{ $examData['passed_exams'] }}</h3>
                                        <p>Passed Exams</p>
                                        <small><i class="fas fa-check-circle me-1"></i>Successfully Completed</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-card bg-warning">
                                        <h3>{{ $examData['pending_exams']->count() }}</h3>
                                        <p>Pending Exams</p>
                                        <small><i class="fas fa-clock me-1"></i>Awaiting Completion</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-card bg-info">
                                        <h3>{{ $examData['completed_exams'] }}</h3>
                                        <p>Total Attempts</p>
                                        <small><i class="fas fa-chart-line me-1"></i>Overall Progress</small>
                                    </div>
                                </div>
                            </div>

                            @if($examData['available_exams'] > 0)
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="progress-overview">
                                            <h5 class="mb-4">Available Exams</h5>
                                            @if(count($examData['pending_exams']) > 0)
                                                @foreach($examData['pending_exams'] as $exam)
                                                    <div class="course-card mb-3">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="course-icon bg-warning text-white rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-file-alt"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h5 class="mb-1">{{ $exam['title'] }}</h5>
                                                                <small class="text-muted">{{ $exam['course'] }} • {{ $exam['questions'] }} questions • {{ $exam['duration'] }} minutes</small>
                                                            </div>
                                                            <span class="badge bg-warning">Available</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <a href="{{ route('lms.exams.show', $exam['id']) }}" class="btn btn-primary btn-sm">
                                                                <i class="fas fa-play me-1"></i>Take Exam
                                                            </a>
                                                            <small class="text-muted">Course: {{ $exam['course'] }}</small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">No Exams Available Yet</h5>
                                                    <p class="text-muted">Complete your courses to unlock exams!</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <div class="progress-overview">
                                            <h5 class="mb-4">Recent Results</h5>
                                            @if($examData['recent_results']->count() > 0)
                                                @foreach($examData['recent_results'] as $result)
                                                    <div class="course-card mb-3 {{ $result['passed'] ? 'border-success' : 'border-danger' }}">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="course-icon {{ $result['passed'] ? 'bg-success' : 'bg-danger' }} text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas {{ $result['passed'] ? 'fa-check' : 'fa-times' }}"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-0">{{ $result['exam_title'] }}</h6>
                                                                <small class="text-muted">{{ $result['course_title'] }}</small>
                                                            </div>
                                                            <span class="badge {{ $result['passed'] ? 'bg-success' : 'bg-danger' }}">{{ $result['score'] }}%</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="text-muted">{{ $result['completed_at'] }}</small>
                                                            <a href="{{ route('lms.exams.results', [$result['exam_id'], $result['attempt_id']]) }}" class="btn btn-outline-primary btn-sm">
                                                                <i class="fas fa-eye me-1"></i>View Results
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="fas fa-chart-line fa-2x text-muted mb-3"></i>
                                                    <p class="text-muted mb-0">No exam results yet. Take your first exam to see results here!</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-graduation-cap fa-4x text-muted mb-4"></i>
                                    <h4 class="text-muted mb-3">Complete Courses to Unlock Exams</h4>
                                    <p class="text-muted mb-4">Exams become available after you complete 100% of a course. Start learning to unlock your first exam!</p>
                                    <a href="#courses" onclick="document.getElementById('courses-tab').click()" class="btn btn-primary btn-lg">
                                        <i class="fas fa-book me-2"></i>Browse Courses
                                    </a>
                                </div>
                            @endif

                            <div class="alert alert-info mt-4">
                                <h6><i class="fas fa-info-circle me-2"></i>Exam Information:</h6>
                                <ul class="mb-0">
                                    <li>Exams are unlocked after completing 100% of a course</li>
                                    <li>Each exam has a time limit and passing score requirement</li>
                                    <li>You can view detailed results and feedback after completion</li>
                                    <li>Multiple attempts may be allowed depending on the exam settings</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Achievements Tab -->
                <div class="tab-pane fade" id="achievements" role="tabpanel">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="mb-4"><i class="fas fa-trophy me-2 text-primary"></i>Your Achievements</h4>
                            
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="progress-overview">
                                        <h5 class="mb-4">Available Badges</h5>
                                        <div class="row">
                                            <div class="col-md-4 mb-4">
                                                <div class="text-center p-4 {{ $achievements['first_course'] ? 'bg-success text-white' : 'bg-light' }} rounded">
                                                    <i class="fas fa-medal fa-3x {{ $achievements['first_course'] ? 'text-white' : 'text-muted' }} mb-3"></i>
                                                    <h6>First Course</h6>
                                                    <p class="{{ $achievements['first_course'] ? 'text-white' : 'text-muted' }} small mb-2">Complete your first ASOM course</p>
                                                    <span class="badge {{ $achievements['first_course'] ? 'bg-light text-success' : 'bg-secondary' }}">
                                                        {{ $achievements['first_course'] ? 'Unlocked' : 'Locked' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <div class="text-center p-4 {{ $achievements['bible_scholar'] ? 'bg-success text-white' : 'bg-light' }} rounded">
                                                    <i class="fas fa-book-reader fa-3x {{ $achievements['bible_scholar'] ? 'text-white' : 'text-muted' }} mb-3"></i>
                                                    <h6>Bible Scholar</h6>
                                                    <p class="{{ $achievements['bible_scholar'] ? 'text-white' : 'text-muted' }} small mb-2">Complete Bible Introduction & Hermeneutics</p>
                                                    <span class="badge {{ $achievements['bible_scholar'] ? 'bg-light text-success' : 'bg-secondary' }}">
                                                        {{ $achievements['bible_scholar'] ? 'Unlocked' : 'Locked' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <div class="text-center p-4 {{ $achievements['community_builder'] ? 'bg-success text-white' : 'bg-light' }} rounded">
                                                    <i class="fas fa-users fa-3x {{ $achievements['community_builder'] ? 'text-white' : 'text-muted' }} mb-3"></i>
                                                    <h6>Community Builder</h6>
                                                    <p class="{{ $achievements['community_builder'] ? 'text-white' : 'text-muted' }} small mb-2">Verify email and join community</p>
                                                    <span class="badge {{ $achievements['community_builder'] ? 'bg-light text-success' : 'bg-secondary' }}">
                                                        {{ $achievements['community_builder'] ? 'Unlocked' : 'Locked' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <div class="text-center p-4 {{ $achievements['preacher'] ? 'bg-success text-white' : 'bg-light' }} rounded">
                                                    <i class="fas fa-microphone fa-3x {{ $achievements['preacher'] ? 'text-white' : 'text-muted' }} mb-3"></i>
                                                    <h6>Preacher</h6>
                                                    <p class="{{ $achievements['preacher'] ? 'text-white' : 'text-muted' }} small mb-2">Complete Homiletics course</p>
                                                    <span class="badge {{ $achievements['preacher'] ? 'bg-light text-success' : 'bg-secondary' }}">
                                                        {{ $achievements['preacher'] ? 'Unlocked' : 'Locked' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <div class="text-center p-4 {{ $achievements['counselor'] ? 'bg-success text-white' : 'bg-light' }} rounded">
                                                    <i class="fas fa-hands-helping fa-3x {{ $achievements['counselor'] ? 'text-white' : 'text-muted' }} mb-3"></i>
                                                    <h6>Counselor</h6>
                                                    <p class="{{ $achievements['counselor'] ? 'text-white' : 'text-muted' }} small mb-2">Complete Biblical Counseling</p>
                                                    <span class="badge {{ $achievements['counselor'] ? 'bg-light text-success' : 'bg-secondary' }}">
                                                        {{ $achievements['counselor'] ? 'Unlocked' : 'Locked' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <div class="text-center p-4 {{ $achievements['graduate'] ? 'bg-success text-white' : 'bg-light' }} rounded">
                                                    <i class="fas fa-graduation-cap fa-3x {{ $achievements['graduate'] ? 'text-white' : 'text-muted' }} mb-3"></i>
                                                    <h6>ASOM Graduate</h6>
                                                    <p class="{{ $achievements['graduate'] ? 'text-white' : 'text-muted' }} small mb-2">Complete all ASOM courses</p>
                                                    <span class="badge {{ $achievements['graduate'] ? 'bg-light text-success' : 'bg-secondary' }}">
                                                        {{ $achievements['graduate'] ? 'Unlocked' : 'Locked' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- filepath: resources/views/asom-welcome.blade.php --}}
<div class="col-lg-4">
    <div class="progress-overview">
        <h5 class="mb-4">Progress Milestones</h5>
        @php
            $progress = $stats['overall_progress'];
        @endphp
        <div class="milestone-item mb-3 p-3 bg-light rounded {{ $progress >= 25 ? '' : 'opacity-50' }}">
            <div class="d-flex align-items-center">
                <div class="milestone-icon {{ $progress >= 25 ? 'bg-success' : 'bg-secondary' }} text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas {{ $progress >= 25 ? 'fa-unlock' : 'fa-lock' }} fa-sm"></i>
                </div>
                <div>
                    <h6 class="mb-1">25% Complete</h6>
                    <small class="text-muted">First milestone reward</small>
                </div>
            </div>
        </div>
        <div class="milestone-item mb-3 p-3 bg-light rounded {{ $progress >= 50 ? '' : 'opacity-50' }}">
            <div class="d-flex align-items-center">
                <div class="milestone-icon {{ $progress >= 50 ? 'bg-success' : 'bg-secondary' }} text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas {{ $progress >= 50 ? 'fa-unlock' : 'fa-lock' }} fa-sm"></i>
                </div>
                <div>
                    <h6 class="mb-1">50% Complete</h6>
                    <small class="text-muted">Halfway there!</small>
                </div>
            </div>
        </div>
        <div class="milestone-item mb-3 p-3 bg-light rounded {{ $progress >= 75 ? '' : 'opacity-50' }}">
            <div class="d-flex align-items-center">
                <div class="milestone-icon {{ $progress >= 75 ? 'bg-success' : 'bg-secondary' }} text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas {{ $progress >= 75 ? 'fa-unlock' : 'fa-lock' }} fa-sm"></i>
                </div>
                <div>
                    <h6 class="mb-1">75% Complete</h6>
                    <small class="text-muted">Almost there!</small>
                </div>
            </div>
        </div>
        <div class="milestone-item mb-3 p-3 bg-light rounded {{ $progress >= 100 ? '' : 'opacity-50' }}">
            <div class="d-flex align-items-center">
                <div class="milestone-icon {{ $progress >= 100 ? 'bg-success' : 'bg-secondary' }} text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas {{ $progress >= 100 ? 'fa-unlock' : 'fa-lock' }} fa-sm"></i>
                </div>
                <div>
                    <h6 class="mb-1">100% Complete</h6>
                    <small class="text-muted">ASOM Graduate!</small>
                </div>
            </div>
        </div>
    </div>
</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificates Tab -->
    <div class="tab-pane fade" id="certificates" role="tabpanel">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h4 class="mb-4"><i class="fas fa-certificate me-2 text-primary"></i>Your Certificates</h4>
                    
                    @php
                        $userCertificates = Auth::user()->certificates()->with('course')->orderBy('issued_at', 'desc')->get();
                    @endphp
                    
                    @if($userCertificates->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-certificate text-muted mb-3" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mb-3">No Certificates Yet</h5>
                            <p class="text-muted mb-4">Complete courses and pass exams to earn certificates</p>
                            <a href="#courses-tab" class="btn btn-primary" onclick="switchTab('courses')">
                                <i class="fas fa-book me-2"></i>View Courses
                            </a>
                        </div>
                    @else
                        <div class="row">
                            @foreach($userCertificates as $certificate)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="fas fa-certificate text-primary me-3" style="font-size: 2rem;"></i>
                                                <div>
                                                    <h6 class="card-title mb-1">{{ $certificate->course->title }}</h6>
                                                    <small class="text-muted">{{ $certificate->certificate_id }}</small>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                @if($certificate->is_approved)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Approved
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>Pending Approval
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <div class="small text-muted mb-3">
                                                <div><strong>Issued:</strong> {{ $certificate->issued_at->format('M j, Y') }}</div>
                                                @if($certificate->final_grade)
                                                    <div><strong>Grade:</strong> {{ number_format($certificate->final_grade, 1) }}%</div>
                                                @endif
                                            </div>
                                            
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('lms.certificates.show', $certificate) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-2"></i>View Certificate
                                                </a>
                                                @if($certificate->is_approved)
                                                    <a href="{{ route('lms.certificates.download', $certificate) }}" 
                                                       class="btn btn-success btn-sm">
                                                        <i class="fas fa-download me-2"></i>Download PDF
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('lms.certificates.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-2"></i>View All Certificates
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle direct tab navigation from URL hash
            const hash = window.location.hash;
            if (hash && hash.includes('-tab')) {
                const tabId = hash.replace('#', '').replace('-tab', '');
                const tabTrigger = document.querySelector(`#${tabId}-tab`);
                if (tabTrigger) {
                    const tab = new bootstrap.Tab(tabTrigger);
                    tab.show();
                }
            }
            
            // Animate progress ring with real data
            const progressRing = document.querySelector('.progress-circle.filled');
            if (progressRing) {
                const progress = {{ $stats['overall_progress'] }};
                const circumference = 2 * Math.PI * 52; // radius = 52
                progressRing.style.strokeDasharray = circumference;
                progressRing.style.strokeDashoffset = circumference;
                
                // Animate to actual progress
                setTimeout(() => {
                    const offset = circumference - (progress / 100) * circumference;
                    progressRing.style.strokeDashoffset = offset;
                }, 500);
            }
            
            // Add click analytics for course cards (placeholder)
            document.querySelectorAll('.course-card').forEach(card => {
                card.addEventListener('click', function() {
                    // This can be enhanced to track course interactions
                    console.log('Course card clicked:', this.querySelector('h5').textContent);
                });
            });
            
            // Tab change event handling
            document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    // Update URL hash when tab changes
                    const targetId = e.target.getAttribute('data-bs-target').replace('#', '');
                    window.location.hash = targetId.replace(targetId.split('-')[0], targetId.split('-')[0] + '-tab');
                });
            });
            
            // Mobile-friendly card interactions
            if (window.innerWidth <= 768) {
                document.querySelectorAll('.course-card, .group-card').forEach(card => {
                    card.addEventListener('touchstart', function() {
                        this.style.transform = 'translateY(-2px)';
                    });
                    
                    card.addEventListener('touchend', function() {
                        setTimeout(() => {
                            this.style.transform = '';
                        }, 150);
                    });
                });
            }
        });
        
        // Function to update progress (can be called when real data is available)
        function updateProgress(percentage) {
            const progressRing = document.querySelector('.progress-circle.filled');
            const progressText = document.querySelector('.progress-ring text');
            
            if (progressRing && progressText) {
                const circumference = 2 * Math.PI * 52;
                const offset = circumference - (percentage / 100) * circumference;
                
                progressRing.style.strokeDashoffset = offset;
                progressText.textContent = percentage + '%';
            }
        }
        
        // Handle course filter toggle
        const myCoursesBtn = document.getElementById('myCoursesBtn');
        const allCoursesBtn = document.getElementById('allCoursesBtn');
        const myCoursesSection = document.getElementById('myCourses');
        const allCoursesSection = document.getElementById('allCourses');
        
        if (myCoursesBtn && allCoursesBtn) {
            myCoursesBtn.addEventListener('change', function() {
                if (this.checked) {
                    myCoursesSection.classList.remove('d-none');
                    allCoursesSection.classList.add('d-none');
                }
            });
            
            allCoursesBtn.addEventListener('change', function() {
                if (this.checked) {
                    allCoursesSection.classList.remove('d-none');
                    myCoursesSection.classList.add('d-none');
                }
            });
        }
    </script>
</body>
</html>
