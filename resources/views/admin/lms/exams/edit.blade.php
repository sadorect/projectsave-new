@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Exam: {{ $exam->title }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.exams.update', $exam) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Course</label>
                            <select name="course_id" class="form-select" required>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ $exam->course_id == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $exam->title }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $exam->description }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Duration (minutes)</label>
                                    <input type="number" name="duration_minutes" class="form-control" value="{{ $exam->duration_minutes }}" required min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Passing Score (%)</label>
                                    <input type="number" name="passing_score" class="form-control" value="{{ $exam->passing_score }}" required min="0" max="100">
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Exam</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Questions</h3>
                    <a href="{{ route('admin.questions.create', $exam) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus"></i> Add Question
                    </a>
                </div>
                <div class="card-body">
                    @if($exam->questions->count() > 0)
                        <ul class="list-group">
                            @foreach($exam->questions as $question)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>{{ $question->question_text }}</div>
                                        <span class="badge bg-primary">{{ $question->points }} pts</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted text-center">No questions added yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
