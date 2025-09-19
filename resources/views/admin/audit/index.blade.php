@extends('admin.layouts.app')

@section('title', 'Admin Audit Log')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Audit Log</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <label class="form-label small">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Action</label>
                    <select name="action" class="form-select">
                        <option value="">All</option>
                        @foreach($actions ?? [] as $a)
                        <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Admin ID</label>
                    <input type="text" name="admin_user_id" value="{{ request('admin_user_id') }}" placeholder="Admin user id" class="form-control">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-12 mt-2">
                    <a href="{{ route('admin.audit.index', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-outline-secondary">Export CSV</a>
                </div>
            </form>

            <table class="table table-striped">
                <thead>
                    <tr>
                            <th><input type="checkbox" id="select-all"></th>
                        <th>ID</th>
                        <th>When</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Target</th>
                        <th>Meta</th>
                        <th>IP</th>
                            <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $log->id }}"></td>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>@if($log->adminUser)<a href="{{ route('admin.users.show', $log->adminUser) }}">{{ $log->adminUser->name }} ({{ $log->adminUser->id }})</a>@else System @endif</td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->target_type }} #{{ $log->target_id }}</td>
                        <td><pre style="white-space:pre-wrap;">{{ json_encode($log->meta) }}</pre></td>
                        <td>{{ $log->ip_address }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.audit.destroy', $log->id) }}" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this log?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <form id="bulk-delete-form" method="POST" action="{{ route('admin.audit.bulkDestroy') }}" style="display:none">
                @csrf
                <div id="bulk-hidden-inputs"></div>
            </form>

            <div class="d-flex gap-2">
                <button id="bulk-delete-btn" class="btn btn-danger">Delete Selected</button>
            </div>

            <div class="mt-3">
                <form method="POST" action="{{ route('admin.audit.toggle') }}">
                    @csrf
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="errorAuditToggle" name="enabled" value="1" {{ data_get($errorAuditEnabled, 'enabled') ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="form-check-label" for="errorAuditToggle">Enable server error audit (500+)</label>
                    </div>
                </form>
            </div>

            <div class="mt-3">
                {{ $logs->withQueryString()->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = selectAll.checked);
        });
    }

    const bulkBtn = document.getElementById('bulk-delete-btn');
    bulkBtn?.addEventListener('click', function () {
        const checked = Array.from(document.querySelectorAll('input[name="ids[]"]:checked')).map(i => i.value);
        if (!checked.length) {
            alert('No items selected.');
            return;
        }
        if (!confirm('Delete selected logs?')) return;

        const container = document.getElementById('bulk-hidden-inputs');
        container.innerHTML = '';
        checked.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            container.appendChild(input);
        });

        document.getElementById('bulk-delete-form').submit();
    });
});
</script>
@endpush
