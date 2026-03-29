@extends('admin.layouts.app')

@section('title', 'Subscriber Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="mb-1">Subscriber Details</h1>
            <p class="text-muted mb-0">{{ $subscriber->email }}</p>
        </div>
        <a href="{{ route('admin.newsletter-subscribers.index') }}" class="btn btn-outline-secondary">Back to subscribers</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Subscription Record</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $subscriber->email }}</dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $subscriber->is_active ? 'success' : 'secondary' }}">
                                {{ $subscriber->is_active ? 'Active subscriber' : 'Unsubscribed' }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Source</dt>
                        <dd class="col-sm-8">{{ ucfirst($subscriber->source ?: 'unknown') }}</dd>

                        <dt class="col-sm-4">Subscribed At</dt>
                        <dd class="col-sm-8">{{ optional($subscriber->subscribed_at)->format('F d, Y g:i A') ?: 'N/A' }}</dd>

                        <dt class="col-sm-4">Unsubscribed At</dt>
                        <dd class="col-sm-8">{{ optional($subscriber->unsubscribed_at)->format('F d, Y g:i A') ?: 'Still subscribed' }}</dd>

                        <dt class="col-sm-4">Created At</dt>
                        <dd class="col-sm-8">{{ optional($subscriber->created_at)->format('F d, Y g:i A') }}</dd>

                        <dt class="col-sm-4">Last Updated</dt>
                        <dd class="col-sm-8">{{ optional($subscriber->updated_at)->format('F d, Y g:i A') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Admin Notes</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">Subscribers are currently collected from the footer form and can unsubscribe using their mailed unsubscribe link.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Delivery Safety</h5>
                </div>
                <div class="card-body">
                    <div class="small text-muted mb-2">Unsubscribe token</div>
                    <code class="d-block text-break">{{ $subscriber->unsubscribe_token }}</code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
