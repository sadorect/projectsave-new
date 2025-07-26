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
                                        allowfullscreen>
                                </iframe>
                            </div>
                        @endif

                        <div class="lesson-content mb-4">
                            {!! $lesson->content !!}
                        </div>

                        <div class="progress-actions">
                            @if(!$lesson->isCompleted(auth()->user()))
                                <form method="POST" action="{{ route('lessons.complete', [$course->id, $lesson->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        Mark as Complete
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-success disabled">Completed</button>
                            @endif

                            @if($nextLesson)
                                <a href="{{ route('lms.lessons.show', [$course->slug, $nextLesson->slug]) }}" 
                                   class="btn btn-primary float-end">
                                    Next Lesson <i class="bi bi-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Course Progress</h5>
                    </div>
                    <div class="card-body">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $courseProgress }}%" 
                                 aria-valuenow="{{ $courseProgress }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $courseProgress }}%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>Lesson List</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($course->lessons as $courseLesson)
                            <a href="{{ route('lms.lessons.show', [$course->slug, $courseLesson->slug]) }}" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center 
                                      {{ $courseLesson->id === $lesson->id ? 'active' : '' }}">
                                {{ $courseLesson->title }}
                                @if($courseLesson->isCompleted(auth()->user()))
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @include('lms.lessons._completion_modal')
    </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
      
   


    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
        @vite(['resources/js/lms-progress.js'])
    @endpush

</x-layouts.lms>