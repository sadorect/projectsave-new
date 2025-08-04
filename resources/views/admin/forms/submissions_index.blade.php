@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-2 text-gray-800"><i class="fas fa-list me-2"></i>All Form Submissions</h1>
      <p class="text-muted">View and manage all form submissions across all forms</p>
    </div>
    <a href="{{ route('admin.forms.index') }}" class="btn btn-secondary">
      <i class="fas fa-arrow-left me-2"></i>Back to Forms
    </a>
  </div>

  @include('components.alerts')

  @if($submissions && $submissions->count() > 0)
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">All Submissions ({{ $submissions->total() }})</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th width="5%">#</th>
                <th width="25%">Form Title</th>
                <th width="20%">Submitted By</th>
                <th width="15%">Email</th>
                <th width="15%">Submitted At</th>
                <th width="20%">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($submissions as $submission)
                <tr>
                  <td>{{ $loop->iteration + ($submissions->currentPage() - 1) * $submissions->perPage() }}</td>
                  <td>
                    <strong>{{ $submission->form->title ?? 'Unknown Form' }}</strong>
                    <br><small class="text-muted">ID: {{ $submission->form_id }}</small>
                  </td>
                  <td>
                    @if($submission->user)
                      {{ $submission->user->name }}
                    @else
                      <span class="text-muted">Guest User</span>
                    @endif
                  </td>
                  <td>
                    @if($submission->user)
                      {{ $submission->user->email }}
                    @else
                      <span class="text-muted">N/A</span>
                    @endif
                  </td>
                  <td>
                    <small>{{ $submission->created_at->format('M d, Y H:i') }}</small>
                    <br><small class="text-muted">{{ $submission->created_at->diffForHumans() }}</small>
                  </td>
                  <td>
                    <div class="btn-group" role="group">
                      <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" 
                              data-bs-target="#submissionModal{{ $submission->id }}" title="View Details">
                        <i class="fas fa-eye"></i>
                      </button>
                      <a href="{{ route('admin.forms.submissions', $submission->form) }}" 
                         class="btn btn-primary btn-sm" title="View Form Submissions">
                        <i class="fas fa-list"></i>
                      </a>
                      @if($submission->form)
                        <a href="{{ route('admin.forms.download', $submission->form) }}" 
                           class="btn btn-success btn-sm" title="Download CSV">
                          <i class="fas fa-download"></i>
                        </a>
                      @endif
                    </div>
                  </td>
                </tr>

                <!-- Submission Details Modal -->
                <div class="modal fade" id="submissionModal{{ $submission->id }}" tabindex="-1">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Submission Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-6">
                            <strong>Form:</strong> {{ $submission->form->title ?? 'Unknown' }}
                          </div>
                          <div class="col-md-6">
                            <strong>Submitted:</strong> {{ $submission->created_at->format('M d, Y H:i') }}
                          </div>
                        </div>
                        <div class="row mt-2">
                          <div class="col-md-6">
                            <strong>User:</strong> {{ $submission->user->name ?? 'Guest' }}
                          </div>
                          <div class="col-md-6">
                            <strong>Email:</strong> {{ $submission->user->email ?? 'N/A' }}
                          </div>
                        </div>
                        
                        <hr>
                        
                        <h6>Submitted Data:</h6>
                        @if($submission->data && is_array($submission->data))
                          <div class="table-responsive">
                            <table class="table table-sm">
                              @foreach($submission->data as $key => $value)
                                <tr>
                                  <td width="30%"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong></td>
                                  <td>
                                    @if(is_array($value))
                                      {{ implode(', ', $value) }}
                                    @else
                                      {{ $value }}
                                    @endif
                                  </td>
                                </tr>
                              @endforeach
                            </table>
                          </div>
                        @else
                          <p class="text-muted">No data available</p>
                        @endif
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <div class="d-flex justify-content-center">
          {{ $submissions->links() }}
        </div>
      </div>
    </div>
  @else
    <div class="card shadow">
      <div class="card-body text-center py-5">
        <i class="fas fa-inbox text-muted mb-4" style="font-size: 4rem;"></i>
        <h4 class="text-muted mb-3">No Submissions Found</h4>
        <p class="text-muted mb-4">No one has submitted any forms yet. Create forms and share them to start collecting data.</p>
        <a href="{{ route('admin.forms.index') }}" class="btn btn-primary">
          <i class="fas fa-clipboard-list me-2"></i>View Forms
        </a>
      </div>
    </div>
  @endif
</div>
@endsection
