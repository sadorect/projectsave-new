@extends('admin.layouts.app')

@section('title', 'Create Ministry Report')

@section('content')
<div class="container-fluid">
    @include('components.alerts')

    <form action="{{ route('admin.reports.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1">Create Ministry Report</h1>
                <p class="text-muted mb-0">Document outreaches, testimonies, impact metrics, and prayer needs in one page.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Publish Report</button>
            </div>
        </div>

        @include('admin.reports._form')
    </form>
</div>
@endsection
