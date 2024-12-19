<x-layouts.lms>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2>{{ $lesson->title }}</h2>
                        
                        
                   @if($lesson->video_url)
                        <div class="video-container mb-4">
                            <iframe src="{{ $lesson->embed_video_url }}" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                    @endif
                                           <div class="lesson-content">
                            {!! $lesson->content !!}
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('lessons.index', $course->slug) }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left"></i> Back to Lessons
                            </a>
                            @if($nextLesson)
                                <a href="{{ route('lessons.show', [$course->slug, $nextLesson->slug]) }}" class="btn btn-primary">
                                    Next Lesson <i class="bi bi-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Course Content</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($course->lessons as $courseLesson)
                            <a href="{{ route('lessons.show', [$course->slug, $courseLesson->slug]) }}" 
                               class="list-group-item list-group-item-action {{ $courseLesson->id === $lesson->id ? 'active' : '' }}">
                                {{ $courseLesson->order }}. {{ $courseLesson->title }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.lms>
<style>
.video-container {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
    height: 0;
    overflow: hidden;
}

.video-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.lesson-content {
    font-size: 1.1rem;
    line-height: 1.6;
}

</style>