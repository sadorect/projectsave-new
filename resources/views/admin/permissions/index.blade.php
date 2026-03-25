@extends('admin.layouts.app')

@section('title', 'Permissions Management')
@section('page_subtitle', 'Keep the full permission matrix visible by category while editing role coverage safely.')

@section('content')
@php
    $permissionCount = $permissions->flatten(1)->count();
    $categoryCount = $permissions->count();
    $selectedRoleIds = collect(old('roles', []))->map(fn ($roleId) => (int) $roleId)->all();
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-primary-subtle text-primary px-3 py-2">{{ $permissionCount }} permissions</span>
                <span class="badge bg-secondary-subtle text-secondary px-3 py-2">{{ $categoryCount }} categories</span>
                <span class="badge bg-info-subtle text-info px-3 py-2">{{ $roles->count() }} assignable roles</span>
            </div>

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
                <i class="bi bi-plus-circle me-2"></i>Create New Permission
            </button>
        </div>

        <div class="col-md-12">
            @foreach($permissions as $category => $categoryPermissions)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h3 class="card-title mb-1">{{ $category }}</h3>
                            <p class="text-muted mb-0 small">{{ $categoryPermissions->count() }} permissions in this category.</p>
                        </div>
                        <span class="badge bg-light text-dark">{{ $categoryPermissions->pluck('roles')->flatten()->unique('id')->count() }} roles using this category</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Permission</th>
                                        <th>Guard</th>
                                        <th>Assigned Roles</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categoryPermissions as $permission)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $permission->name }}</div>
                                                <div class="small text-muted mb-1">{{ $permission->slug }}</div>
                                                <div class="small text-muted">{{ $permission->description ?: 'No description provided.' }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $permission->guard_name }}</span>
                                            </td>
                                            <td>
                                                @forelse($permission->roles as $role)
                                                    <span class="badge bg-info me-1 mb-1">{{ $role->name }}</span>
                                                @empty
                                                    <span class="text-muted small">Not assigned to any role yet.</span>
                                                @endforelse
                                            </td>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil me-1"></i>Edit
                                                    </a>
                                                    <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" onsubmit="return confirm('Delete this permission? Roles will lose access immediately.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash me-1"></i>Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="modal fade" id="createPermissionModal" tabindex="-1" aria-labelledby="createPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createPermissionModalLabel">Create New Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="permission_name" class="form-label">Permission Name</label>
                        <input type="text" id="permission_name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="permission_category" class="form-label">Category</label>
                        <input type="text" id="permission_category" name="category" class="form-control" list="permission-category-options" value="{{ old('category', 'Custom') }}">
                    </div>
                    <div class="mb-3">
                        <label for="permission_description" class="form-label">Description</label>
                        <textarea id="permission_description" name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Guard</label>
                        <input type="text" class="form-control" value="{{ config('auth.defaults.guard', 'web') }}" readonly>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Assign to Roles</label>
                        @foreach($roles as $role)
                            <div class="form-check mb-2">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="form-check-input" id="role{{ $role->id }}" {{ in_array($role->id, $selectedRoleIds, true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role{{ $role->id }}">
                                    {{ $role->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>

<datalist id="permission-category-options">
    @foreach($categories as $category)
        <option value="{{ $category }}"></option>
    @endforeach
</datalist>
@endsection
