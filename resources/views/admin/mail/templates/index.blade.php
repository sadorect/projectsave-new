@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Email Templates</h2>
        <a href="{{ route('admin.mail-templates.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> New Template
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Subject</th>
                        <th>Variables</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                    <tr>
                        <td>{{ $template->name }}</td>
                        <td>{{ $template->subject }}</td>
                        <td>
                            @if($template->variables)
                                @foreach($template->variables as $variable)
                                    <span class="badge bg-info">{{ $variable }}</span>
                                @endforeach
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.mail-templates.edit', $template) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $templates->links() }}
        </div>
    </div>
</div>
@endsection
