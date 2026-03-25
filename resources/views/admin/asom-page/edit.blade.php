@extends('admin.layouts.app')

@section('title', 'ASOM Page Settings')
@section('page_subtitle', 'Edit the ASOM landing and catalogue copy from one structured settings screen.')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-primary-subtle text-primary px-3 py-2">{{ count($pageContent) }} sections</span>
            <span class="badge bg-secondary-subtle text-secondary px-3 py-2">Nested page configuration editor</span>
        </div>
        <a href="{{ route('asom') }}" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-box-arrow-up-right me-2"></i>Preview Public Page
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.asom-page.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="alert alert-info mb-4">
                    Update text, links, and labels for the ASOM public experience. Changes are saved as structured settings and applied without editing templates.
                </div>

                @foreach($pageContent as $sectionKey => $sectionValue)
                    <div class="card shadow-sm border-0 bg-light mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-1">{{ \Illuminate\Support\Str::headline((string) $sectionKey) }}</h5>
                            <div class="small text-muted">Section key: {{ $sectionKey }}</div>
                        </div>
                        <div class="card-body">
                            @include('admin.asom-page.partials.fields', [
                                'fieldKey' => $sectionKey,
                                'fieldValue' => $sectionValue,
                                'fieldPath' => $sectionKey,
                            ])
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Save ASOM Page Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection