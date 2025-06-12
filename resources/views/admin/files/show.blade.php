@extends('admin.layouts.app')

@section('title', 'File Details')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>File Details</h2>
            <div class="btn-group">
                <a href="{{ route('admin.files.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Files
                </a>
                @if($fileExists)
                    <a href="{{ route('admin.files.download', $file) }}" class="btn btn-primary">
                        <i class="fas fa-download"></i> Download
                    </a>
                @endif
                <form action="{{ route('admin.files.destroy', $file) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this file?')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>File Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold">Original Name:</td>
                                        <td>{{ $file->original_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">System Name:</td>
                                        <td><code>{{ $file->filename }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">File Size:</td>
                                        <td>{{ $file->formatted_size }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">MIME Type:</td>
                                        <td><span class="badge bg-light text-dark">{{ $file->mime_type }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Category:</td>
                                        <td><span class="badge bg-secondary">{{ ucfirst($file->category) }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Privacy:</td>
                                        <td>
                                            <span class="badge {{ $file->is_private ? 'bg-warning' : 'bg-success' }}">
                                                <i class="fas {{ $file->is_private ? 'fa-lock' : 'fa-unlock' }}"></i>
                                                {{ $file->is_private ? 'Private' : 'Public' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold">Uploaded:</td>
                                        <td>{{ $file->created_at->format('M d, Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Updated:</td>
                                        <td>{{ $file->updated_at->format('M d, Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">File Path:</td>
                                        <td><code>{{ $file->path }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">File Exists:</td>
                                        <td>
                                            <span class="badge {{ $fileExists ? 'bg-success' : 'bg-danger' }}">
                                                {{ $fileExists ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @if($file->expires_at)
                                        <tr>
                                            <td class="fw-bold">Expires:</td>
                                            <td>
                                                {{ $file->expires_at->format('M d, Y H:i:s') }}
                                                @if($file->isExpired())
                                                    <span class="badge bg-danger ms-2">Expired</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                  </table>
                                </div>
                            </div>
    
                            @if($file->metadata && count($file->metadata) > 0)
                                <hr>
                                <h6>Metadata</h6>
                                <div class="row">
                                    @foreach($file->metadata as $key => $value)
                                        <div class="col-md-6">
                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                            @if(is_array($value))
                                                {{ implode(' x ', $value) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
    
                    <!-- File Preview -->
                    @if($fileExists && str_starts_with($file->mime_type, 'image/'))
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Preview</h5>
                            </div>
                            <div class="card-body text-center">
                                <img src="{{ route('admin.files.download', $file) }}" 
                                     alt="{{ $file->original_name }}" 
                                     class="img-fluid" 
                                     style="max-height: 400px;">
                            </div>
                        </div>
                    @endif
                </div>
    
                <div class="col-md-4">
                    <!-- Owner Information -->
                    <div class="card">
                        <div class="card-header">
                            <h5>File Owner</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-circle bg-primary text-white me-3">
                                    {{ strtoupper(substr($file->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $file->user->name }}</h6>
                                    <small class="text-muted">{{ $file->user->email }}</small>
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h5 class="mb-0">{{ $file->user->files()->count() }}</h5>
                                        <small class="text-muted">Total Files</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h5 class="mb-0">{{ $file->user->files()->sum('size') ? number_format($file->user->files()->sum('size') / 1024 / 1024, 1) . ' MB' : '0 MB' }}</h5>
                                    <small class="text-muted">Total Size</small>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.users.show', $file->user) }}" class="btn btn-outline-primary btn-sm w-100">
                                    View User Profile
                                </a>
                            </div>
                        </div>
                    </div>
    
                    <!-- Actions -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5>Actions</h5>
                        </div>
                        <div class="card-body">
                            <!-- Privacy Toggle -->
                            <form action="{{ route('admin.files.update-privacy', $file) }}" method="POST" class="mb-3">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_private" value="{{ $file->is_private ? '0' : '1' }}">
                                <button type="submit" class="btn {{ $file->is_private ? 'btn-success' : 'btn-warning' }} w-100">
                                    <i class="fas {{ $file->is_private ? 'fa-unlock' : 'fa-lock' }}"></i>
                                    Make {{ $file->is_private ? 'Public' : 'Private' }}
                                </button>
                            </form>
    
                            @if($fileExists)
                                <a href="{{ route('admin.files.download', $file) }}" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-download"></i> Download File
                                </a>
                            @endif
    
                            <form action="{{ route('admin.files.destroy', $file) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100" 
                                        onclick="return confirm('Are you sure you want to delete this file? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i> Delete File
                                </button>
                            </form>
                        </div>
                    </div>
    
                    <!-- File Status -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5>Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>File Exists:</span>
                                <span class="badge {{ $fileExists ? 'bg-success' : 'bg-danger' }}">
                                    {{ $fileExists ? 'Yes' : 'Missing' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Privacy:</span>
                                <span class="badge {{ $file->is_private ? 'bg-warning' : 'bg-success' }}">
                                    {{ $file->is_private ? 'Private' : 'Public' }}
                                </span>
                            </div>
                            @if($file->expires_at)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Expiration:</span>
                                    <span class="badge {{ $file->isExpired() ? 'bg-danger' : 'bg-info' }}">
                                        {{ $file->isExpired() ? 'Expired' : 'Active' }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <style>
            .avatar-circle {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
            }
        </style>
    @endsection
    