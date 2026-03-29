@extends('admin.layouts.app')

@section('title', 'Manage Ministry Reports')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="mb-1">Ministry Reports</h1>
            <p class="text-muted mb-0">Publish outreach updates, testimonies, field photos, and follow-up reports.</p>
        </div>
        <a href="{{ route('admin.reports.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Report
        </a>
    </div>

    @include('components.alerts')

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Impact</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $report->title }}</div>
                                    <div class="small text-muted">
                                        {{ $report->is_featured ? 'Featured report' : 'Standard report' }}
                                    </div>
                                </td>
                                <td>{{ $report->report_type }}</td>
                                <td>{{ optional($report->report_date)->format('M d, Y') }}</td>
                                <td>{{ $report->location ?: 'Unspecified' }}</td>
                                <td>
                                    <span class="badge bg-{{ $report->isPublished() ? 'success' : 'secondary' }}">
                                        {{ $report->isPublished() ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    {{ number_format($report->people_reached) }} reached
                                    <br>
                                    {{ number_format($report->souls_impacted) }} impacted
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        @if($report->isPublished())
                                            <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.reports.edit', $report) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this report and its uploaded images?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No ministry reports have been created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
