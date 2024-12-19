<x-layouts.lms>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h1>{{ $course->title }}</h1>
                <p>{{ $course->description }}</p>
                
                @if($course->featured_image)
                    <img src="{{ $course->featured_image }}" alt="{{ $course->title }}" class="img-fluid mb-4">
                @endif
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="course-actions">
                            @if(auth()->user()->courses->contains($course))
                                <a href="{{ route('lessons.index', $course->slug) }}" class="btn btn-primary w-100 mb-2">Continue Learning</a>
                                <form action="{{ route('courses.unenroll', $course->slug) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to unenroll?')">
                                        Unenroll
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('courses.enroll', $course->slug) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">Enroll Now</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.lms>
