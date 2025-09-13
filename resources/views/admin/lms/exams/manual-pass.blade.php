@extends('admin.layouts.app')

@section('title', 'Manual Pass - ' . $exam->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-check me-2"></i>
                        Manually Pass Student for: {{ $exam->title }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Exams
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('admin.exams.manual-pass.store', $exam) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Notice:</strong> This will create a passing exam attempt record for the selected student. 
                            Use this feature to remedy situations where a student's legitimate passing attempt was accidentally deleted.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="user_id" class="form-label">
                                        <i class="fas fa-user me-1"></i>Select Student *
                                    </label>
                                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                        <option value="">Choose a student...</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="score" class="form-label">
                                        <i class="fas fa-percentage me-1"></i>Score (%) *
                                    </label>
                                    <input type="number" 
                                           name="score" 
                                           id="score" 
                                           class="form-control @error('score') is-invalid @enderror"
                                           value="{{ old('score', $exam->passing_score) }}"
                                           min="{{ $exam->passing_score }}" 
                                           max="100" 
                                           step="0.01" 
                                           required>
                                    <small class="text-muted">
                                        Passing score for this exam: {{ $exam->passing_score }}%
                                    </small>
                                    @error('score')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="notes" class="form-label">
                                <i class="fas fa-sticky-note me-1"></i>Administrative Notes
                            </label>
                            <textarea name="notes" 
                                      id="notes" 
                                      class="form-control @error('notes') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Optional: Reason for manual pass, reference to original attempt, etc.">{{ old('notes') }}</textarea>
                            <small class="text-muted">These notes will be stored with the exam attempt record for audit purposes.</small>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Exam Information:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Course: {{ $exam->course->title ?? 'N/A' }}</li>
                                <li>Passing Score: {{ $exam->passing_score }}%</li>
                                <li>Duration: {{ $exam->duration_minutes }} minutes</li>
                                <li>Max Attempts: {{ $exam->max_attempts }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to manually pass this student? This action will create a permanent exam attempt record.')">
                                <i class="fas fa-check me-1"></i>Mark as Passed
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-focus the student selection
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('user_id').focus();
    });
</script>
@endpush
