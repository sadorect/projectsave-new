@extends('admin.layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Users</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add New User
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @php
        // Prefer the controller-provided $activeSessions; fallback to a quick DB query if missing
        if (!isset($activeSessions)) {
            try {
                $sessionTable = config('session.table', 'sessions');
                $activeSessions = \Illuminate\Support\Facades\DB::table($sessionTable)
                    ->whereNotNull('user_id')
                    ->pluck('user_id')
                    ->unique()
                    ->map(fn($v) => (int) $v)
                    ->toArray();
            } catch (\Throwable $e) {
                $activeSessions = [];
            }
        }
    @endphp

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Logged In</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            @if(in_array($user->id, $activeSessions))
                                <span class="badge bg-success">Online</span>
                            @else
                                <span class="badge bg-secondary">Offline</span>
                            @endif
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->user_type)
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $user->user_type)) }}</span>
                            @else
                                <span class="badge bg-secondary">Regular User</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_admin)
                                <span class="badge bg-primary">Admin</span>
                            @else
                                @foreach($user->roles as $role)
                                <span class="badge bg-secondary">{{ $role->name }}</span>
                            @endforeach
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info" title="View User">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit User">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')" title="Delete User">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
            </div>
        </div>
        <div class="mt-4 d-flex justify-content-center">
            <nav aria-label="Page navigation">
                {{ $users->links('pagination::bootstrap-4') }}
            </nav>
        </div>
    </div>
</div>

@endsection
