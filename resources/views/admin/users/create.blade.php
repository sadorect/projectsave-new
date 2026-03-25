@extends('admin.layouts.app')

@section('title', 'Create User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Create New User</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_admin" class="form-check-input" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                                <label class="form-check-label">Admin User</label>
                            </div>
                        </div>

                        @php($selectedRoles = collect(old('roles', []))->map(fn ($roleId) => (int) $roleId)->all())
                        <div class="mb-4">
                            <label class="form-label">Roles</label>
                            <div class="small text-muted mb-2">A user can hold more than one role at the same time.</div>
                            <div class="border rounded p-3">
                                @forelse($roles as $role)
                                    <div class="form-check mb-2">
                                        <input
                                            type="checkbox"
                                            name="roles[]"
                                            value="{{ $role->id }}"
                                            class="form-check-input"
                                            id="create-role-{{ $role->id }}"
                                            {{ in_array($role->id, $selectedRoles, true) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="create-role-{{ $role->id }}">
                                            {{ $role->name }}
                                            <span class="d-block small text-muted">{{ $role->slug }}</span>
                                        </label>
                                    </div>
                                @empty
                                    <div class="text-muted small">No roles are available yet.</div>
                                @endforelse
                            </div>
                            @error('roles')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                            @error('roles.*')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
