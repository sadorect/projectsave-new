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
                    @foreach($deletionRequests as $request)
                    <tr>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ Str::limit($request->reason, 50) }}</td>
                        <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <span class="badge {{ $request->status === 'pending' ? 'badge-warning text-dark' : ($request->status === 'processed' ? 'badge-success' : 'badge-secondary') }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td>
                          <div class="btn-group">
                              <a href="{{ route('admin.deletion-requests.show', $request) }}" 
                                 class="btn btn-sm btn-info me-2">
                                  <i class="bi bi-eye"></i> View Details
                              </a>
                              <button type="button" 
                                      class="btn btn-sm btn-danger" 
                                      onclick="confirmDeletion({{ $request->id }})">
                                  Process Deletion
                              </button>
                          </div>
                      </td>
                      
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDeletion(requestId) {
    if (confirm('Are you sure you want to process this deletion request? This cannot be undone.')) {
        document.getElementById('deletion-form-' + requestId).submit();
    }
}
</script>
@endsection
