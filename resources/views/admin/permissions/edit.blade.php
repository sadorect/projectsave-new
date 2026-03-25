@extends('admin.layouts.app')

@section('title', 'Edit Permission')
@section('page_subtitle', 'Update the permission definition and immediately review which roles inherit it.')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="mb-3">
                            <label for="name" class="form-label">Permission Name</label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $permission->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" id="category" name="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category', $permission->category) }}">
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $permission->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="small text-muted">
                            Slug: {{ $permission->slug }}
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="border rounded p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-1">Assigned Roles</h6>
                                    <div class="small text-muted">Choose which roles inherit this permission.</div>
                                </div>
                                <span class="badge bg-light text-dark">{{ $permission->roles->count() }} selected</span>
                            </div>

                            @foreach($roles as $role)
                                <div class="form-check mb-2">
                                    <input
                                        type="checkbox"
                                        name="roles[]"
                                        value="{{ $role->id }}"
                                        class="form-check-input"
                                        id="role-{{ $role->id }}"
                                        {{ in_array($role->id, old('roles', $permission->roles->pluck('id')->all()), true) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="role-{{ $role->id }}">
                                        {{ $role->name }}
                                        <span class="d-block small text-muted">{{ $role->slug }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Update Permission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection