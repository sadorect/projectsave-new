@extends('admin.layouts.app')

@section('title', 'Audit Log')
@section('page_kicker', 'Audit And Security')
@section('page_subtitle', 'Review operator activity, export the trail, and manage audit retention without leaving the admin console.')

@section('content')
@php($canManageAudit = auth()->user()->can('manage-audit-log'))

<div class="admin-page-shell">
    <section class="admin-stat-grid">
        <article class="admin-stat-card">
            <span class="admin-stat-label">Total entries</span>
            <strong class="admin-stat-value">{{ number_format($summary['total_logs']) }}</strong>
            <p class="admin-stat-note mb-0">All audit items currently retained in the system.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Logged today</span>
            <strong class="admin-stat-value">{{ number_format($summary['today_logs']) }}</strong>
            <p class="admin-stat-note mb-0">Fresh activity recorded since midnight.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Operators</span>
            <strong class="admin-stat-value">{{ number_format($summary['unique_admins']) }}</strong>
            <p class="admin-stat-note mb-0">Distinct back-office users represented in the log.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Destructive actions</span>
            <strong class="admin-stat-value">{{ number_format($summary['destructive_actions']) }}</strong>
            <p class="admin-stat-note mb-0">Deletes, removals, and rejects captured for review.</p>
        </article>
    </section>

    <x-ui.panel title="Filters" subtitle="Narrow the audit trail by time window, action type, or operator.">
        <form method="GET" class="admin-filter-grid">
            <label class="admin-field">
                <span class="admin-field-label">From</span>
                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
            </label>
            <label class="admin-field">
                <span class="admin-field-label">To</span>
                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
            </label>
            <label class="admin-field">
                <span class="admin-field-label">Action</span>
                <select name="action" class="form-select">
                    <option value="">All actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                    @endforeach
                </select>
            </label>
            <label class="admin-field">
                <span class="admin-field-label">Admin user ID</span>
                <input type="text" name="admin_user_id" value="{{ request('admin_user_id') }}" placeholder="Filter by operator ID" class="form-control">
            </label>

            <div class="admin-filter-actions">
                <button class="surface-button-primary" type="submit">Apply filters</button>
                <a href="{{ route('admin.audit.index') }}" class="surface-button-secondary">Reset</a>
                <a href="{{ route('admin.audit.index', array_merge(request()->all(), ['export' => 'csv'])) }}" class="surface-button-ghost">Export CSV</a>
            </div>
        </form>
    </x-ui.panel>

    <x-ui.panel title="Activity Trail" subtitle="Track who changed what, when it happened, and how the action was performed.">
        @if($logs->isNotEmpty())
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                @if($canManageAudit)
                    <form id="auditBulkDestroyForm" method="POST" action="{{ route('admin.audit.bulkDestroy') }}" class="d-flex flex-wrap align-items-center gap-2" data-admin-confirm="Delete the selected audit entries? This action cannot be undone.">
                        @csrf
                        <button type="submit" class="surface-button-secondary">Delete selected</button>
                        <span class="text-muted small">Check individual rows below to choose records for deletion.</span>
                    </form>
                @endif

                @if($canManageAudit)
                    <form method="POST" action="{{ route('admin.audit.toggle') }}" class="d-flex flex-wrap align-items-center gap-3 ms-auto">
                        @csrf
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="errorAuditToggle" name="enabled" value="1" {{ data_get($errorAuditEnabled, 'enabled') ? 'checked' : '' }}>
                            <label class="form-check-label" for="errorAuditToggle">Capture server errors in the audit trail</label>
                        </div>
                        <button type="submit" class="surface-button-secondary">Save setting</button>
                    </form>
                @else
                    <div class="d-flex flex-wrap align-items-center gap-3 ms-auto">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="errorAuditToggleView" value="1" {{ data_get($errorAuditEnabled, 'enabled') ? 'checked' : '' }} disabled>
                            <label class="form-check-label" for="errorAuditToggleView">Capture server errors in the audit trail</label>
                        </div>
                        <span class="text-muted small">View-only role</span>
                    </div>
                @endif
            </div>

            <div class="surface-table-shell">
                <table class="table admin-table align-middle mb-0">
                    <thead>
                        <tr>
                            @if($canManageAudit)
                                <th class="text-center" style="width: 56px;">
                                    <input type="checkbox" data-admin-select-all="[data-admin-select-item]">
                                </th>
                            @endif
                            <th>ID</th>
                            <th>When</th>
                            <th>Operator</th>
                            <th>Action</th>
                            <th>Target</th>
                            <th>Metadata</th>
                            <th>IP</th>
                            @if($canManageAudit)
                                <th class="text-end">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                @if($canManageAudit)
                                    <td class="text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $log->id }}" data-admin-select-item form="auditBulkDestroyForm">
                                    </td>
                                @endif
                                <td>#{{ $log->id }}</td>
                                <td>
                                    <strong class="d-block">{{ $log->created_at->format('M j, Y') }}</strong>
                                    <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                </td>
                                <td>
                                    @if($log->adminUser)
                                        @can('view', $log->adminUser)
                                            <a href="{{ route('admin.users.show', $log->adminUser) }}" class="admin-link">{{ $log->adminUser->name }}</a>
                                        @else
                                            <span>{{ $log->adminUser->name }}</span>
                                        @endcan
                                        <small class="d-block text-muted">ID {{ $log->admin_user_id }}</small>
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="admin-status-chip is-info">{{ str_replace('_', ' ', $log->action) }}</span>
                                </td>
                                <td>
                                    <strong class="d-block">{{ $log->target_type ?? 'System' }}</strong>
                                    <small class="text-muted">#{{ $log->target_id ?? 'n/a' }}</small>
                                </td>
                                <td>
                                    @if(! empty($log->meta))
                                        <details class="admin-inline-details">
                                            <summary>View payload</summary>
                                            <pre class="admin-json-block">{{ json_encode($log->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </details>
                                    @else
                                        <span class="text-muted">No metadata</span>
                                    @endif
                                </td>
                                <td><code>{{ $log->ip_address ?? 'n/a' }}</code></td>
                                @if($canManageAudit)
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.audit.destroy', $log->id) }}" class="d-inline-flex" data-admin-confirm="Delete this audit entry?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $logs->withQueryString()->links() }}
            </div>
        @else
            <x-ui.empty-state
                title="No audit entries match these filters"
                message="Try widening the date range or clearing the filters to see more activity."
                icon="bi bi-shield-check"
            >
                <x-slot:actions>
                    <a href="{{ route('admin.audit.index') }}" class="surface-button-secondary">Reset filters</a>
                </x-slot:actions>
            </x-ui.empty-state>
        @endif
    </x-ui.panel>
</div>
@endsection
