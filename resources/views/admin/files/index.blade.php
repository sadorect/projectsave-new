@extends('admin.layouts.app')

@section('title', 'File Management')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>File Management</h2>
            <div class="btn-group">
                <a href="{{ route('admin.files.analysis') }}" class="btn btn-info">
                    <i class="fas fa-chart-bar"></i> Storage Analysis
                </a>
                <form action="{{ route('admin.files.cleanup-expired') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Clean up all expired files?')">
                        <i class="fas fa-broom"></i> Cleanup Expired ({{ $stats['expired_files'] }})
                    </button>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ number_format($stats['total_files']) }}</h4>
                                <p class="mb-0">Total Files</p>
                            </div>
                            <i class="fas fa-file fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $stats['total_size'] }}</h4>
                                <p class="mb-0">Total Size</p>
                            </div>
                            <i class="fas fa-hdd fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ number_format($stats['total_users_with_files']) }}</h4>
                                <p class="mb-0">Users with Files</p>
                            </div>
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ number_format($stats['files_today']) }}</h4>
                                <p class="mb-0">Uploaded Today</p>
                            </div>
                            <i class="fas fa-upload fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">File Type</label>
                        <select name="mime_type" class="form-select">
                            <option value="">All Types</option>
                            @foreach($mimeTypes as $type)
                                <option value="{{ $type }}" {{ request('mime_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Privacy</label>
                        <select name="is_private" class="form-select">
                            <option value="">All Files</option>
                            <option value="1" {{ request('is_private') === '1' ? 'selected' : '' }}>Private</option>
                            <option value="0" {{ request('is_private') === '0' ? 'selected' : '' }}>Public</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search filename..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="{{ route('admin.files.index') }}" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="bulkForm" action="{{ route('admin.files.bulk-delete') }}" method="POST">
                    @csrf
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="button" id="selectAll" class="btn btn-sm btn-outline-primary">Select All</button>
                            <button type="button" id="selectNone" class="btn btn-sm btn-outline-secondary">Select None</button>
                            <span id="selectedCount" class="ms-3 text-muted">0 selected</span>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-danger" id="bulkDeleteBtn" style="display: none;" 
                                    onclick="return confirm('Delete selected files? This action cannot be undone.')">
                                <i class="fas fa-trash"></i> Delete Selected
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Files Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="masterCheckbox">
                                </th>
                                <th>File</th>
                                <th>Owner</th>
                                <th>Size</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Privacy</th>
                                <th>Uploaded</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($files as $file)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="file_ids[]" value="{{ $file->id }}" class="file-checkbox">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas {{ getFileIcon($file->mime_type) }} me-2 text-muted"></i>
                                            <div>
                                                <div class="fw-bold">{{ Str::limit($file->original_name, 30) }}</div>
                                                <small class="text-muted">{{ $file->filename }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $file->user->name }}</div>
                                            <small class="text-muted">{{ $file->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $file->formatted_size }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $file->mime_type }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($file->category) }}</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.files.update-privacy', $file) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_private" value="{{ $file->is_private ? '0' : '1' }}">
                                            <button type="submit" class="btn btn-sm {{ $file->is_private ? 'btn-warning' : 'btn-success' }}">
                                                <i class="fas {{ $file->is_private ? 'fa-lock' : 'fa-unlock' }}"></i>
                                                {{ $file->is_private ? 'Private' : 'Public' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $file->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $file->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.files.show', $file) }}" class="btn btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.files.download', $file) }}" class="btn btn-outline-primary" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <form action="{{ route('admin.files.destroy', $file) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete"
                                                        onclick="return confirm('Delete this file?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No files found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $files->firstItem() ?? 0 }} to {{ $files->lastItem() ?? 0 }} of {{ $files->total() }} files
                    </div>
                    {{ $files->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const masterCheckbox = document.getElementById('masterCheckbox');
            const fileCheckboxes = document.querySelectorAll('.file-checkbox');
            const selectedCount = document.getElementById('selectedCount');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const bulkForm = document.getElementById('bulkForm');

            function updateSelectedCount() {
                const checked = document.querySelectorAll('.file-checkbox:checked').length;
                selectedCount.textContent = `${checked} selected`;
                bulkDeleteBtn.style.display = checked > 0 ? 'block' : 'none';
            }

            masterCheckbox.addEventListener('change', function() {
                fileCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedCount();
            });

            fileCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            document.getElementById('selectAll').addEventListener('click', function() {
                fileCheckboxes.forEach(checkbox => checkbox.checked = true);
                masterCheckbox.checked = true;
                updateSelectedCount();
            });

            document.getElementById('selectNone').addEventListener('click', function() {
                fileCheckboxes.forEach(checkbox => checkbox.checked = false);
                masterCheckbox.checked = false;
                updateSelectedCount();
            });

            bulkForm.addEventListener('submit', function(e) {
                const checkedBoxes = document.querySelectorAll('.file-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    e.preventDefault();
                    alert('Please select files to delete');
                }
            });
        });
    </script>
    @endpush
@endsection

@php
function getFileIcon($mimeType) {
    if (str_starts_with($mimeType, 'image/')) return 'fa-image';
    if (str_starts_with($mimeType, 'video/')) return 'fa-video';
    if (str_starts_with($mimeType, 'audio/')) return 'fa-music';
    if ($mimeType === 'application/pdf') return 'fa-file-pdf';
    if (str_contains($mimeType, 'word')) return 'fa-file-word';
    if (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheet')) return 'fa-file-excel';
    if (str_contains($mimeType, 'powerpoint') || str_contains($mimeType, 'presentation')) return 'fa-file-powerpoint';
    if (str_starts_with($mimeType, 'text/')) return 'fa-file-alt';
    return 'fa-file';
}
@endphp
