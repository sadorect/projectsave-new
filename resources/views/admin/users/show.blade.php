@extends('admin.layouts.app')

@section('title', 'View User - ' . $user->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>User Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">{{ $user->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <!-- User Information Card -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-circle"></i> User Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Name</label>
                                <p class="form-control-plaintext">{{ $user->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email Address</label>
                                <p class="form-control-plaintext">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">User Type</label>
                                <p class="form-control-plaintext">
                                    @if($user->user_type)
                                        <span class="badge bg-info fs-6">{{ ucfirst(str_replace('_', ' ', $user->user_type)) }}</span>
                                    @else
                                        <span class="badge bg-secondary fs-6">Regular User</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Account Status</label>
                                <p class="form-control-plaintext">
                                    @if($user->is_admin)
                                        <span class="badge bg-danger fs-6">Administrator</span>
                                    @else
                                        <span class="badge bg-success fs-6">Regular User</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email Verification</label>
                                <p class="form-control-plaintext">
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success fs-6">
                                                <i class="bi bi-check-circle"></i> Verified
                                        </span>
                                        <small class="text-muted d-block">{{ $user->email_verified_at ? \Carbon\Carbon::parse($user->email_verified_at)->format('M d, Y H:i') : '' }}</small>
                                    @else
                                        <span class="badge bg-warning fs-6">
                                                <span id="email-verified-badge" class="badge bg-warning fs-6">
                                                    <i class="bi bi-exclamation-triangle"></i> Unverified
                                                </span>
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Member Since</label>
                                <p class="form-control-plaintext">{{ $user->created_at->format('F d, Y') }}</p>
                                <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roles and Permissions Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-check"></i> Roles & Permissions
                    </h5>
                </div>
                <div class="card-body">
                    @if($user->is_admin)
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            This user has administrator privileges and has access to all system features.
                        </div>
                    @else
                        @if($user->roles && $user->roles->count() > 0)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Assigned Roles</label>
                                <div>
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-primary me-2 mb-2">{{ $role->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="text-muted">No specific roles assigned to this user.</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Activity and Stats Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up"></i> Quick Stats
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Account Age</span>
                        <strong>{{ $user->created_at->diffInDays() }} days</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Last Updated</span>
                        <strong>{{ $user->updated_at->diffForHumans() }}</strong>
                    </div>
                    @if($user->user_type === 'asom_student')
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>ASOM Student</span>
                            <span class="badge bg-success">Active</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear"></i> Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit User
                        </a>
                        @if(!$user->email_verified_at)
                            <form id="admin-verify-form" action="{{ route('admin.users.verify', $user) }}" method="POST" class="mb-2">
                                @csrf
                                @method('PATCH')
                                <button id="admin-verify-btn" type="submit" class="btn btn-outline-warning">
                                    <i class="bi bi-check2-all"></i> Mark Email Verified
                                </button>
                            </form>
                            <button class="btn btn-outline-secondary mb-2" onclick="resendVerification({{ $user->id }})">
                                <i class="bi bi-envelope"></i> Resend Verification
                            </button>
                        @endif
                        @if(\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_active'))
                        <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" class="mb-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-{{ $user->is_active ? 'danger' : 'success' }}">
                                <i class="bi {{ $user->is_active ? 'bi-person-x' : 'bi-person-check' }}"></i>
                                {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                            </button>
                        </form>
                        @endif
                        @if(!$user->is_admin)
                            <button class="btn btn-outline-success" onclick="toggleAdminStatus({{ $user->id }})">
                                <i class="bi bi-shield-plus"></i> Make Admin
                            </button>
                        @endif
                        <hr>
                        <button class="btn btn-outline-danger" onclick="confirmDelete({{ $user->id }})">
                            <i class="bi bi-trash"></i> Delete User
                        </button>
                    </div>
                </div>
            </div>

            <!-- ASOM Information (if applicable) -->
            @if($user->user_type === 'asom_student')
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-graduation-cap"></i> ASOM Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">This user is enrolled in the Archippus School of Ministry program.</p>
                        <div class="d-grid">
                            <a href="{{ route('asom.welcome') }}" class="btn btn-outline-info" target="_blank">
                                <i class="bi bi-box-arrow-up-right"></i> View ASOM Groups
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> Deleting this user will permanently remove all their data.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(userId) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/users/${userId}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function resendVerification(userId) {
    if (!confirm('Resend email verification to this user?')) return;

    fetch('/email/verification-notification', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ user_id: userId })
    }).then(res => {
        if (res.ok) {
            alert('Verification email resent successfully!');
            location.reload();
        } else {
            alert('Failed to resend verification email.');
        }
    }).catch(() => alert('Failed to resend verification email.'));
}

function toggleAdminStatus(userId) {
    if (!confirm('Make this user an administrator?')) return;

    // Redirect to edit page for now
    window.location.href = `/admin/users/${userId}/edit`;
}

// Handle admin verify form via fetch to allow in-place UI update
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('admin-verify-form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!confirm('Mark this user email as verified?')) return;

        const url = form.action;
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({})
        }).then(res => res.json())
        .then(data => {
            if (data && data.success) {
                // update badge
                const badge = document.getElementById('email-verified-badge');
                if (badge) {
                    badge.className = 'badge bg-success fs-6';
                    badge.innerHTML = '<i class="bi bi-check-circle"></i> Verified';
                }
                alert(data.message || 'User email verified successfully');
            } else {
                alert(data.message || 'Failed to verify user email');
            }
        }).catch(() => alert('Failed to verify user email'));
    });
});
</script>
@endpush
