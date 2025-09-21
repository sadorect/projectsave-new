@extends('admin.layouts.app')

@section('title', 'Pending Certificates')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Pending Certificates</h1>
        <div>
            <a href="{{ route('admin.certificate-settings') }}" class="btn btn-outline-primary">
                <i class="bi bi-gear"></i> Settings
            </a>
            <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> All Certificates
            </a>
            @if($pendingCertificates->count() > 0)
                <button type="button" class="btn btn-success" onclick="bulkApproveModal()">
                    <i class="bi bi-check-all"></i> Bulk Approve
                </button>
            @endif
        </div>
    </div>

    @if($pendingCertificates->count() > 0)
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            <strong>{{ $pendingCertificates->total() }}</strong> certificates awaiting your approval.
        </div>

        <!-- Bulk Selection -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label" for="selectAll">
                        <strong>Select All Certificates</strong>
                    </label>
                </div>
            </div>
        </div>

        <!-- Pending Certificates List -->
        @foreach($pendingCertificates as $certificate)
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input certificate-checkbox" type="checkbox" 
                               value="{{ $certificate->id }}" id="cert_{{ $certificate->id }}">
                        <label class="form-check-label fw-bold" for="cert_{{ $certificate->id }}">
                            @if($certificate->course_id)
                                {{ $certificate->course->title }} Certificate
                            @else
                                <i class="bi bi-mortarboard text-primary"></i> Diploma in Ministry Certificate
                            @endif
                        </label>
                    </div>
                    <div>
                        <span class="badge bg-warning">Pending Approval</span>
                        <code class="ms-2">{{ $certificate->certificate_id }}</code>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="mb-3">Student Information</h6>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Name:</strong> {{ $certificate->user->name }}<br>
                                    <strong>Email:</strong> {{ $certificate->user->email }}<br>
                                    <strong>User Type:</strong> 
                                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $certificate->user->user_type ?? 'student')) }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Final Grade:</strong> 
                                    <span class="badge bg-success">{{ $certificate->final_grade }}%</span><br>
                                    <strong>Completed:</strong> {{ $certificate->completed_at->format('M d, Y g:i A') }}<br>
                                    <strong>Requested:</strong> {{ $certificate->created_at->format('M d, Y g:i A') }}
                                </div>
                            </div>

                            @if($certificate->notes)
                                <div class="mt-3">
                                    <strong>Notes:</strong>
                                    <p class="text-muted mb-0">{{ $certificate->notes }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.certificates.show', $certificate) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                                <button type="button" class="btn btn-success" 
                                        onclick="quickApprove({{ $certificate->id }})">
                                    <i class="bi bi-check-circle"></i> Quick Approve
                                </button>
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="rejectModal({{ $certificate->id }})">
                                    <i class="bi bi-x-circle"></i> Reject
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-center">
            {{ $pendingCertificates->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                <h4 class="mt-3">All Caught Up!</h4>
                <p class="text-muted">There are no pending certificates to review at this time.</p>
                <a href="{{ route('admin.certificates.index') }}" class="btn btn-primary">
                    View All Certificates
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Bulk Approve Modal -->
<div class="modal fade" id="bulkApproveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Approve Certificates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkApproveForm" method="POST" action="{{ route('admin.certificates.bulk-approve') }}">
                @csrf
                <div class="modal-body">
                    <p>You are about to approve <span id="selectedCount">0</span> certificates.</p>
                    <p class="text-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        This action cannot be undone. Make sure you have reviewed all selected certificates.
                    </p>
                    <div id="selectedCertificates"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-all"></i> Approve Selected
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
            <form id="rejectForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p class="text-danger">Are you sure you want to reject this certificate?</p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" 
                                  rows="3" required placeholder="Please provide a detailed reason for rejection"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Certificate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Select All functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.certificate-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Quick approve
function quickApprove(certificateId) {
    if (confirm('Are you sure you want to approve this certificate?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/certificates/${certificateId}/approve`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Reject modal
function rejectModal(certificateId) {
    const form = document.getElementById('rejectForm');
    form.action = `/admin/certificates/${certificateId}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

// Bulk approve modal
function bulkApproveModal() {
    const selectedCheckboxes = document.querySelectorAll('.certificate-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one certificate to approve.');
        return;
    }
    
    const selectedCount = selectedCheckboxes.length;
    document.getElementById('selectedCount').textContent = selectedCount;
    
    // Clear previous selections
    const container = document.getElementById('selectedCertificates');
    container.innerHTML = '';
    
    // Add hidden inputs for selected certificates
    selectedCheckboxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'certificate_ids[]';
        input.value = checkbox.value;
        container.appendChild(input);
    });
    
    new bootstrap.Modal(document.getElementById('bulkApproveModal')).show();
}
</script>
@endsection
