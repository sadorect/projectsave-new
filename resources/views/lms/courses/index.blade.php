<x-layouts.lms>
    <div class="container">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Available Courses</h4>
                    <div class="input-group w-auto">
                        <input type="text" class="form-control" placeholder="Search courses...">
                        <button class="btn btn-outline-secondary" type="button">Search</button>
                    </div>
                </div>
            </div>
            
            @forelse($courses as $course)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        @if($course->featured_image)
                            <img src="{{ $course->featured_image }}" 
                                 class="card-img-top" 
                                 alt="{{ $course->title }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $course->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($course->description, 100) }}</p>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">By {{ $course->instructor->name }}</small>
                                <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm">View Course</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        No courses available at the moment.
                    </div>
                </div>
            @endforelse
            
            <div class="col-12 mt-4">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
</x-layouts.lms>
