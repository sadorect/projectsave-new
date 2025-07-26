<x-layouts.app>
    <!-- Hero Section with Background Image -->
    <!--div class="hero-section page text-white" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('{{ asset('frontend/img/asom-bg.jpg') }}') center/cover;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4">Archippus School of Ministry</h1>
                    <p class="lead">Equipping Saints for Kingdom Impact</p>
                    <div class="mt-4">
                        <a href="#courses" class="btn btn-primary btn-lg">View Courses</a>
                        <a href="#program-overview" class="btn btn-outline-light btn-lg ms-2">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </div-->
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12">
                    <h2>Archippus School of Ministry</h2>
                </div>
                <div class="col-12">
                    <a>Equipping Saints for Kingdom Impact</a>
                   
                </div>
               
            </div>
        </div>
    </div>
    

    <!-- Key Features Section -->
    <div class="container my-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card text-center p-4">
                    <i class="fas fa-bible fa-3x text-primary mb-3"></i>
                    <h3>Biblical Foundation</h3>
                    <p>Deep understanding of scripture and theological principles</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center p-4">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h3>Community Learning</h3>
                    <p>Interactive learning environment with peer collaboration</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center p-4">
                    <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
                    <h3>Practical Ministry</h3>
                    <p>Hands-on training for effective ministry leadership</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Program Overview -->
    <div id="program-overview" class="bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-4">Program Overview</h2>
                    <p class="lead">Our Diploma in Ministry program provides comprehensive theological education and practical ministry training.</p>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Comprehensive Biblical Studies</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Practical Ministry Training</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Experienced Instructors</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Flexible Learning Options</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <img src="{{ asset('frontend/img/bible-study.jpg') }}" alt="Bible Study" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>

    <!-- Available Courses -->
    <div id="courses" class="container my-5">
        <h2 class="text-center mb-5">Available Courses</h2>
        <div class="row g-4">
            @foreach($courses as $course)
                <div class="col-md-4">
                    <div class="course-card h-100">
                        <div class="course-image">
                            @if($course->featured_image)
                            <img src="{{ Storage::disk('s3')->url($course->featured_image) }}" 
                                     alt="{{ $course->title }}" 
                         class="img-fluid"
                         onerror="this.src='{{ asset('frontend/img/course-placeholder.jpg') }}'; this.onerror=null;">
                @else
                    <img src="{{ asset('frontend/img/course-placeholder.jpg') }}" 
                         alt="{{ $course->title }}" 
                         class="img-fluid">
                @endif
                            <div class="course-overlay">
                                <span class="badge bg-{{ $course->status === 'published' ? 'success' : 'warning' }}">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="course-content p-4">
                            <h4>{{ $course->title }}</h4>
                            <p>{!! Str::limit($course->description, 100) !!}</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <a href="{{ route('lms.courses.show', $course->slug) }}" class="btn btn-outline-primary">View Details</a>
                                <!--small class="text-muted">By {{ $course->instructor->name }}</small-->
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            {{ $courses->links() }}
        </div>
    </div>

    <!-- Call to Action -->
    <div class="cta-section bg-primary text-white py-5">
        <div class="container text-center">
            <h3 class="mb-4">Ready to Begin Your Journey?</h3>
            <p class="lead mb-4">Join our program and equip yourself for effective ministry</p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg">Enroll Now</a>
        </div>
    </div>

    <style>
    .hero-section {
        min-height: 60vh;
        display: flex;
        align-items: center;
    }

    .feature-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .feature-card:hover {
        transform: translateY(-5px);
    }

    .course-card {
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .course-card:hover {
        transform: translateY(-5px);
    }

    .course-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .course-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .course-overlay {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .cta-section {
        background: linear-gradient(45deg, #1a237e, #283593);
    }
    </style>
</x-layouts.app>
