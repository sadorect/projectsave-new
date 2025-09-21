@extends('admin.layouts.app')

@section('title', 'Certificate Settings')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="page-header">
      <h1>Certificate Settings</h1>
      <p class="text-muted">Configure certificate appearance and properties</p>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5>Certificate Configuration</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.certificate-settings.update') }}" method="POST" enctype="multipart/form-data">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="organization_tagline" class="form-label">Organization Tagline</label>
                <input type="text" class="form-control" id="organization_tagline" name="organization_tagline" 
                     value="{{ old('organization_tagline', $settings['organization_tagline']) }}" placeholder="e.g., Certificate Services">
                <small class="text-muted">This appears under the organization name on the certificate.</small>
              </div>
            </div>
          @csrf
          @method('PUT')

          <!-- Basic Settings -->
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="organization_name" class="form-label">Organization Name</label>
                <input type="text" class="form-control" id="organization_name" name="organization_name" 
                     value="{{ old('organization_name', $settings['organization_name']) }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="font_family" class="form-label">Font Family</label>
                <select class="form-control" id="font_family" name="font_family">
                  <option value="Helvetica Neue" {{ $settings['font_family'] == 'Helvetica Neue' ? 'selected' : '' }}>Helvetica Neue</option>
                  <option value="Georgia" {{ $settings['font_family'] == 'Georgia' ? 'selected' : '' }}>Georgia</option>
                  <option value="Times New Roman" {{ $settings['font_family'] == 'Times New Roman' ? 'selected' : '' }}>Times New Roman</option>
                  <option value="Arial" {{ $settings['font_family'] == 'Arial' ? 'selected' : '' }}>Arial</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Color Settings -->
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="primary_color" class="form-label">Primary Color</label>
                <input type="color" class="form-control" id="primary_color" name="primary_color" 
                     value="{{ old('primary_color', $settings['primary_color']) }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="secondary_color" class="form-label">Secondary Color</label>
                <input type="color" class="form-control" id="secondary_color" name="secondary_color" 
                     value="{{ old('secondary_color', $settings['secondary_color']) }}">
              </div>
            </div>
          </div>

          <!-- Logo Settings -->
          <div class="mb-3">
            <label for="logo" class="form-label">Certificate Logo</label>
            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
            <small class="text-muted">Current: {{ $settings['logo_path'] }}</small>
          </div>

          <hr>

          <!-- Director Signature Settings -->
          <h6>Director Signature Settings</h6>
          <p class="text-muted small">Configure the primary signatory (usually Program Director or Principal)</p>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="director_name" class="form-label">Director Full Name</label>
                <input type="text" class="form-control" id="director_name" name="director_name" 
                     value="{{ old('director_name', $settings['director_name']) }}"
                     placeholder="e.g., Rev. Dr. John Smith">
                <small class="text-muted">Full name as it should appear on certificate</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="director_title" class="form-label">Director Title/Position</label>
                <input type="text" class="form-control" id="director_title" name="director_title" 
                     value="{{ old('director_title', $settings['director_title']) }}"
                     placeholder="e.g., Program Director">
                <small class="text-muted">Official title or position</small>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="director_credentials" class="form-label">Director Credentials <span class="text-muted">(Optional)</span></label>
                <input type="text" class="form-control" id="director_credentials" name="director_credentials" 
                     value="{{ old('director_credentials', $settings['director_credentials'] ?? '') }}"
                     placeholder="e.g., Ph.D., M.Div., D.Min.">
                <small class="text-muted">Academic or professional credentials</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="director_organization" class="form-label">Director Organization <span class="text-muted">(Optional)</span></label>
                <input type="text" class="form-control" id="director_organization" name="director_organization" 
                     value="{{ old('director_organization', $settings['director_organization'] ?? '') }}"
                     placeholder="e.g., ASOM International">
                <small class="text-muted">Organization or institution name</small>
              </div>
            </div>
          </div>

          <!-- Director Signature Image -->
          <div class="mb-3">
            <label for="director_signature_image" class="form-label">Director Signature Image <span class="text-muted">(Optional)</span></label>
            <input type="file" class="form-control" id="director_signature_image" name="director_signature_image" accept="image/*">
            <small class="text-muted">Current: {{ $settings['director_signature_path'] ?? 'No image uploaded' }}</small>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="director_signature_width" class="form-label">Director Signature Width (px)</label>
                <input type="number" class="form-control" id="director_signature_width" name="director_signature_width" 
                     value="{{ old('director_signature_width', $settings['director_signature_width'] ?? 150) }}"
                     min="50" max="300" step="10">
                <small class="text-muted">Width in pixels (50-300)</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="director_signature_height" class="form-label">Director Signature Height (px)</label>
                <input type="number" class="form-control" id="director_signature_height" name="director_signature_height" 
                     value="{{ old('director_signature_height', $settings['director_signature_height'] ?? 75) }}"
                     min="25" max="150" step="5">
                <small class="text-muted">Height in pixels (25-150)</small>
              </div>
            </div>
          </div>

          <hr>

          <!-- Registrar Signature Settings -->
          <h6>Registrar Signature Settings</h6>
          <p class="text-muted small">Configure the secondary signatory (usually Academic Registrar)</p>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="registrar_name" class="form-label">Registrar Name</label>
                <input type="text" class="form-control" id="registrar_name" name="registrar_name" 
                     value="{{ old('registrar_name', $settings['registrar_name']) }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="registrar_title" class="form-label">Registrar Title</label>
                <input type="text" class="form-control" id="registrar_title" name="registrar_title" 
                     value="{{ old('registrar_title', $settings['registrar_title']) }}">
              </div>
            </div>
          </div>

          <!-- Registrar Signature Image -->
          <div class="mb-3">
            <label for="registrar_signature_image" class="form-label">Registrar Signature Image <span class="text-muted">(Optional)</span></label>
            <input type="file" class="form-control" id="registrar_signature_image" name="registrar_signature_image" accept="image/*">
            <small class="text-muted">Current: {{ $settings['registrar_signature_path'] ?? 'No image uploaded' }}</small>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="registrar_signature_width" class="form-label">Registrar Signature Width (px)</label>
                <input type="number" class="form-control" id="registrar_signature_width" name="registrar_signature_width" 
                     value="{{ old('registrar_signature_width', $settings['registrar_signature_width'] ?? 150) }}"
                     min="50" max="300" step="10">
                <small class="text-muted">Width in pixels (50-300)</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="registrar_signature_height" class="form-label">Registrar Signature Height (px)</label>
                <input type="number" class="form-control" id="registrar_signature_height" name="registrar_signature_height" 
                     value="{{ old('registrar_signature_height', $settings['registrar_signature_height'] ?? 75) }}"
                     min="25" max="150" step="5">
                <small class="text-muted">Height in pixels (25-150)</small>
              </div>
            </div>
          </div>

          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Update Settings</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Preview Section -->
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h6>Preview</h6>
      </div>
      <div class="card-body">
        <div class="certificate-preview" style="background: linear-gradient(135deg, {{ $settings['primary_color'] }} 0%, {{ $settings['secondary_color'] }} 60%); color: white; padding: 20px; border-radius: 8px; font-family: {{ $settings['font_family'] }};">
          <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
            <div style="width: 40px; height: 40px; background: white; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: {{ $settings['primary_color'] }}; font-weight: bold;">
              @if(file_exists(public_path($settings['logo_path'])))
                <img src="{{ asset($settings['logo_path']) }}" style="max-width: 100%; max-height: 100%;">
              @else
                LOGO
              @endif
            </div>
            <div>
              <div style="font-size: 12px; font-weight: bold;">{{ $settings['organization_name'] }}</div>
              <div style="font-size: 14px; opacity: 0.9;">{{ $settings['organization_tagline'] }}</div>
            </div>
          </div>
          <div style="font-size: 11px; margin-bottom: 4px;">This certifies that</div>
          <div style="font-size: 20px; font-weight: bold; margin-bottom: 8px;">STUDENT NAME</div>
          <div style="font-size: 11px; margin-bottom: 4px;">has completed</div>
          <div style="font-size: 14px; font-style: italic; margin-bottom: 12px;">Course Title</div>
          <div style="font-size: 10px; display: flex; justify-content: space-between;">
            <span>{{ $settings['director_name'] }}</span>
            <span>{{ $settings['registrar_name'] }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
