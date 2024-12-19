<x-layouts.app>
    <div class="container py-8">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h2>My Courses</h2>
                    </div>
                    <div class="card-body">
                        @forelse($enrolledCourses as $course)
                            <div class="course-item border-bottom p-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        @if($course->featured_image)
                                            <img src="{{ $course->featured_image }}" 
                                                 class="img-fluid rounded" 
                                                 alt="{{ $course->title }}">
                                        @endif
                                    </div>
                                    <div class="col-md-8">
                                        <h3>{{ $course->title }}</h3>
                                        <p class="text-muted">Enrolled: {{ $course->pivot->created_at->format('M d, Y') }}</p>
                                        
                                        <div class="mt-3">
                                            <a href="{{ route('lessons.index', $course) }}" 
                                               class="btn btn-primary">
                                                Continue Learning
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p>You haven't enrolled in any courses yet.</p>
                                <a href="{{ route('courses.index') }}" class="btn btn-primary">
                                    Browse Courses
                                </a>
                            </div>
                        @endforelse

                        <div class="mt-4">
                            {{ $enrolledCourses->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3>Learning Progress</h3>
                    </div>
                    <div class="card-body">
                        <p>Total Courses: {{ $enrolledCourses->total() }}</p>
                        <!-- Add more stats here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
