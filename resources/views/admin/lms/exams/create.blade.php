@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Create New Exam</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.exams.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select" required>
                        <option value="">Select a course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" class="form-control" required min="1">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Passing Score (%)</label>
                            <input type="number" name="passing_score" class="form-control" required min="0" max="100">
                        </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Maximum Attempts Allowed</label>
                            <input type="number" name="max_attempts" class="form-control" value="1" min="1">
                            <small class="text-muted">Number of times a student can take this exam</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="allow_retakes" class="form-check-input" id="allowRetakes" 
                                       value="1" {{ isset($exam) && $exam->allow_retakes ? 'checked' : '' }}>
                                <label class="form-check-label" for="allowRetakes">
                                    Allow Retakes After Failed Attempts
                                </label>
                                <small class="text-muted d-block">If checked, students can retry even after using all attempts if they haven't passed</small>
                            </div>
                        </div>
                    </div> 
                </div> 
            </div>
        </div>

                <div class="text-end">
                    <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.querySelector('select[name="course_id"]').addEventListener('change', function() {
        const courseSelect = this;
        const selectedOption = courseSelect.options[courseSelect.selectedIndex];
        const titleInput = document.querySelector('input[name="title"]');
        
        if (selectedOption.value) {
            titleInput.value = selectedOption.text + ' - Final Exam';
        }
    });
</script>
@endpush

@endsection

