<x-layouts.lms>
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>Total Courses</h5>
                        <h2>{{ $stats['total'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Completed</h5>
                        <h2>{{ $stats['completed'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>In Progress</h5>
                        <h2>{{ $stats['in_progress'] }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>My Courses</h3>
            </div>
            <div class="card-body">
                @if($enrolledCourses->count() > 0)
                    <div class="row">
                        @foreach($enrolledCourses as $course)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    @if($course->featured_image)
                                        <img src="{{ $course->featured_image }}" class="card-img-top" alt="{{ $course->title }}">
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $course->title }}</h5>
                                        <p class="card-text">{{ Str::limit($course->description, 100) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="{{ route('lessons.index', $course->slug) }}" class="btn btn-primary">Continue Learning</a>
                                            <span class="badge bg-{{ $course->pivot->status === 'completed' ? 'success' : 'info' }}">
                                                {{ ucfirst($course->pivot->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <h4>You haven't enrolled in any courses yet.</h4>
                        <a href="{{ route('courses.index') }}" class="btn btn-primary mt-3">Browse Courses</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.lms>
