@extends('layouts.user')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-folder-open me-2"></i>My Files</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fas fa-upload me-1"></i> Upload Files
                </button>
            </div>

            <!-- File Upload Modal -->
            <div class="modal fade" id="uploadModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><i class="fas fa-cloud-upload-alt me-2"></i>Upload Files</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Select Files</label>
                                    <input type="file" name="files[]" class="form-control form-control-lg" multiple required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Max 10MB per file. Allowed: Images, PDF, Word, Text files
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Category</label>
                                    <select name="category" class="form-select">
                                        <option value="general"><i class="fas fa-folder"></i> General</option>
                                        <option value="documents"><i class="fas fa-file-alt"></i> Documents</option>
                                        <option value="images"><i class="fas fa-images"></i> Images</option>
                                    </select>
                                </div>
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_private" value="1" class="form-check-input" id="privateSwitch" checked>
                                    <label class="form-check-label fw-bold" for="privateSwitch">
                                        <i class="fas fa-lock me-1"></i>Private (only you can access)
                                    </label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-1"></i>Upload Files
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Files Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-file fa-2x mb-2"></i>
                            <h5>{{ $files->total() }} ({{ auth()->user()->formatted_total_file_size}}) </h5>
                            <small>Total Files</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-images fa-2x mb-2"></i>
                            <h5>{{ $files->where('category', 'images')->count() }}</h5>
                            <small>Images</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt fa-2x mb-2"></i>
                            <h5>{{ $files->where('category', 'documents')->count() }}</h5>
                            <small>Documents</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-lock fa-2x mb-2"></i>
                            <h5>{{ $files->where('is_private', 1)->count() }}</h5>
                            <small>Private Files</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Files List -->
            <div class="row">
                @forelse($files as $file)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-0 file-card">
                            <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    @php
                                        $extension = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                                        $fileIcon = match($extension) {
                                            'pdf' => 'fas fa-file-pdf text-danger',
                                            'doc', 'docx' => 'fas fa-file-word text-primary',
                                            'xls', 'xlsx' => 'fas fa-file-excel text-success',
                                            'ppt', 'pptx' => 'fas fa-file-powerpoint text-warning',
                                            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'fas fa-file-image text-info',
                                            'txt' => 'fas fa-file-alt text-secondary',
                                            'zip', 'rar' => 'fas fa-file-archive text-dark',
                                            default => 'fas fa-file text-muted'
                                        };
                                    @endphp
                                    <i class="{{ $fileIcon }} fa-lg me-2"></i>
                                    <span class="badge bg-{{ $file->category === 'images' ? 'success' : ($file->category === 'documents' ? 'info' : 'secondary') }}">
                                        {{ ucfirst($file->category ?? 'general') }}
                                    </span>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary border-3 fw-bold" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v">Action</i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('files.download', $file) }}">
                                                <i class="fas fa-download me-2 text-primary"></i>Download
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('files.destroy', $file) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this file?')">
                                                    <i class="fas fa-trash me-2"></i>Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title text-truncate mb-3" title="{{ $file->original_name }}">
                                    {{ $file->original_name }}
                                </h6>
                                <div class="file-info">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small">
                                            <i class="fas fa-weight-hanging me-1"></i>{{ $file->formatted_size }}
                                        </span>
                                        @if($file->is_private)
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-lock me-1"></i>Private
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-globe me-1"></i>Public
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-calendar me-1"></i>{{ $file->created_at->format('M d, Y') }}
                                        <br>
                                        <i class="fas fa-clock me-1"></i>{{ $file->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="{{ route('files.download', $file) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-folder-open fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted mb-3">No files uploaded yet</h4>
                                <p class="text-muted mb-4">Start by uploading your first file to organize and manage your documents.</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <i class="fas fa-upload me-1"></i> Upload Your First File
                                </button>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($files->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $files->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.file-card {
    transition: all 0.3s ease;
    border-radius: 10px;
}

.file-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.file-info {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    margin-top: 10px;
}

.modal-content {
    border-radius: 15px;
    border: none;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.btn {
    border-radius: 8px;
}

.form-control, .form-select {
    border-radius: 8px;
}
</style>
@endsection
