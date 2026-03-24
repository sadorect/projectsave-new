@extends('admin.layouts.app')

@section('title', 'Partner Applications')
@section('page_kicker', 'Partner Operations')
@section('page_subtitle', 'Review prayer, ground, and skilled partner applications through one consistent approval queue.')

@section('content')
<div class="admin-page-shell">
    <section class="admin-stat-grid">
        <article class="admin-stat-card">
            <span class="admin-stat-label">Total applications</span>
            <strong class="admin-stat-value">{{ number_format($summary['total']) }}</strong>
            <p class="admin-stat-note mb-0">All partner records currently stored in the pipeline.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Pending review</span>
            <strong class="admin-stat-value">{{ number_format($summary['pending']) }}</strong>
            <p class="admin-stat-note mb-0">Applications still waiting for an operator decision.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Approved</span>
            <strong class="admin-stat-value">{{ number_format($summary['approved']) }}</strong>
            <p class="admin-stat-note mb-0">Partner records already cleared into ministry service.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Rejected</span>
            <strong class="admin-stat-value">{{ number_format($summary['rejected']) }}</strong>
            <p class="admin-stat-note mb-0">Applications closed without approval.</p>
        </article>
    </section>

    <x-ui.panel title="Filter Queue" subtitle="Slice the application queue by partner type or status.">
        <form method="GET" class="admin-filter-grid">
            <label class="admin-field">
                <span class="admin-field-label">Partner type</span>
                <select name="partner_type" class="form-select">
                    <option value="">All partner types</option>
                    <option value="prayer" @selected(request('partner_type') === 'prayer')>Prayer Force</option>
                    <option value="ground" @selected(request('partner_type') === 'ground')>Ground Force</option>
                    <option value="skilled" @selected(request('partner_type') === 'skilled')>Skilled Force</option>
                </select>
            </label>
            <label class="admin-field">
                <span class="admin-field-label">Status</span>
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                </select>
            </label>

            <div class="admin-filter-actions">
                <button type="submit" class="surface-button-primary">Apply filters</button>
                <a href="{{ route('admin.partners.index') }}" class="surface-button-secondary">Reset</a>
            </div>
        </form>
    </x-ui.panel>

    <div class="row g-4">
        <div class="col-xl-8">
            <x-ui.panel title="Application Queue" subtitle="Review the latest partner records, then approve, reject, or open the full application.">
                @if($partners->isNotEmpty())
                    <div class="surface-table-shell">
                        <table class="table admin-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Applicant</th>
                                    <th>Partner type</th>
                                    <th>Calling</th>
                                    <th>Status</th>
                                    <th>Applied</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($partners as $partner)
                                    <tr>
                                        <td>
                                            <strong class="d-block">{{ $partner->name }}</strong>
                                            <small class="text-muted">{{ $partner->email }}</small>
                                            <small class="d-block text-muted">{{ $partner->phone }}</small>
                                        </td>
                                        <td>
                                            <span class="admin-status-chip is-muted">{{ ucfirst($partner->partner_type) }} Force</span>
                                        </td>
                                        <td>{{ $partner->calling ?: 'Not provided' }}</td>
                                        <td>
                                            <span class="admin-status-chip {{ $partner->status === 'approved' ? 'is-success' : ($partner->status === 'rejected' ? 'is-danger' : 'is-warning') }}">
                                                {{ ucfirst($partner->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="d-block">{{ $partner->created_at->format('M j, Y') }}</strong>
                                            <small class="text-muted">{{ $partner->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td class="text-end">
                                            <div class="admin-action-row justify-content-end">
                                                <a href="{{ route('admin.partners.show', $partner) }}" class="btn btn-sm btn-outline-primary">Review</a>
                                                @if($partner->status === 'pending')
                                                    <form action="{{ route('admin.partners.approve', $partner) }}" method="POST" class="d-inline-flex">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="notify_via[]" value="mail">
                                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                    </form>
                                                    <form action="{{ route('admin.partners.reject', $partner) }}" method="POST" class="d-inline-flex" data-admin-confirm="Reject this partner application?">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="notify_via[]" value="mail">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $partners->links() }}
                    </div>
                @else
                    <x-ui.empty-state
                        title="No partner applications found"
                        message="There are no records matching the current filter set."
                        icon="bi bi-people"
                    >
                        <x-slot:actions>
                            <a href="{{ route('admin.partners.index') }}" class="surface-button-secondary">Reset filters</a>
                        </x-slot:actions>
                    </x-ui.empty-state>
                @endif
            </x-ui.panel>
        </div>

        <div class="col-xl-4">
            <div class="d-grid gap-4">
                <x-ui.panel title="Application Mix" subtitle="See how the current queue is distributed by force type.">
                    <div class="admin-stack-list">
                        <div class="admin-stack-item">
                            <span>Prayer Force</span>
                            <strong>{{ number_format($summary['prayer']) }}</strong>
                        </div>
                        <div class="admin-stack-item">
                            <span>Ground Force</span>
                            <strong>{{ number_format($summary['ground']) }}</strong>
                        </div>
                        <div class="admin-stack-item">
                            <span>Skilled Force</span>
                            <strong>{{ number_format($summary['skilled']) }}</strong>
                        </div>
                    </div>
                </x-ui.panel>

                <x-ui.panel title="Operator Note" subtitle="Use the detail page when you need to review salvation history, leadership references, or communication preferences before deciding.">
                    <p class="text-muted mb-0">
                        The queue view is optimized for quick triage. Open a record to see the full ministry application, choose notification channels,
                        and preserve a cleaner approval history.
                    </p>
                </x-ui.panel>
            </div>
        </div>
    </div>
</div>
@endsection
