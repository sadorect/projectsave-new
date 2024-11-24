@extends('admin.layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h1>Create New Role</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
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

                <div class="mb-4">
                    <label class="form-label">Permissions</label>
                    <div class="row">
                        @foreach($permissions->groupBy('category') as $category => $categoryPermissions)
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">{{ $category }}</h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($categoryPermissions as $permission)
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input" 
                                                       name="permissions[]" 
                                                       value="{{ $permission->id }}" 
                                                       id="permission_{{ $permission->id }}"
                                                       {{ (old('permissions') && in_array($permission->id, old('permissions'))) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                    <small class="d-block text-muted">{{ $permission->description }}</small>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

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