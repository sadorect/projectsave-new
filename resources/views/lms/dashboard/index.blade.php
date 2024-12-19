<x-layouts.lms>
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h4 class="mb-0">My Enrolled Courses</h4>
                    </div>
                    
                    <div class="card-body p-0">
                        @forelse($enrolledCourses as $course)
                            <div class="course-item p-4 border-bottom">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <img src="{{ $course->featured_image }}" 
                                             alt="{{ $course->title }}"
                                             class="img-fluid rounded">
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $course->title }}</h5>
                                        <p class="text-muted mb-2">
                                            <small>Enrolled: {{ $course->pivot->created_at->format('M d, Y') }}</small>
                                        </p>
                                        
                                        <div class="progress mb-2" style="height: 5px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ auth()->user()->getCourseProgress($course) }}%">
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                {{ number_format(auth()->user()->getCourseProgress($course), 1) }}% Complete
                                            </small>
                                            <a href="{{ route('lessons.index', $course) }}" 
                                               class="btn btn-primary btn-sm">
                                                Continue Learning
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <h5>No courses enrolled yet</h5>
                                <p class="text-muted">Start learning by enrolling in a course</p>
                                <a href="{{ route('courses.index') }}" class="btn btn-primary">
                                    Browse Courses
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Learning Progress</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Enrolled Courses</span>
                            <strong>{{ $enrolledCourses->total() }}</strong>
                        </div>
                        <!-- Add more stats here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.lms>
