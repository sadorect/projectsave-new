<x-layouts.lms>
    <div class="container py-8">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    @if($course->featured_image)
                        <img src="{{ $course->featured_image }}" class="card-img-top" alt="{{ $course->title }}">
                    @endif
                    <div class="card-body">
                        <h1 class="card-title">{{ $course->title }}</h1>
                        <p class="text-muted">Instructor: {{ $course->instructor->name }}</p>
                        
                        <div class="course-description mt-4">
                            {!! $course->description !!}
                        </div>
                        @auth
                            @if(auth()->user()->isEnrolledIn($course))
                                <div class="mt-4 d-flex gap-2">
                                    <a href="{{ route('lessons.index', $course) }}" class="btn btn-primary">
                                        Continue Learning
                                    </a>
                                    <form action="{{ route('courses.unenroll', $course) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('Are you sure you want to unenroll from this course?')">
                                            Unenroll
                                        </button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('courses.enroll', $course) }}" method="POST" class="mt-4">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Enroll Now</button>
                                </form>
                            @endif
                        @endauth
                        
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3>Course Content</h3>
                    </div>
                    <div class="card-body">
                        <div class="lesson-list">
                            @foreach($course->lessons()->orderBy('order')->get() as $lesson)
                                <div class="lesson-item p-2">
                                    {{ $lesson->title }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.lms>
