@extends('admin.layouts.app')

@section('title', 'Active Sessions')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Active Sessions (driver: {{ $driver }})</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    <div class="mb-3">
        <form method="GET" class="form-inline">
            <div class="form-check mr-3">
                <input class="form-check-input" type="checkbox" name="show_guests" id="show_guests" value="1" {{ request()->boolean('show_guests') ? 'checked' : '' }}>
                <label class="form-check-label" for="show_guests">Show guest sessions</label>
            </div>

            <div class="mr-3">
                <label for="direction" class="mr-2">Sort</label>
                <select name="direction" id="direction" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="desc" {{ request('direction', 'desc') === 'desc' ? 'selected' : '' }}>Newest first</option>
                    <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Oldest first</option>
                </select>
            </div>

            <button class="btn btn-sm btn-primary" type="submit">Apply</button>
        </form>
    </div>

    @if($sessions->isEmpty())
        <div class="alert alert-info">No active sessions found or session driver not supported.</div>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Session ID</th>
                    <th>Driver</th>
                    <th>Active For</th>
                    <th>User</th>
                    <th>Last Activity</th>
                    <th>Size (bytes)</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $s)
                    <tr>
                        <td><code>{{ $s['id'] }}</code></td>
                        <td>{{ $s['driver'] ?? $driver }}</td>
                        <td>
                            @php
                                $activeFor = '';
                                if(!empty($s['last_activity'])) {
                                    try {
                                        $last = \Carbon\Carbon::parse($s['last_activity']);
                                        $activeFor = $last->diffForHumans(now(), \Carbon\Carbon::DIFF_ABSOLUTE);
                                    } catch (\Throwable $e) {
                                        $activeFor = '-';
                                    }
                                } else {
                                    $activeFor = '-';
                                }
                            @endphp
                            {{ $activeFor }}
                        </td>
                        <td>
                            @if($s['user'])
                                <a href="{{ route('admin.users.show', $s['user']) }}">{{ $s['user']->name }} (ID: {{ $s['user']->id }})</a>
                            @elseif($s['user_id'])
                                Unknown user (ID: {{ $s['user_id'] }})
                            @else
                                Guest/No user
                            @endif
                        </td>
                        <td>{{ $s['last_activity'] }}</td>
                        <td>{{ $s['size'] }}</td>
                        <td>
                            <form action="{{ route('admin.sessions.destroy', $s['id']) }}" method="POST" onsubmit="return confirm('Terminate this session?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Terminate</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $sessions->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
