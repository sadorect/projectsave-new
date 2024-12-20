<x-layouts.lms>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h1>{{ $course->title }}</h1>
                <p>{{ $course->description }}</p>
                
                @if($course->featured_image)
                    <img src="{{ $course->featured_image }}" alt="{{ $course->title }}" class="img-fluid mb-4">
                @endif

                <div class="course-actions mt-4">
                    @if(auth()->user()->courses->contains($course))
                        <a href="{{ route('lms.lessons.index', $course->slug) }}" class="btn btn-primary">Continue Learning</a>
                        <form action="{{ route('lms.courses.unenroll', $course->slug) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Unenroll</button>
                        </form>
                    @else
                        <form action="{{ route('lms.courses.enroll', $course->slug) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">Enroll Now</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.lms>
