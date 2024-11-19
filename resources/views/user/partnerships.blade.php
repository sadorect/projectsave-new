@extends('layouts.user')

@section('title', 'My Partnerships')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">My Partnerships</h1>
            <p class="text-muted">Manage your partnership commitments and activities</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            @if($partnerships->count() > 0)
                @foreach($partnerships as $partnership)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title">{{ ucfirst($partnership->type) }} Partnership</h5>
                                <span class="badge bg-{{ $partnership->status === 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($partnership->status) }}
                                </span>
                            </div>
                            <p class="text-muted mb-3">Joined: {{ $partnership->created_at->format('M d, Y') }}</p>
                            <div class="partnership-details">
                                <!-- Partnership specific details -->
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <h5>No Active Partnerships</h5>
                        <p class="text-muted">Join our partnership programs to support our mission.</p>
                        <div class="mt-4">
                            <a href="{{ route('partners.create', 'prayer') }}" class="btn btn-primary me-2">Join Prayer Force</a>
                            <a href="{{ route('partners.create', 'skilled') }}" class="btn btn-outline-primary">Become a Skilled Volunteer</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Partnership Benefits</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Regular prayer updates</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Ministry newsletters</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Special event invitations</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Impact reports</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
