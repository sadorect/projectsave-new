<x-layouts.app>
    <div class="container">
        <div class="section-header text-center">
            <p>Our Courses</p>
            <h2>Transform Your Life Through Learning</h2>
        </div>
        <div class="row">
            @foreach($courses as $course)
                <div class="col-lg-4 mb-4">
                    <div class="course-card">
                        <img src="{{ $course->featured_image }}" alt="{{ $course->title }}">
                        <h3>{{ $course->title }}</h3>
                        <p>{{ Str::limit($course->description, 150) }}</p>
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-custom">Learn More</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
