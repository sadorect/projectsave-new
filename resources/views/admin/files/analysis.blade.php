@extends('admin.layouts.app')

@section('title', 'Storage Analysis')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Storage Analysis</h2>
            <p class="text-muted mb-0">See how uploaded files are distributed across owners, categories, and file types.</p>
        </div>
        <a href="{{ route('admin.files.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to files
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Total Files</div>
                    <div class="display-6 mb-0">{{ number_format($analysis['total_files']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Total Storage</div>
                    <div class="display-6 mb-0">{{ number_format(($analysis['total_size'] ?? 0) / 1024 / 1024, 2) }} MB</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Categories Tracked</div>
                    <div class="display-6 mb-0">{{ number_format($analysis['by_category']->count()) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Top File Owners</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Files</th>
                                    <th>Total Size</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($analysis['by_user'] as $row)
                                    <tr>
                                        <td>{{ $row->name }}</td>
                                        <td>{{ number_format($row->file_count) }}</td>
                                        <td>{{ number_format(($row->total_size ?? 0) / 1024 / 1024, 2) }} MB</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-muted text-center py-4">No file owner data available yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">By Category</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Files</th>
                                    <th>Total Size</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($analysis['by_category'] as $row)
                                    <tr>
                                        <td>{{ $row->category ?: 'Uncategorized' }}</td>
                                        <td>{{ number_format($row->file_count) }}</td>
                                        <td>{{ number_format(($row->total_size ?? 0) / 1024 / 1024, 2) }} MB</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-muted text-center py-4">No category data available yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">By MIME Type</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Files</th>
                                    <th>Total Size</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($analysis['by_type'] as $row)
                                    <tr>
                                        <td>{{ $row->mime_type }}</td>
                                        <td>{{ number_format($row->file_count) }}</td>
                                        <td>{{ number_format(($row->total_size ?? 0) / 1024 / 1024, 2) }} MB</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-muted text-center py-4">No MIME type data available yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Monthly Upload Volume</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Files</th>
                                    <th>Total Size</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($analysis['monthly_uploads'] as $row)
                                    <tr>
                                        <td>{{ $row->month }}</td>
                                        <td>{{ number_format($row->count) }}</td>
                                        <td>{{ number_format(($row->size ?? 0) / 1024 / 1024, 2) }} MB</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-muted text-center py-4">No upload trend data available yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
