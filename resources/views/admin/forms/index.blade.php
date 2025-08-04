@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-2 text-gray-800"><i class="fas fa-clipboard-list me-2"></i>Forms Management</h1>
      <p class="text-muted">Create and manage custom forms for data collection</p>
    </div>
    <a href="{{ route('admin.forms.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-2"></i>Create New Form
    </a>
  </div>

  @include('components.alerts')

  @if($forms && $forms->count() > 0)
    <div class="card shadow mb-4">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">All Forms ({{ $forms->total() }})</h6>
        <a href="{{ route('admin.submissions.index') }}" class="btn btn-outline-primary btn-sm">
          <i class="fas fa-list me-1"></i>View All Submissions
        </a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th width="5%">#</th>
                <th width="25%">Form Title</th>
                <th width="30%">Description</th>
                <th width="10%">Fields</th>
                <th width="10%">Submissions</th>
                <th width="10%">Access</th>
                <th width="10%">Created</th>
                <th width="10%">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($forms as $form)
                <tr>
                  <td>{{ $loop->iteration + ($forms->currentPage() - 1) * $forms->perPage() }}</td>
                  <td>
                    <strong>{{ $form->title ?? 'Untitled Form' }}</strong>
                    @if(!empty($form->notify_emails))
                      <br><small class="text-info">
                        <i class="fas fa-envelope me-1"></i>{{ count($form->notify_emails) }} notification(s)
                      </small>
                    @endif
                  </td>
                  <td>
                    <small>{{ Str::limit($form->description ?? 'No description', 100) }}</small>
                  </td>
                  <td>
                    <span class="badge bg-info">{{ count($form->fields ?? []) }} fields</span>
                  </td>
                  <td>
                    <span class="badge bg-success">{{ $form->submissions()->count() }}</span>
                  </td>
                  <td>
                    @if($form->require_login)
                      <span class="badge bg-warning">Login Required</span>
                    @else
                      <span class="badge bg-success">Public</span>
                    @endif
                  </td>
                  <td>
                    <small>{{ $form->created_at->format('M d, Y') }}</small>
                  </td>
                  <td>
                    <div class="btn-group" role="group">
                      <a href="{{ route('forms.show', $form) }}" class="btn btn-info btn-sm" title="View Form">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="{{ route('admin.forms.edit', $form) }}" class="btn btn-warning btn-sm" title="Edit Form">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="{{ route('admin.forms.submissions', $form) }}" class="btn btn-success btn-sm" title="View Submissions">
                        <i class="fas fa-list"></i>
                      </a>
                      <form action="{{ route('admin.forms.destroy', $form) }}" method="POST" class="d-inline" 
                            onsubmit="return confirm('Are you sure you want to delete this form and all its submissions?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" type="submit" title="Delete Form">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <div class="d-flex justify-content-center">
          {{ $forms->links() }}
        </div>
      </div>
    </div>
  @else
    <div class="card shadow">
      <div class="card-body text-center py-5">
        <i class="fas fa-clipboard-list text-muted mb-4" style="font-size: 4rem;"></i>
        <h4 class="text-muted mb-3">No Forms Found</h4>
        <p class="text-muted mb-4">You haven't created any forms yet. Start by creating your first form to collect data from users.</p>
        <a href="{{ route('admin.forms.create') }}" class="btn btn-primary">
          <i class="fas fa-plus me-2"></i>Create Your First Form
        </a>
      </div>
    </div>
  @endif
</div>
@endsection