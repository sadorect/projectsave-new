@extends('admin.layouts.app')

@section('title', 'Newsletter Subscribers')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="mb-1">Newsletter Subscribers</h1>
            <p class="text-muted mb-0">Review who is subscribed to devotional email updates and track subscription status.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-2">Total</div>
                    <div class="fs-3 fw-bold">{{ number_format($stats['total']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-2">Active</div>
                    <div class="fs-3 fw-bold text-success">{{ number_format($stats['active']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-2">Unsubscribed</div>
                    <div class="fs-3 fw-bold text-secondary">{{ number_format($stats['inactive']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-2">Last 30 Days</div>
                    <div class="fs-3 fw-bold">{{ number_format($stats['recent']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Source</th>
                            <th>Subscribed</th>
                            <th>Unsubscribed</th>
                            <th class="text-end">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscribers as $subscriber)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $subscriber->email }}</div>
                                    <div class="small text-muted">ID #{{ $subscriber->id }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $subscriber->is_active ? 'success' : 'secondary' }}">
                                        {{ $subscriber->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ ucfirst($subscriber->source ?: 'unknown') }}</td>
                                <td>{{ optional($subscriber->subscribed_at)->format('M d, Y g:i A') ?: 'N/A' }}</td>
                                <td>{{ optional($subscriber->unsubscribed_at)->format('M d, Y g:i A') ?: 'Still subscribed' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.newsletter-subscribers.show', $subscriber) }}" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No newsletter subscribers found yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $subscribers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
