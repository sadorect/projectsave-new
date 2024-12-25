<x-layouts.lms>
<div class="container">
    <h2 class="mb-4">Available Courses</h2>
    
    <div class="row">
        @foreach($courses as $course)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($course->featured_image)
                        <img src="{{ $course->featured_image }}" class="card-img-top" alt="{{ $course->title }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $course->title }}</h5>
                        <p class="card-text">{!! Str::limit($course->description, 150) !!}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('lms.courses.show', $course->slug) }}" class="btn btn-primary">View Course</a>
                            <small class="text-muted">By {{ $course->instructor->name }}</small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    {{ $courses->links() }}
</div>
</x-layouts.lms>