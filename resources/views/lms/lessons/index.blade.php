<x-layouts.lms>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h2>{{ $course->title }}</h2>
                <div class="card">
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($lessons as $lesson)
                                <a href="{{ route('lessons.show', [$course->slug, $lesson->slug]) }}" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">{{ $lesson->order }}. {{ $lesson->title }}</h5>
                                        @if($lesson->video_url)
                                            <small><i class="bi bi-camera-video"></i> Video Lesson</small>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Course Progress</h5>
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                        <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-outline-primary w-100">
                            Course Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.lms>
