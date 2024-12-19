<x-layouts.app>
    <div class="container py-8">
        <div class="row">
            <!-- Video and Content Column -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="text-2xl font-bold mb-4">{{ $lesson->title }}</h1>
                        
                        <x-video-player :lesson="$lesson" />
                        
                        <div class="lesson-content mt-6">
                            {!! $lesson->content !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar with Course Progress -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3>Course Content</h3>
                    </div>
                    <div class="card-body">
                        <div class="lesson-list">
                            @foreach($course->lessons()->orderBy('order')->get() as $courseLesson)
                                <div class="lesson-item p-3 {{ $lesson->id === $courseLesson->id ? 'bg-light' : '' }}">
                                    <a href="{{ route('lessons.show', [$course, $courseLesson]) }}" 
                                       class="text-decoration-none">
                                        {{ $courseLesson->title }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
