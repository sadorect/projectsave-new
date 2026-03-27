@extends('admin.layouts.app')

@section('title', 'Deletion Request Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Deletion Request Details</h5>
                    <a href="{{ route('admin.deletion-requests.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Requester</dt>
                        <dd class="col-sm-9">{{ $request->requester_name ?? $request->user?->name ?? 'Deleted user' }}</dd>

                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9">{{ $request->requester_email ?? $request->user?->email ?? 'No email snapshot' }}</dd>

                        <dt class="col-sm-3">Requested On</dt>
                        <dd class="col-sm-9">{{ $request->created_at->format('M d, Y H:i') }}</dd>

                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'completed' ? 'success' : 'secondary') }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Reason</dt>
                        <dd class="col-sm-9">{{ $request->reason }}</dd>

                        <dt class="col-sm-3">Impact Summary</dt>
                        <dd class="col-sm-9">
                            Files: {{ $impactSummary['files'] }}<br>
                            Partnerships: {{ $impactSummary['partnerships'] }}<br>
                            Activities: {{ $impactSummary['activities'] }}
                        </dd>

                        @if($request->processedBy)
                            <dt class="col-sm-3">Processed By</dt>
                            <dd class="col-sm-9">{{ $request->processedBy->name }}</dd>
                        @endif

                        @if($request->processed_at)
                            <dt class="col-sm-3">Processed On</dt>
                            <dd class="col-sm-9">{{ $request->processed_at->format('M d, Y H:i') }}</dd>
                        @endif

                        @if($request->processed_notes)
                            <dt class="col-sm-3">Admin Notes</dt>
                            <dd class="col-sm-9">{{ $request->processed_notes }}</dd>
                        @endif
                    </dl>

                    @if($request->status === 'pending')
                        <form action="{{ route('admin.deletion-requests.process', $request) }}" method="POST" class="mt-4">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Admin notes</label>
                                <textarea name="processed_notes" class="form-control" rows="4" placeholder="Add any notes that should be saved with this decision.">{{ old('processed_notes') }}</textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="decision" value="complete" class="btn btn-danger" onclick="return confirm('Complete this deletion and remove the account?')">
                                    Complete Deletion
                                </button>
                                <button type="submit" name="decision" value="reject" class="btn btn-outline-secondary" onclick="return confirm('Reject this deletion request?')">
                                    Reject Request
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-light border mt-4 mb-0">
                            This request has already been processed and is kept here for audit history.
                        </div>
                    @endif
                </div>
            </div>

            @if($request->user)
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">Current Account Snapshot</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="small text-muted">User type</div>
                                <div>{{ $request->user->user_type ?? 'standard' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Joined</div>
                                <div>{{ optional($request->user->created_at)->format('M d, Y H:i') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Files</div>
                                <div>{{ $impactSummary['files'] }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Partnerships</div>
                                <div>{{ $impactSummary['partnerships'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
