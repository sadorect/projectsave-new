<x-layouts.lms>
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-3">{{ $lesson->title }}</h4>
                        
                        <x-video-player :lesson="$lesson" />
                        
                        <div class="mt-4">
                            {!! $lesson->content !!}
                        </div>
                        
                        <div class="mt-4 border-top pt-4">
                            <button id="mark-complete" 
                                    class="btn btn-success" 
                                    data-lesson-id="{{ $lesson->id }}"
                                    {{ auth()->user()->lessonProgress()->where('lesson_id', $lesson->id)->where('completed', true)->exists() ? 'disabled' : '' }}>
                                {{ auth()->user()->lessonProgress()->where('lesson_id', $lesson->id)->where('completed', true)->exists() ? 'Completed' : 'Mark as Complete' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Course Content</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($course->lessons()->orderBy('order')->get() as $courseLesson)
                            <a href="{{ route('lessons.show', [$course, $courseLesson]) }}" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
                               {{ $lesson->id === $courseLesson->id ? 'active' : '' }}">
                                {{ $courseLesson->title }}
                                @if(auth()->user()->lessonProgress()->where('lesson_id', $courseLesson->id)->where('completed', true)->exists())
                                    <span class="badge bg-success rounded-pill">✓</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/lesson-progress.js') }}"></script>
    @endpush
</x-layouts.lms>
