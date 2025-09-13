@extends('admin.layouts.app')

@section('title', 'Exam Attempts')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list me-2"></i>
                        All Exam Attempts
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Exams
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($attempts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Exam</th>
                                        <th>Score</th>
                                        <th>Result</th>
                                        <th>Date</th>
                                        <th>Duration</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attempts as $attempt)
                                        <tr>
                                            <td>
                                                <strong>{{ $attempt->user->name }}</strong><br>
                                                <small class="text-muted">{{ $attempt->user->email }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $attempt->exam->title }}</strong><br>
                                                <small class="text-muted">{{ $attempt->exam->course->title ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <span class="h5 mb-0 text-{{ $attempt->score >= $attempt->exam->passing_score ? 'success' : 'danger' }}">
                                                    {{ number_format($attempt->score, 1) }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $attempt->score >= $attempt->exam->passing_score ? 'success' : 'danger' }} text-white">
                                                    {{ $attempt->score >= $attempt->exam->passing_score ? 'PASSED' : 'FAILED' }}
                                                </span>
                                            </td>
                                            <td>{{ $attempt->started_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                @if($attempt->completed_at && $attempt->started_at)
                                                    {{ $attempt->started_at->diffInMinutes($attempt->completed_at) }}m
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($attempt->answers['manual_pass']) && $attempt->answers['manual_pass'])
                                                    <span class="badge bg-info text-white" title="Manually passed by administrator">
                                                        <i class="fas fa-hand-paper me-1"></i>Manual
                                                    </span>
                                                @else
                                                    <span class="badge bg-primary text-white">
                                                        <i class="fas fa-pencil-alt me-1"></i>Exam
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.exam-attempts.show', $attempt) }}" 
                                                       class="btn btn-sm btn-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="deleteAttempt({{ $attempt->id }})" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Hidden delete form -->
                                                <form id="delete-form-{{ $attempt->id }}" 
                                                      action="{{ route('admin.exam-attempts.destroy', $attempt) }}" 
                                                      method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $attempts->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Exam Attempts Found</h5>
                            <p class="text-muted">No students have taken any exams yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteAttempt(attemptId) {
    if (confirm('Are you sure you want to delete this exam attempt? This action cannot be undone.')) {
        document.getElementById('delete-form-' + attemptId).submit();
    }
}
</script>
@endpush