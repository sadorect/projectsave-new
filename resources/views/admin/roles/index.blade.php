@extends('admin.layouts.app')

@section('title', 'Roles Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4 d-flex justify-content-between align-items-center">
            <h1>Roles Management</h1>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create New Role
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->description }}</td>
                                <td>
                                    @foreach($role->permissions as $permission)
                                        <span class="badge bg-primary me-1">{{ $permission->name }}</span>
                                    @endforeach
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