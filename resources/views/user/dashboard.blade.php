@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Welcome back, {{ auth()->user()->name }}!</h1>
                    <p class="text-muted mb-0">Here's what's happening with your account.</p>
                </div>
                <div class="text-end">
                    <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-handshake fa-2x text-primary"></i>
                    </div>
                    <h5 class="card-title">Partnership Status</h5>
                    <p class="card-text h4 text-success mb-0">Active</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-praying-hands fa-2x text-info"></i>
                    </div>
                    <h5 class="card-title">Prayer Points</h5>
                    <p class="card-text h4 text-info mb-0">0</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-bell fa-2x text-warning"></i>
                    </div>
                    <h5 class="card-title">Notifications</h5>
                    <p class="card-text h4 text-warning mb-0">3</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-graduation-cap fa-2x text-success"></i>
                    </div>
                    <h5 class="card-title">ASOM Status</h5>
                    <p class="card-text h4 text-success mb-0">
                        {{ auth()->user()->user_type === 'asom_student' ? 'Enrolled' : 'Available' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="row">
        <!-- Recent Activities -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Activities</h5>
                    <small class="text-muted">Last 7 days</small>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Profile Updated</h6>
                                <p class="text-muted mb-1">You updated your profile information</p>
                                <small class="text-muted">2 days ago</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Partnership Activated</h6>
                                <p class="text-muted mb-1">Your partnership status was activated</p>
                                <small class="text-muted">5 days ago</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Welcome Email Sent</h6>
                                <p class="text-muted mb-1">Welcome email was sent to your inbox</p>
                                <small class="text-muted">1 week ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ASOM Information Card -->
            <!-- Update the ASOM Information Card section -->
@if(auth()->user()->user_type !== 'asom_student')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-gradient-primary text-white">
        <h5 class="card-title mb-0">
            <i class="fas fa-graduation-cap me-2"></i>Archippus School of Ministry (ASOM)
        </h5>
    </div>
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="mb-2">Transform Your Ministry Journey</h6>
                <p class="text-muted mb-3">
                    Join our comprehensive ministry training program designed to equip you with biblical knowledge, 
                    practical skills, and spiritual formation for effective ministry.
                </p>
                <ul class="list-unstyled mb-0">
                    <li><i class="fas fa-check text-success me-2"></i>Biblical Studies & Hermeneutics</li>
                    <li><i class="fas fa-check text-success me-2"></i>Ministry Skills & Leadership</li>
                    <li><i class="fas fa-check text-success me-2"></i>Spiritual Formation & Counseling</li>
                    <li><i class="fas fa-check text-success me-2"></i>Interactive WhatsApp Learning Groups</li>
                </ul>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-graduation-cap fa-4x text-primary mb-3"></i>
                <div>
                    <form action="{{ route('asom.join') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('Are you ready to join ASOM and begin your ministry training journey?')">
                            <i class="fas fa-rocket me-2"></i>Join ASOM
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<!-- Show this for ASOM students -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-gradient-success text-white">
        <h5 class="card-title mb-0">
            <i class="fas fa-graduation-cap me-2"></i>ASOM Student Dashboard
        </h5>
    </div>
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="mb-2">Welcome to ASOM!</h6>
                <p class="text-muted mb-3">
                    You are now enrolled in the Archippus School of Ministry. Access your course materials 
                    and join your WhatsApp groups to begin your learning journey.
                </p>
                <div class="d-flex gap-2">
                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Enrolled</span>
                    <span class="badge bg-info"><i class="fas fa-users me-1"></i>Access to Groups</span>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-graduation-cap fa-4x text-success mb-3"></i>
                <div class="d-flex gap-2 flex-wrap justify-content-center">
                    <a href="{{ route('asom.welcome') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-tachometer-alt me-2"></i>ASOM Dashboard
                    </a>
                    <a href="{{ route('asom.welcome') }}#groups-tab" class="btn btn-success btn-lg">
                        <i class="fab fa-whatsapp me-2"></i>Course Groups
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

        </div>

        <!-- Quick Actions Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('user.profile') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user me-2"></i>Update Profile
                        </a>
                        <a href="#" class="btn btn-outline-info">
                            <i class="fas fa-praying-hands me-2"></i>Submit Prayer Request
                        </a>
                        <a href="#" class="btn btn-outline-success">
                            <i class="fas fa-calendar me-2"></i>View Events
                        </a>
                        @if(auth()->user()->user_type === 'asom_student')
                            <a href="{{ route('asom.welcome') }}" class="btn btn-outline-primary mb-2 w-100">
                                <i class="fas fa-graduation-cap me-2"></i>ASOM Dashboard
                            </a>
                            <a href="{{ route('asom.welcome') }}#groups-tab" class="btn btn-outline-success">
                                <i class="fab fa-whatsapp me-2"></i>Course Groups
                            </a>
                        @else
                        <form action="{{ route('asom.join') }}" method="POST" class="d-inline" onsubmit="return confirm('Join ASOM now?')">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                            <button type="submit" class="btn btn-outline-warning w-100" >
                                <i class="fas fa-graduation-cap me-2"></i>Join ASOM
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('user.files') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-folder me-2"></i>My Files
                        </a>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Upcoming Events</h5>
                </div>
                <div class="card-body">
                    <div class="event-item mb-3">
                        <div class="d-flex">
                            <div class="event-date text-center me-3">
                                <div class="bg-primary text-white rounded p-2">
                                    <div class="fw-bold">15</div>
                                    <small>MAR</small>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Prayer Conference</h6>
                                <p class="text-muted mb-0 small">Join us for a powerful time of prayer and worship</p>
                            </div>
                        </div>
                    </div>
                    <div class="event-item mb-3">
                        <div class="d-flex">
                            <div class="event-date text-center me-3">
                                <div class="bg-success text-white rounded p-2">
                                    <div class="fw-bold">22</div>
                                    <small>MAR</small>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">ASOM Graduation</h6>
                                <p class="text-muted mb-0 small">Celebrating our ministry graduates</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="#" class="btn btn-sm btn-outline-primary">View All Events</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -21px;
    top: 20px;
    height: calc(100% + 10px);
    width: 2px;
    background-color: #e9ecef;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.event-date {
    min-width: 50px;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endsection
