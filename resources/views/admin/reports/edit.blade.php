@extends('admin.layouts.app')

@section('title', 'Edit Ministry Report')

@section('content')
<div class="container-fluid">
    @include('components.alerts')

    <form action="{{ route('admin.reports.update', $report) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1">Edit Ministry Report</h1>
                <p class="text-muted mb-0">{{ $report->title }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Update Report</button>
            </div>
        </div>

        @include('admin.reports._form')
    </form>
</div>
@endsection
