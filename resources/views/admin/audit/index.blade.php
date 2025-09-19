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
                        <th>ID</th>
                        <th>When</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Target</th>
                        <th>Meta</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>@if($log->adminUser)<a href="{{ route('admin.users.show', $log->adminUser) }}">{{ $log->adminUser->name }} ({{ $log->adminUser->id }})</a>@else System @endif</td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->target_type }} #{{ $log->target_id }}</td>
                        <td><pre style="white-space:pre-wrap;">{{ json_encode($log->meta) }}</pre></td>
                        <td>{{ $log->ip_address }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                {{ $logs->withQueryString()->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
