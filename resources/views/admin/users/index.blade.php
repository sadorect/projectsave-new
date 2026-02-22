@extends('admin.layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="container-fluid">

    {{-- Page heading --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Users</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add New User
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Filter bar ────────────────────────────────────────────────────── --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.users.index') }}" id="filterForm">
                {{-- preserve per_page when filters change --}}
                <input type="hidden" name="per_page" value="{{ $perPage }}">

                <div class="row g-2 align-items-end">
                    {{-- Search --}}
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold mb-1">Search</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control form-control-sm"
                                   placeholder="Name or email…"
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- User Type --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold mb-1">User Type</label>
                        <select name="user_type" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            <option value="regular"       @selected(request('user_type') === 'regular')>Regular</option>
                            <option value="asom_student"  @selected(request('user_type') === 'asom_student')>ASOM Student</option>
                        </select>
                    </div>

                    {{-- Role --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold mb-1">Role</label>
                        <select name="is_admin" class="form-select form-select-sm">
                            <option value="">All Roles</option>
                            <option value="1" @selected(request('is_admin') === '1')>Admins</option>
                            <option value="0" @selected(request('is_admin') === '0')>Non-Admins</option>
                        </select>
                    </div>

                    {{-- Verified --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold mb-1">Verified</label>
                        <select name="verified" class="form-select form-select-sm">
                            <option value="">Any Status</option>
                            <option value="1" @selected(request('verified') === '1')>Verified</option>
                            <option value="0" @selected(request('verified') === '0')>Unverified</option>
                        </select>
                    </div>

                    {{-- Per page (auto-submits on change) --}}
                    <div class="col-md-1">
                        <label class="form-label small fw-semibold mb-1">Show</label>
                        <select name="per_page" class="form-select form-select-sm"
                                onchange="this.form.submit()">
                            <option value="25"  @selected($perPage == 25)>25</option>
                            <option value="50"  @selected($perPage == 50)>50</option>
                            <option value="100" @selected($perPage == 100)>100</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary flex-fill">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary flex-fill">
                            <i class="bi bi-x-circle me-1"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Bulk action form (wraps table) ────────────────────────────────── --}}
    <form method="POST" action="{{ route('admin.users.bulk-action') }}" id="bulkForm">
        @csrf

        {{-- Pass active filters back so the redirect returns to the same filtered view --}}
        @foreach(request()->only(['search','user_type','is_admin','verified','per_page']) as $key => $val)
            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
        @endforeach

        <div class="card">

            {{-- Bulk toolbar (hidden until rows are checked) --}}
            <div class="card-header d-flex align-items-center gap-3 py-2" id="bulkToolbar"
                 style="display:none!important">
                <span class="text-muted small me-auto" id="selectedCount">0 selected</span>
                <div class="input-group input-group-sm" style="width:auto">
                    <select name="action" class="form-select form-select-sm" id="bulkActionSelect">
                        <option value="">— Bulk Action —</option>
                        <option value="verify">✔ Verify Email</option>
                        <option value="activate">▶ Activate</option>
                        <option value="deactivate">⏸ Deactivate</option>
                        <option value="delete">🗑 Delete</option>
                    </select>
                    <button type="button" class="btn btn-sm btn-danger"
                            onclick="confirmBulkAction()">Apply</button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:36px">
                                    <input type="checkbox" id="selectAll" class="form-check-input"
                                           title="Select all on this page">
                                </th>
                                <th style="width:70px">Status</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>User Type</th>
                                <th>Role</th>
                                <th>Verified</th>
                                <th>Joined</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                if (!isset($activeSessions)) {
                                    try {
                                        $sessionTable  = config('session.table', 'sessions');
                                        $activeSessions = \Illuminate\Support\Facades\DB::table($sessionTable)
                                            ->whereNotNull('user_id')
                                            ->pluck('user_id')->unique()
                                            ->map(fn($v) => (int) $v)->toArray();
                                    } catch (\Throwable $e) {
                                        $activeSessions = [];
                                    }
                                }
                            @endphp

                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                           class="form-check-input user-checkbox">
                                </td>
                                <td>
                                    @if(in_array($user->id, $activeSessions))
                                        <span class="badge bg-success">Online</span>
                                    @else
                                        <span class="badge bg-secondary">Offline</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $user->name }}</span>
                                </td>
                                <td class="text-muted small">{{ $user->email }}</td>
                                <td>
                                    @if($user->user_type)
                                        <span class="badge bg-info text-dark">
                                            {{ ucfirst(str_replace('_', ' ', $user->user_type)) }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary border">Regular</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->is_admin)
                                        <span class="badge bg-primary">Admin</span>
                                    @else
                                        @forelse($user->roles as $role)
                                            <span class="badge bg-secondary">{{ $role->name }}</span>
                                        @empty
                                            <span class="text-muted small">—</span>
                                        @endforelse
                                    @endif
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-patch-check me-1"></i>Verified
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                            <i class="bi bi-exclamation-circle me-1"></i>Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted small text-nowrap">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.users.show', $user) }}"
                                           class="btn btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        {{-- form= attribute links this button to the out-of-tree delete form below (avoids nested <form>) --}}
                                        <button type="submit"
                                                form="delete-user-{{ $user->id }}"
                                                class="btn btn-outline-danger" title="Delete"
                                                onclick="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="bi bi-people fs-3 d-block mb-2"></i>
                                    No users match the current filters.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer: count + pagination --}}
            <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2 py-2">
                <small class="text-muted">
                    Showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}
                    of {{ $users->total() }} users
                    &nbsp;·&nbsp; Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
                </small>
                {{ $users->links('pagination::bootstrap-4') }}
            </div>
        </div>{{-- /card --}}
    </form>{{-- /bulkForm --}}
</div>

{{-- Delete forms live OUTSIDE bulkForm to avoid invalid nested <form> elements --}}
@foreach($users as $user)
<form id="delete-user-{{ $user->id }}"
      action="{{ route('admin.users.destroy', $user) }}"
      method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>
@endforeach

@push('scripts')
<script>
(function () {
    const selectAll     = document.getElementById('selectAll');
    const bulkToolbar   = document.getElementById('bulkToolbar');
    const selectedCount = document.getElementById('selectedCount');
    const bulkForm      = document.getElementById('bulkForm');

    function getChecked() {
        return [...document.querySelectorAll('.user-checkbox:checked')];
    }

    function updateToolbar() {
        const n = getChecked().length;
        if (n > 0) {
            bulkToolbar.style.removeProperty('display');
            bulkToolbar.style.display = 'flex';
        } else {
            bulkToolbar.style.display = 'none';
        }
        selectedCount.textContent = n + ' user' + (n !== 1 ? 's' : '') + ' selected';
    }

    selectAll.addEventListener('change', function () {
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = this.checked);
        updateToolbar();
    });

    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            const all     = document.querySelectorAll('.user-checkbox').length;
            const checked = getChecked().length;
            selectAll.indeterminate = checked > 0 && checked < all;
            selectAll.checked       = checked === all;
            updateToolbar();
        });
    });

    window.confirmBulkAction = function () {
        const action  = document.getElementById('bulkActionSelect').value;
        const checked = getChecked();

        if (!action) {
            alert('Please choose a bulk action from the dropdown.');
            return;
        }
        if (checked.length === 0) {
            alert('Please select at least one user.');
            return;
        }

        const labels = {
            delete:     'permanently DELETE',
            verify:     'verify the email of',
            activate:   'activate',
            deactivate: 'deactivate',
        };

        const warning = action === 'delete' ? '\n\nThis action CANNOT be undone.' : '';
        const msg = `Are you sure you want to ${labels[action]} ${checked.length} selected user(s)?${warning}`;

        if (confirm(msg)) {
            bulkForm.submit();
        }
    };
})();
</script>
@endpush

@endsection
