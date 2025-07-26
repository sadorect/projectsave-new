<x-layouts.lms>
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>Overall Progress</h5>
                        <div class="progress bg-light">
                            <div class="progress-bar bg-success" style="width: {{ $stats['overall_progress'] }}%">
                                {{ $stats['overall_progress'] }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Completed Courses</h5>
                        <h2>{{ $stats['completed'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>In Progress</h5>
                        <h2>{{ $stats['in_progress'] }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>My Learning</h3>
                <a href="{{ route('lms.courses.index') }}" class="btn btn-primary">Browse Courses</a>
            </div>
            <div class="card-body">
                @if($enrolledCourses->count() > 0)
                    <div class="row">
                        @foreach($enrolledCourses as $course)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    @if($course->featured_image)
                                        <img src="{{ Storage::disk('s3')->url($course->featured_image) }}" 
                                             class="card-img-top" 
                                             alt="{{ $course->title }}"
                                             onerror="this.src='{{ asset('frontend/img/course-placeholder.jpg') }}'; this.onerror=null;">
                                    @else
                                        <img src="{{ asset('frontend/img/course-placeholder.jpg') }}" 
                                             class="card-img-top" 
                                             alt="{{ $course->title }}">
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $course->title }}</h5>
                                        <div class="progress mb-3">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $course->progress }}%">
                                                {{ round($course->progress) }}%
                                            </div>
                                        </div>
                                        <a href="{{ route('lms.lessons.index', $course->slug) }}" class="btn btn-primary">
                                            {{ $course->isCompleted() ? 'Review Course' : 'Continue Learning' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <h4>You haven't enrolled in any courses yet.</h4>
                        <a href="{{ route('lms.courses.index') }}" class="btn btn-primary mt-3">Browse Available Courses</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.lms>
