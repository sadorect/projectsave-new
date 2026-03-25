@extends('admin.layouts.app')

@section('title', 'Create Role')
@section('page_subtitle', 'Build focused admin roles by assigning permissions in clear category groups.')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST" id="role-create-form">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" required value="{{ old('name') }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @include('admin.roles.partials.permission-matrix', [
                    'permissionGroups' => $permissionGroups,
                    'selectedPermissions' => old('permissions', []),
                    'formId' => 'role-create-form',
                ])

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection