<x-layouts.app>
  <!-- Page Header Start -->
  <div class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2>Archippus School of Ministry</h2>
            </div>
            <div class="col-12">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('asom') }}">Asom</a>
            </div>
        </div>
    </div>
</div>
  <!-- Hero Section >
  <div class="hero-section bg-primary text-white py-5">
      <div class="container">
          <div class="row align-items-center">
              <div class="col-md-8">
                  <h1>Archippus School of Ministry</h1>
                  <p class="lead">Diploma in Ministry Program</p>
                  <p>Equipping saints for the work of ministry through comprehensive biblical education</p>
              </div>
              <div class="col-md-4 text-center">
                  <img src="{{ asset('images/logo.png') }}" alt="School Logo" class="img-fluid">
              </div>
          </div>
      </div>
  </div-->

  <!-- Program Overview -->
  <div class="container my-5">
      <div class="row">
          <div class="col-md-8">
              <h2>Program Overview</h2>
              <p>Our Diploma in Ministry program is designed to provide comprehensive theological education and practical ministry training. Upon completion, graduates receive a recognized Diploma in Ministry certificate.</p>
          </div>
          <div class="col-md-4">
              <div class="card">
                  <div class="card-body">
                      <h5 class="card-title">Program Highlights</h5>
                      <ul class="list-unstyled">
                          <li><i class="fa fa-check text-success"></i> Comprehensive Biblical Studies</li>
                          <li><i class="fa fa-check text-success"></i> Practical Ministry Training</li>
                          <li><i class="fa fa-check text-success"></i> Experienced Instructors</li>
                          <li><i class="fa fa-check text-success"></i> Flexible Learning</li>
                      </ul>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Available Courses -->
  <div class="container mb-5">
      <h2 class="mb-4">Available Courses</h2>
      <div class="row">
          @foreach($courses as $course)
              <div class="col-md-4 mb-4">
                  <div class="card h-100 shadow-sm">
                      @if($course->featured_image)
                          <img src="{{ $course->featured_image }}" class="card-img-top" alt="{{ $course->title }}">
                      @endif
                      <div class="card-body">
                          <h5 class="card-title">{{ $course->title }}</h5>
                          <p class="card-text">{{ Str::limit($course->description, 100) }}</p>
                          <div class="d-flex justify-content-between align-items-center">
                              <a href="{{ route('lms.courses.show', $course->slug) }}" class="btn btn-outline-primary">View Course</a>
                              <small class="text-muted">Instructor: {{ $course->instructor->name }}</small>
                          </div>
                      </div>
                  </div>
              </div>
          @endforeach
      </div>
      
      {{ $courses->links() }}
  </div>

  <!-- Call to Action -->
  <div class="bg-light py-5">
      <div class="container">
          <div class="row justify-content-center text-center">
              <div class="col-md-8">
                  <h3>Ready to Begin Your Journey?</h3>
                  <p>Join our program and equip yourself for effective ministry</p>
                  <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Enroll Now</a>
              </div>
          </div>
      </div>
  </div>

  <style>
  .hero-section {
      background: linear-gradient(45deg, #1a237e, #283593);
  }
  .card {
      transition: transform 0.3s ease;
  }
  .card:hover {
      transform: translateY(-5px);
  }
  </style>
</x-layouts.app>
