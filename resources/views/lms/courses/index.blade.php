<x-layouts.app>
    <div class="container py-8">
        <div class="section-header text-center mb-5">
            <h2>Available Courses</h2>
            <p>Expand your knowledge with our comprehensive courses</p>
        </div>

        <div class="row">
            @forelse($courses as $course)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if($course->featured_image)
                            <img src="{{ $course->featured_image }}" class="card-img-top" alt="{{ $course->title }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $course->title }}</h5>
                            <p class="card-text">{{ Str::limit($course->description, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('courses.show', $course) }}" class="btn btn-primary">View Course</a>
                                <small class="text-muted">By {{ $course->instructor->name }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p>No courses available at the moment.</p>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $courses->links() }}
        </div>
    </div>
</x-layouts.app>
