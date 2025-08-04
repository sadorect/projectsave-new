@extends('admin.layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-2 text-gray-800"><i class="fas fa-plus me-2"></i>Create New Form</h1>
      <p class="text-muted">Design a custom form to collect data from users</p>
    </div>
    <a href="{{ route('admin.forms.index') }}" class="btn btn-secondary">
      <i class="fas fa-arrow-left me-2"></i>Back to Forms
    </a>
  </div>

  @include('components.alerts')

  <div class="row">
    <div class="col-lg-8">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Form Details</h6>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.forms.store') }}" id="form-create">
            @csrf
            
            <div class="row">
              <div class="col-md-8 mb-3">
                <label class="form-label"><i class="fas fa-heading me-1"></i>Form Title <span class="text-danger">*</span></label>
                <input name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" 
                       placeholder="Enter form title" required>
                @error('title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label"><i class="fas fa-lock me-1"></i>Access Level</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="require_login" value="1" 
                         id="require_login" {{ old('require_login') ? 'checked' : '' }}>
                  <label class="form-check-label" for="require_login">
                    Require Login
                  </label>
                </div>
                <small class="form-text text-muted">When enabled, only logged-in users can submit this form</small>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label"><i class="fas fa-align-left me-1"></i>Description</label>
              <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                        rows="3" placeholder="Provide a brief description of this form's purpose">{{ old('description') }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Form Fields</h5>
                <button type="button" class="btn btn-success btn-sm" id="add-field">
                  <i class="fas fa-plus me-1"></i>Add Field
                </button>
              </div>
              
              <div id="fields">
                <div class="field-group card mb-3" data-index="0">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-4 mb-3">
                        <label class="form-label">Field Label <span class="text-danger">*</span></label>
                        <input name="fields[0][label]" value="{{ old('fields.0.label') }}" 
                               placeholder="Enter field label" required 
                               class="form-control @error('fields.0.label') is-invalid @enderror">
                        @error('fields.0.label')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                      
                      <div class="col-md-3 mb-3">
                        <label class="form-label">Field Type <span class="text-danger">*</span></label>
                        <select name="fields[0][type]" required 
                                class="form-control @error('fields.0.type') is-invalid @enderror field-type-select">
                          <option value="text" {{ old('fields.0.type') == 'text' ? 'selected' : '' }}>Text Input</option>
                          <option value="textarea" {{ old('fields.0.type') == 'textarea' ? 'selected' : '' }}>Textarea</option>
                          <option value="select" {{ old('fields.0.type') == 'select' ? 'selected' : '' }}>Select Dropdown</option>
                          <option value="checkbox" {{ old('fields.0.type') == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                          <option value="radio" {{ old('fields.0.type') == 'radio' ? 'selected' : '' }}>Radio Buttons</option>
                          <option value="email" {{ old('fields.0.type') == 'email' ? 'selected' : '' }}>Email</option>
                          <option value="number" {{ old('fields.0.type') == 'number' ? 'selected' : '' }}>Number</option>
                          <option value="date" {{ old('fields.0.type') == 'date' ? 'selected' : '' }}>Date</option>
                        </select>
                        @error('fields.0.type')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                      
                      <div class="col-md-3 mb-3">
                        <label class="form-label">Options</label>
                        <input name="fields[0][options]" value="{{ old('fields.0.options') }}" 
                               placeholder="Comma separated (for select/radio)" 
                               class="form-control options-input @error('fields.0.options') is-invalid @enderror"
                               style="display: none;">
                        @error('fields.0.options')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                      
                      <div class="col-md-2 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="form-check">
                          <input type="hidden" name="fields[0][required]" value="0">
                          <input type="checkbox" name="fields[0][required]" value="1" 
                                 class="form-check-input" id="required_0" 
                                 {{ old('fields.0.required') ? 'checked' : '' }}>
                          <label class="form-check-label" for="required_0">
                            Required
                          </label>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm remove-field mt-2" style="display:none;">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-4">
              <label class="form-label"><i class="fas fa-envelope me-1"></i>Notification Emails</label>
              <input name="notify_emails" value="{{ old('notify_emails') }}" 
                     class="form-control @error('notify_emails') is-invalid @enderror" 
                     id="notify_emails" placeholder="admin@example.com, manager@example.com">
              <small class="form-text text-muted">Enter email addresses separated by commas. These people will receive notifications when someone submits this form.</small>
              @error('notify_emails')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="d-flex justify-content-between">
              <a href="{{ route('admin.forms.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>Cancel
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Create Form
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <div class="col-lg-4">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle me-1"></i>Help & Tips</h6>
        </div>
        <div class="card-body">
          <h6 class="text-primary">Field Types Guide:</h6>
          <ul class="list-unstyled small">
            <li><strong>Text Input:</strong> Single line text entry</li>
            <li><strong>Textarea:</strong> Multi-line text entry</li>
            <li><strong>Select Dropdown:</strong> Choose one option from a list</li>
            <li><strong>Checkbox:</strong> Single yes/no option</li>
            <li><strong>Radio Buttons:</strong> Choose one from multiple options</li>
            <li><strong>Email:</strong> Email address with validation</li>
            <li><strong>Number:</strong> Numeric input only</li>
            <li><strong>Date:</strong> Date picker</li>
          </ul>
          
          <hr>
          
          <h6 class="text-primary">Form Settings:</h6>
          <ul class="list-unstyled small">
            <li><strong>Access Level:</strong> Control who can submit</li>
            <li><strong>Notifications:</strong> Get email alerts for new submissions</li>
            <li><strong>Required Fields:</strong> Make certain fields mandatory</li>
          </ul>
          
          <div class="alert alert-info mt-3">
            <small><i class="fas fa-lightbulb me-1"></i><strong>Tip:</strong> You can reorder and edit fields after creating the form.</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  let fieldIndex = 1;
  const fieldsContainer = document.getElementById('fields');
  const addFieldBtn = document.getElementById('add-field');

  // Show/hide options input based on field type
  function toggleOptionsInput(selectElement) {
    const fieldGroup = selectElement.closest('.field-group');
    const optionsInput = fieldGroup.querySelector('.options-input');
    const fieldType = selectElement.value;
    
    if (fieldType === 'select' || fieldType === 'radio') {
      optionsInput.style.display = 'block';
      optionsInput.required = true;
    } else {
      optionsInput.style.display = 'none';
      optionsInput.required = false;
      optionsInput.value = '';
    }
  }

  // Initialize options visibility for existing fields
  document.querySelectorAll('.field-type-select').forEach(function(select) {
    toggleOptionsInput(select);
    select.addEventListener('change', function() {
      toggleOptionsInput(this);
    });
  });

  addFieldBtn.addEventListener('click', function() {
    const template = document.querySelector('.field-group');
    const newField = template.cloneNode(true);
    newField.setAttribute('data-index', fieldIndex);

    // Update all field names and IDs
    newField.querySelectorAll('input, select').forEach(function(input) {
      if (input.name) {
        input.name = input.name.replace(/\[0\]/, `[${fieldIndex}]`);
      }
      if (input.id) {
        input.id = input.id.replace(/_0$/, `_${fieldIndex}`);
      }
      // Reset values
      if (input.type === 'checkbox') {
        input.checked = false;
      } else {
        input.value = '';
      }
    });

    // Update label for attributes
    newField.querySelectorAll('label[for]').forEach(function(label) {
      label.setAttribute('for', label.getAttribute('for').replace(/_0$/, `_${fieldIndex}`));
    });

    // Show remove button and hide options input
    const removeBtn = newField.querySelector('.remove-field');
    removeBtn.style.display = 'block';
    
    const optionsInput = newField.querySelector('.options-input');
    optionsInput.style.display = 'none';
    optionsInput.required = false;

    // Add event listener for the new field type select
    const newSelect = newField.querySelector('.field-type-select');
    newSelect.addEventListener('change', function() {
      toggleOptionsInput(this);
    });

    fieldsContainer.appendChild(newField);
    fieldIndex++;
  });

  // Handle remove field
  fieldsContainer.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-field') || e.target.closest('.remove-field')) {
      const fieldGroup = e.target.closest('.field-group');
      if (document.querySelectorAll('.field-group').length > 1) {
        fieldGroup.remove();
      } else {
        alert('You must have at least one field in your form.');
      }
    }
  });

  // Convert comma-separated values to arrays before submit
  document.getElementById('form-create').addEventListener('submit', function(e) {
    // Convert notify_emails
    const notifyInput = document.getElementById('notify_emails');
    if (notifyInput && notifyInput.value.trim() !== '') {
      const emails = notifyInput.value.split(',').map(e => e.trim()).filter(e => e.length > 0);
      notifyInput.remove();
      emails.forEach((email, idx) => {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = `notify_emails[${idx}]`;
        hidden.value = email;
        this.appendChild(hidden);
      });
    }

    // Convert field options to arrays
    document.querySelectorAll('.field-group').forEach(function(group) {
      const optionsInput = group.querySelector('.options-input');
      if (optionsInput && optionsInput.value.trim() !== '') {
        const indexMatch = optionsInput.name.match(/fields\[(\d+)\]\[options\]/);
        if (indexMatch) {
          const idx = indexMatch[1];
          const options = optionsInput.value.split(',').map(o => o.trim()).filter(o => o.length > 0);
          optionsInput.remove();
          options.forEach((option, oidx) => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = `fields[${idx}][options][${oidx}]`;
            hidden.value = option;
            group.appendChild(hidden);
          });
        }
      }
    });
  });
});
</script>
@endpush

@endsection