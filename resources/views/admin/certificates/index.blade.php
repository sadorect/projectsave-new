@extends('admin.layouts.app')

@section('title', 'Certificate Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Certificate Management</h1>
        <div>
            <a href="{{ route('admin.certificate-settings') }}" class="btn btn-outline-primary">
                <i class="bi bi-gear"></i> Certificate Settings
            </a>
            <a href="{{ route('admin.certificates.pending') }}" class="btn btn-warning">
                <i class="bi bi-clock"></i> Pending Certificates ({{ $stats['pending'] }})
            </a>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-plus-circle"></i> Generate Sample
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <form method="POST" action="{{ route('admin.certificates.generate-sample') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-mortarboard"></i> Diploma in Ministry
                            </button>
                        </form>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('admin.certificates.generate-sample-course') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-book"></i> Course Certificate
                            </button>
                        </form>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('admin.certificates.cleanup-samples') }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger" 
                                    onclick="return confirm('Are you sure you want to delete all sample certificates?')">
                                <i class="bi bi-trash"></i> Cleanup Samples
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            <a href="{{ route('admin.certificates.export') }}" class="btn btn-success">
                <i class="bi bi-download"></i> Export
            </a>
            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#testingGuideModal">
                <i class="bi bi-question-circle"></i> Testing Guide
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total'] }}</h4>
                            <small>Total Certificates</small>
                        </div>
                        <i class="bi bi-award fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['pending'] }}</h4>
                            <small>Pending Approval</small>
                        </div>
                        <i class="bi bi-clock fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['approved'] }}</h4>
                            <small>Approved</small>
                        </div>
                        <i class="bi bi-check-circle fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['diploma_certificates'] }}</h4>
                            <small>Diploma Certificates</small>
                        </div>
                        <i class="bi bi-mortarboard fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.certificates.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="diploma" {{ request('status') === 'diploma' ? 'selected' : '' }}>Diploma Certificates</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="user" class="form-label">Student</label>
                        <input type="text" name="user" id="user" class="form-control" 
                               placeholder="Search by name or email" value="{{ request('user') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Certificates Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Certificates</h5>
        </div>
        <div class="card-body">
            @if($certificates->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Certificate ID</th>
                                <th>Student</th>
                                <th>Type</th>
                                <th>Grade</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificates as $certificate)
                                <tr>
                                    <td>
                                        <code>{{ $certificate->certificate_id }}</code>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $certificate->user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $certificate->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($certificate->course_id)
                                            <span class="badge bg-secondary">
                                                {{ $certificate->course->title }}
                                            </span>
                                        @else
                                            <span class="badge bg-primary">
                                                <i class="bi bi-mortarboard"></i> Diploma in Ministry
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $certificate->final_grade }}%</span>
                                    </td>
                                    <td>
                                        @if($certificate->is_approved)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Approved
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $certificate->approved_at->format('M d, Y') }}
                                            </small>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="bi bi-clock"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $certificate->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.certificates.show', $certificate) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if(!$certificate->is_approved)
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="approveModal({{ $certificate->id }})">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="rejectModal({{ $certificate->id }})">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $certificates->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted">No certificates found.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p>Are you sure you want to approve this certificate?</p>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Certificate</button>
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
                                  rows="3" required placeholder="Please provide a reason for rejection"></textarea>
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
function approveModal(certificateId) {
    const form = document.getElementById('approveForm');
    form.action = `/admin/certificates/${certificateId}/approve`;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function rejectModal(certificateId) {
    const form = document.getElementById('rejectForm');
    form.action = `/admin/certificates/${certificateId}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>

<!-- Testing Guide Modal -->
<div class="modal fade" id="testingGuideModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-info-circle"></i> Certificate System Testing Guide
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Proof of Concept:</strong> This guide helps you test all certificate features without waiting for real student completions.
                </div>

                <h6 class="fw-bold mt-4 mb-3">📋 Testing Steps</h6>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">1. Generate Sample Certificates</h6>
                        <ul class="small">
                            <li>Click "Generate Sample" → "Diploma in Ministry"</li>
                            <li>Click "Generate Sample" → "Course Certificate"</li>
                            <li>Both will appear as pending approval</li>
                        </ul>

                        <h6 class="text-primary mt-3">2. Test Approval Workflow</h6>
                        <ul class="small">
                            <li>Go to "Pending Certificates"</li>
                            <li>Test individual approval</li>
                            <li>Test bulk approval</li>
                            <li>Test rejection with reasons</li>
                        </ul>

                        <h6 class="text-primary mt-3">3. Verify Certificate Details</h6>
                        <ul class="small">
                            <li>Click on any certificate to view details</li>
                            <li>Check student information display</li>
                            <li>For diploma: verify course completion breakdown</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">4. Test Public Verification</h6>
                        <ul class="small">
                            <li>Approve a certificate first</li>
                            <li>Copy the verification URL from certificate details</li>
                            <li>Open URL in new tab/incognito window</li>
                            <li>Verify certificate displays correctly</li>
                        </ul>

                        <h6 class="text-primary mt-3">5. Test Features</h6>
                        <ul class="small">
                            <li>✅ Filtering (pending, approved, diploma)</li>
                            <li>✅ Search by student name/email</li>
                            <li>✅ Statistics dashboard</li>
                            <li>✅ Sidebar badge count updates</li>
                            <li>✅ Mobile responsive design</li>
                        </ul>

                        <h6 class="text-primary mt-3">6. Cleanup</h6>
                        <ul class="small">
                            <li>Use "Cleanup Samples" to remove test data</li>
                            <li>All sample certificates are clearly marked</li>
                        </ul>
                    </div>
                </div>

                <div class="alert alert-warning mt-4">
                    <h6 class="fw-bold">📌 Key Features to Verify</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="mb-0 small">
                                <li>Certificate ID generation (ASOM-XXXXX-YYYY)</li>
                                <li>Admin approval requirement</li>
                                <li>Grade calculation and display</li>
                                <li>Diploma vs Course certificate types</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="mb-0 small">
                                <li>Public verification system</li>
                                <li>Pending count in sidebar</li>
                                <li>Audit logging</li>
                                <li>Professional certificate design</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded">
                    <h6 class="fw-bold">🔗 Quick Test URLs:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <small>
                                <strong>Admin Certificate Dashboard:</strong><br>
                                <code>/admin/certificates</code>
                            </small>
                        </div>
                        <div class="col-md-6">
                            <small>
                                <strong>Pending Certificates:</strong><br>
                                <code>/admin/certificates/pending</code>
                            </small>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <small>
                                <strong>Public Verification:</strong><br>
                                <code>/certificates/verify/{certificate-id}</code>
                            </small>
                        </div>
                        <div class="col-md-6">
                            <small>
                                <strong>Sample verification:</strong><br>
                                <em>Generated after approval</em>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form method="POST" action="{{ route('admin.certificates.generate-sample') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="bi bi-mortarboard"></i> Start Testing - Generate Diploma
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
