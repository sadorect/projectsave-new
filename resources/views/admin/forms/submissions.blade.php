@extends('admin.layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-2 text-gray-800"><i class="fas fa-paper-plane me-2"></i>Form Submissions</h1>
      <p class="text-muted">Submissions for: <strong>{{ $form->title ?? 'Unknown Form' }}</strong></p>
    </div>
    <div>
      @if($submissions && $submissions->count() > 0)
        <a href="{{ route('admin.forms.download', $form) }}" class="btn btn-success me-2">
          <i class="fas fa-download me-1"></i>Export CSV
        </a>
      @endif
      <a href="{{ route('admin.forms.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Forms
      </a>
    </div>
  </div>

  @include('components.alerts')

  <!-- Form Info Card -->
  <div class="row mb-4">
    <div class="col-lg-8">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Form Information</h6>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Title:</strong> {{ $form->title ?? 'N/A' }}</p>
              <p><strong>Description:</strong> {{ $form->description ?? 'No description' }}</p>
            </div>
            <div class="col-md-6">
              <p><strong>Total Fields:</strong> {{ count($form->fields ?? []) }}</p>
              <p><strong>Access:</strong> 
                @if($form->require_login)
                  <span class="badge bg-warning">Login Required</span>
                @else
                  <span class="badge bg-success">Public Access</span>
                @endif
              </p>
              <p><strong>Created:</strong> {{ $form->created_at->format('M d, Y') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
        </div>
        <div class="card-body text-center">
          <div class="row">
            <div class="col">
              <h3 class="text-primary">{{ $submissions->total() ?? 0 }}</h3>
              <p class="text-muted mb-0">Total Submissions</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if($submissions && $submissions->count() > 0)
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Submissions ({{ $submissions->total() }})</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          @foreach($submissions as $submission)
            <div class="card mb-3">
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col-md-3">
                    <strong>Submission #{{ $submission->id }}</strong>
                    <br><small class="text-muted">{{ $submission->created_at->format('M d, Y H:i') }}</small>
                  </div>
                  <div class="col-md-4">
                    @if($submission->user)
                      <i class="fas fa-user text-primary me-1"></i>{{ $submission->user->name }}
                      <br><small class="text-muted"><i class="fas fa-envelope me-1"></i>{{ $submission->user->email }}</small>
                    @else
                      <i class="fas fa-user-circle text-muted me-1"></i>Guest User
                    @endif
                  </div>
                  <div class="col-md-3">
                    <span class="badge bg-success">
                      <i class="fas fa-check me-1"></i>Submitted {{ $submission->created_at->diffForHumans() }}
                    </span>
                  </div>
                  <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" 
                            data-bs-target="#submission{{ $submission->id }}" aria-expanded="false">
                      <i class="fas fa-eye me-1"></i>View Details
                    </button>
                  </div>
                </div>
              </div>
              
              <div class="collapse" id="submission{{ $submission->id }}">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <h6 class="text-primary mb-3"><i class="fas fa-clipboard-list me-2"></i>Submitted Data</h6>
                      @if($submission->data && is_array($submission->data))
                        @foreach($submission->data as $key => $value)
                          @php
                            // Try to find the field label from form fields
                            $fieldLabel = $key;
                            if ($form->fields) {
                              foreach ($form->fields as $field) {
                                if (isset($field['name']) && $field['name'] === $key) {
                                  $fieldLabel = $field['label'] ?? $key;
                                  break;
                                }
                              }
                            }
                            $fieldLabel = ucfirst(str_replace('_', ' ', $fieldLabel));
                          @endphp
                          <div class="mb-2 p-2 bg-light rounded">
                            <strong class="text-dark">{{ $fieldLabel }}:</strong>
                            <div class="mt-1">
                              @if(is_array($value))
                                <span class="badge bg-info">{{ implode(', ', $value) }}</span>
                              @elseif(filter_var($value, FILTER_VALIDATE_EMAIL))
                                <a href="mailto:{{ $value }}" class="text-decoration-none">
                                  <i class="fas fa-envelope me-1"></i>{{ $value }}
                                </a>
                              @elseif(filter_var($value, FILTER_VALIDATE_URL))
                                <a href="{{ $value }}" target="_blank" class="text-decoration-none">
                                  <i class="fas fa-external-link-alt me-1"></i>{{ $value }}
                                </a>
                              @else
                                <span class="text-muted">{{ $value ?: '(No response)' }}</span>
                              @endif
                            </div>
                          </div>
                        @endforeach
                      @else
                        <div class="alert alert-warning">
                          <i class="fas fa-exclamation-triangle me-2"></i>No form data available for this submission.
                        </div>
                      @endif
                    </div>
                    
                    <div class="col-md-6">
                      <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Submission Info</h6>
                      <div class="mb-2 p-2 bg-light rounded">
                        <strong>Submission ID:</strong> #{{ $submission->id }}
                      </div>
                      <div class="mb-2 p-2 bg-light rounded">
                        <strong>Submitted At:</strong> {{ $submission->created_at->format('l, F j, Y \a\t g:i A') }}
                      </div>
                      <div class="mb-2 p-2 bg-light rounded">
                        <strong>Time Ago:</strong> {{ $submission->created_at->diffForHumans() }}
                      </div>
                      @if($submission->user)
                        <div class="mb-2 p-2 bg-light rounded">
                          <strong>User Account:</strong> Registered User
                        </div>
                      @else
                        <div class="mb-2 p-2 bg-light rounded">
                          <strong>User Account:</strong> <span class="text-muted">Guest (No account)</span>
                        </div>
                      @endif
                      
                      <div class="mt-3">
                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                onclick="deleteSubmission({{ $submission->id }})" title="Delete Submission">
                          <i class="fas fa-trash me-1"></i>Delete
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
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
        <h4 class="text-muted mb-3">No Submissions Yet</h4>
        <p class="text-muted mb-4">This form hasn't received any submissions yet. Share the form link to start collecting responses.</p>
        <div class="input-group mb-3" style="max-width: 500px; margin: 0 auto;">
          <input type="text" class="form-control" id="formUrl" value="{{ route('forms.show', $form) }}" readonly>
          <button class="btn btn-outline-secondary" type="button" onclick="copyFormUrl()">
            <i class="fas fa-copy"></i> Copy Link
          </button>
        </div>
        <a href="{{ route('forms.show', $form) }}" class="btn btn-primary" target="_blank">
          <i class="fas fa-external-link-alt me-2"></i>Preview Form
        </a>
      </div>
    </div>
  @endif
</div>

@push('scripts')
<script>
function copyFormUrl() {
  const urlInput = document.getElementById('formUrl');
  urlInput.select();
  document.execCommand('copy');
  
  // Show feedback
  const button = event.target.closest('button');
  const originalText = button.innerHTML;
  button.innerHTML = '<i class="fas fa-check"></i> Copied!';
  button.classList.remove('btn-outline-secondary');
  button.classList.add('btn-success');
  
  setTimeout(() => {
    button.innerHTML = originalText;
    button.classList.remove('btn-success');
    button.classList.add('btn-outline-secondary');
  }, 2000);
}

function deleteSubmission(submissionId) {
  if (confirm('Are you sure you want to delete this submission? This action cannot be undone.')) {
    // Create a form to submit the delete request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/submissions/${submissionId}`;
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Add method spoofing for DELETE
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);
    
    document.body.appendChild(form);
    form.submit();
  }
}
</script>
@endpush
@endsection