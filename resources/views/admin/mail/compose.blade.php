@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.mail.send') }}" method="POST">
          @csrf
            <div class="mb-3">
                <label>Recipients</label>
                <select id="recipients" name="recipients[]" class="form-control select2" multiple>
                    <optgroup label="Groups">
                        @foreach($groups as $group)
                            <option value="group:{{ $group }}">{{ ucfirst($group) }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Individual Users">
                        @foreach($users as $user)
                            <option value="user:{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
            
            <div class="mb-3">
                <label>Template</label>
                <select id="template_id" name="template_id" class="form-control">
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Custom Message</label>
                <textarea id="custom_message" name="custom_message" class="form-control rich-editor"></textarea>
            </div>
            <button type="button" class="btn btn-info preview-mail" data-_="{{ $template->id }}">
              <i class="bi bi-eye"></i> Preview
          </button>
          
            <button type="submit" class="btn btn-primary">Send Mail</button>
        </form>
    </div>
</div>

<!-- Add this at the bottom of your compose view -->
<div class="modal fade" id="previewModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">Email Preview</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <!-- Preview content will be loaded here -->
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
      </div>
  </div>
</div>

@push('scripts')
    <script>
    $(document).ready(function() {
      $('.preview-mail').click(function() {
        const templateId = $(this).data('template_id');
        $.get(`/admin/mail/preview/${templateId}`, function(response) {
            $('#previewModal .modal-body').html(response);
            $('#previewModal').modal('show');
        });
    });
  });
    </script>
    @endpush
@endsection