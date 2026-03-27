@extends('admin.layouts.app')

@section('title', 'Account Deletion Requests')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Account Deletion Requests</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Reason</th>
                        <th>Requested On</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deletionRequests as $request)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $request->requester_name ?? $request->user?->name ?? 'Deleted user' }}</div>
                            <small class="text-muted">{{ $request->requester_email ?? $request->user?->email ?? 'No email snapshot' }}</small>
                        </td>
                        <td>{{ Str::limit($request->reason, 50) }}</td>
                        <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <span class="badge {{ $request->status === 'pending' ? 'badge-warning text-dark' : ($request->status === 'completed' ? 'badge-success' : 'badge-secondary') }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.deletion-requests.show', $request) }}" class="btn btn-sm btn-info me-2">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No deletion requests found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $deletionRequests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
