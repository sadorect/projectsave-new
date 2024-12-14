@extends('admin.layouts.app')

@section('title', 'Manage FAQs')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>FAQs</h1>
        <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add New FAQ
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faqs as $faq)
                            <tr>
                                <td>{{ $faq->title }}</td>
                                <td><span class="badge bg-{{ $faq->status === 'published' ? 'success' : 'warning' }}">
                                    {{ ucfirst($faq->status) }}
                                </span></td>
                                <td>{{ $faq->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No FAQs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $faqs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
