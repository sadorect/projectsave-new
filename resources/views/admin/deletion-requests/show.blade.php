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
                        <dt class="col-sm-3">User</dt>
                        <dd class="col-sm-9">{{ $request->user->name }}</dd>

                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9">{{ $request->user->email }}</dd>

                        <dt class="col-sm-3">Requested On</dt>
                        <dd class="col-sm-9">{{ $request->created_at->format('M d, Y H:i') }}</dd>

                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : 'success' }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Reason</dt>
                        <dd class="col-sm-9">{{ $request->reason }}</dd>
                    </dl>

                    @if($request->status === 'pending')
                        <form action="{{ route('admin.deletion-requests.process', $request) }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                Process Deletion
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
