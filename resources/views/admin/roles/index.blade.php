@extends('admin.layouts.app')

@section('title', 'Roles Management')
@section('page_subtitle', 'Review role coverage, permission volume, and protected system roles at a glance.')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-primary-subtle text-primary px-3 py-2">{{ $roles->count() }} roles</span>
                <span class="badge bg-secondary-subtle text-secondary px-3 py-2">{{ $roles->sum(fn ($role) => $role->permissions->count()) }} permission assignments</span>
            </div>

            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create New Role
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Permissions</th>
                            <th>Guard</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $role->name }}</div>
                                    <div class="small text-muted">{{ $role->slug }}</div>
                                </td>
                                <td>{{ $role->description ?: 'No description provided.' }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        <span class="badge bg-primary-subtle text-primary">{{ $role->permissions->count() }} permissions</span>
                                        @if(in_array($role->slug, ['super-admin', 'admin']))
                                            <span class="badge bg-warning-subtle text-warning">Protected role</span>
                                        @endif
                                    </div>

                                    @foreach($role->permissions->take(6) as $permission)
                                        <span class="badge bg-primary me-1 mb-1">{{ $permission->name }}</span>
                                    @endforeach

                                    @if($role->permissions->count() > 6)
                                        <span class="badge bg-light text-dark mb-1">+{{ $role->permissions->count() - 6 }} more</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $role->guard_name }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.roles.edit', $role) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </a>
                                        @if(!in_array($role->slug, ['super-admin', 'admin']))
                                            <form action="{{ route('admin.roles.destroy', $role) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash me-1"></i>Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
