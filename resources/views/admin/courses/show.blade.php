@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Courses
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h2>{{ $course->title }}</h2>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="badge bg-{{ $course->status === 'published' ? 'success' : ($course->status === 'draft' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($course->status) }}
                        </span>
                        <small class="text-muted">Created {{ $course->created_at->format('M d, Y') }}</small>
                    </div>

                    @if($course->featured_image)
                        <img src="{{ $course->featured_image }}" alt="{{ $course->title }}" class="img-fluid mb-4 rounded">
                    @endif

                    <div class="course-description mb-4">
                        {{ $course->description }}
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Edit Course
                        </a>
                        <a href="{{ route('admin.lessons.create', ['course_id' => $course->id]) }}" class="btn btn-success">
                            <i class="bi bi-plus-lg"></i> Add Lesson
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Course Lessons</h4>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($course->lessons()->orderBy('order')->get() as $lesson)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>{{ $lesson->order }}. {{ $lesson->title }}</span>
                                    <a href="{{ route('admin.lessons.edit', $lesson) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">No lessons added yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
