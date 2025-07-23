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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Maximum Attempts Allowed</label>
                                    <input type="number" name="max_attempts" class="form-control" value="{{ $exam->max_attempts }}"  min="1">
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
                       
                        

                        <div class="text-end">
                            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Exam</button>
                            <a href="{{ route('admin.exams.preview', $exam) }}" class="btn btn-info me-2">
                              <i class="bi bi-eye"></i> Preview Exam
                          </a>
                        </div>
                    </form>
                </div>

                 <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Exam Status</h5>
                            <form action="{{ route('admin.exams.toggle-activation', $exam) }}" 
                                method="POST" 
                                class="d-inline exam-activation-form" 
                                data-exam-id="{{ $exam->id }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn toggle-activation btn-{{ $exam->is_active ? 'success' : 'secondary' }}">
                                    <i class="bi bi-toggle-{{ $exam->is_active ? 'on' : 'off' }}"></i>
                                    <span>{{ $exam->is_active ? 'Active' : 'Inactive' }}</span>
                                </button>
                                
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
                                    </div>
                                
                                <div>
                                  <span class="badge bg-primary">{{ $question->points }} pts</span>
                                  <a href="{{ route('admin.questions.edit', [$exam, $question]) }}" class="btn btn-sm btn-primary ms-2">
                                      <i class="bi bi-pencil"></i>
                                  </a>
                                  <form action="{{ route('admin.questions.destroy', [$exam, $question]) }}" method="POST" class="d-inline">
                                      @csrf
                                      @method('DELETE')
                                      <button type="submit" class="btn btn-sm btn-danger ms-1" onclick="return confirm('Are you sure you want to delete this question?')">
                                          <i class="bi bi-trash"></i>
                                      </button>
                                  </form>
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
@push('scripts')
<script>
    $(document).ready(function() {
        $('.toggle-activation').click(function(e) {
            e.preventDefault();
            const button = $(this);
            const form = button.closest('form');
            
            $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize() + '&is_active=' + (button.hasClass('btn-success') ? '0' : '1'),
            success: function(response) {
                const isActive = !button.hasClass('btn-success');
                button.toggleClass('btn-success btn-secondary');
                button.find('i').toggleClass('bi-toggle-on bi-toggle-off');
                button.find('span').text(isActive ? 'Active' : 'Inactive');
                
                toastr.success(response.message);
            }
        });

        });
    });
    </script>
    
    @endpush
@endsection


