@extends('admin.layouts.app')

@section('title', 'Certificate Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Certificate Details</h1>
        <div>
            <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Certificates
            </a>
            <a href="{{ route('admin.certificates.preview', $certificate) }}" target="_blank" class="btn btn-outline-primary">
                <i class="bi bi-aspect-ratio"></i> Preview
            </a>
            @if(!$certificate->is_approved)
                <form action="{{ route('admin.certificates.regenerate', $certificate) }}" method="POST" class="d-inline" onsubmit="return confirm('Regenerate this certificate? The current one will be archived and replaced with a new pending certificate.')">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning">
                        <i class="bi bi-arrow-repeat"></i> Regenerate
                    </button>
                </form>
                <button type="button" class="btn btn-success" onclick="approveModal()">
                    <i class="bi bi-check-circle"></i> Approve
                </button>
                <button type="button" class="btn btn-danger" onclick="rejectModal()">
                    <i class="bi bi-x-circle"></i> Reject
                </button>
                <form action="{{ route('admin.certificates.destroy', $certificate) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this certificate? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Certificate Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-award"></i> Certificate Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Certificate ID:</strong></div>
                        <div class="col-sm-8"><code>{{ $certificate->certificate_id }}</code></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Type:</strong></div>
                        <div class="col-sm-8">
                            @if($certificate->course_id)
                                <span class="badge bg-secondary">Course Certificate</span>
                                <br><small>{{ $certificate->course->title }}</small>
                            @else
                                <span class="badge bg-primary">
                                    <i class="bi bi-mortarboard"></i> Diploma in Ministry
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Final Grade:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-success fs-6">{{ $certificate->final_grade }}%</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Status:</strong></div>
                        <div class="col-sm-8">
                            @if($certificate->is_approved)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Approved
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="bi bi-clock"></i> Pending Approval
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Completed:</strong></div>
                        <div class="col-sm-8">{{ $certificate->completed_at->format('F d, Y g:i A') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Requested:</strong></div>
                        <div class="col-sm-8">{{ $certificate->created_at->format('F d, Y g:i A') }}</div>
                    </div>
                    @if($certificate->issued_at)
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Issued:</strong></div>
                            <div class="col-sm-8">{{ $certificate->issued_at->format('F d, Y g:i A') }}</div>
                        </div>
                    @endif
                    @if($certificate->notes)
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Notes:</strong></div>
                            <div class="col-sm-8">
                                <p class="mb-0">{{ $certificate->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if($certificate->is_approved)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-link-45deg"></i> Public Verification
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">Public verification URL:</p>
                        <div class="input-group">
                            <input type="text" class="form-control" readonly 
                                   value="{{ $certificate->verification_url }}" id="verificationUrl">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard()">
                                <i class="bi bi-clipboard"></i> Copy
                            </button>
                        </div>
                        <small class="text-muted">
                            Anyone with this URL can verify the authenticity of this certificate.
                        </small>
                    </div>
                </div>
            @endif
        </div>

        <!-- Student Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person"></i> Student Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Name:</strong></div>
                        <div class="col-sm-8">{{ $certificate->user->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Email:</strong></div>
                        <div class="col-sm-8">{{ $certificate->user->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>User Type:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-info">
                                {{ ucfirst(str_replace('_', ' ', $certificate->user->user_type ?? 'student')) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Joined:</strong></div>
                        <div class="col-sm-8">{{ $certificate->user->created_at->format('F d, Y') }}</div>
                    </div>
                    @if($certificate->user->phone)
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Phone:</strong></div>
                            <div class="col-sm-8">{{ $certificate->user->phone }}</div>
                        </div>
                    @endif
                </div>
            </div>

            @if($certificate->is_approved && $certificate->approver)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-person-check"></i> Approval Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Approved By:</strong></div>
                            <div class="col-sm-8">{{ $certificate->approver->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Approved On:</strong></div>
                            <div class="col-sm-8">{{ $certificate->approved_at->format('F d, Y g:i A') }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Course Details for Diploma Certificates -->
    @if(!$certificate->course_id && isset($userCourseDetails))
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-mortarboard"></i> Diploma Requirements Completion
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            This student has completed all requirements for the Diploma in Ministry program.
                        </p>
                        
                        <div class="row">
                            @foreach($userCourseDetails as $courseDetail)
                                <div class="col-md-6 mb-4">
                                    <div class="border rounded p-3 {{ $courseDetail['completed'] ? 'bg-light-success' : 'bg-light-danger' }}">
                                        <h6 class="mb-2">
                                            @if($courseDetail['completed'])
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                            @else
                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                            @endif
                                            {{ $courseDetail['course_title'] }}
                                        </h6>
                                        
                                        @if($courseDetail['enrolled_at'])
                                            <small class="text-muted">
                                                Enrolled: {{ \Carbon\Carbon::parse($courseDetail['enrolled_at'])->format('M d, Y') }}
                                            </small>
                                        @endif
                                        
                                        @if(count($courseDetail['exam_results']) > 0)
                                            <div class="mt-2">
                                                <small><strong>Exam Results:</strong></small>
                                                @foreach($courseDetail['exam_results'] as $exam)
                                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                                        <small>{{ $exam['exam_title'] }}</small>
                                                        <div>
                                                            <span class="badge {{ $exam['passed'] ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $exam['best_score'] }}%
                                                            </span>
                                                            <small class="text-muted">({{ $exam['attempts_count'] }} attempts)</small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <small class="text-muted">No exams completed</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.certificates.approve', $certificate) }}">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p>Are you sure you want to approve this certificate?</p>
                    <div class="alert alert-info">
                        <strong>Student:</strong> {{ $certificate->user->name }}<br>
                        <strong>Certificate:</strong> 
                        @if($certificate->course_id)
                            {{ $certificate->course->title }}
                        @else
                            Diploma in Ministry
                        @endif
                        <br>
                        <strong>Grade:</strong> {{ $certificate->final_grade }}%
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes (Optional)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" 
                                  placeholder="Add any additional notes or comments">{{ $certificate->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Approve Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.certificates.reject', $certificate) }}">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <strong>Warning:</strong> This action will permanently reject this certificate.
                        The student will be notified of the rejection.
                    </div>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">
                            Rejection Reason <span class="text-danger">*</span>
                        </label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" 
                                  rows="4" required 
                                  placeholder="Please provide a detailed reason for rejection. This will be shared with the student."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Reject Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveModal() {
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function rejectModal() {
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function copyToClipboard() {
    const input = document.getElementById('verificationUrl');
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value);
    
    // Show feedback
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-check"></i> Copied!';
    button.classList.add('btn-success');
    button.classList.remove('btn-outline-secondary');
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-secondary');
    }, 2000);
}
</script>

<style>
.bg-light-success {
    background-color: rgba(25, 135, 84, 0.1) !important;
}
.bg-light-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
}
</style>
@endsection
