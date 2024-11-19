@extends('admin.layouts.app')

@section('title', 'Anniversary Wish Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Celebration Wish Logs</h5>
                    <div class="filters">
                        <select class="form-select" onchange="filterWishLogs(this.value)">
                            <option value="all">All Celebrations</option>
                            <option value="birthday">Birthdays</option>
                            <option value="wedding">Wedding Anniversaries</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Member</th>
                                    <th>Type</th>
                                    <th>Years</th>
                                    <th>Sent By</th>
                                    <th>Message</th>
                                    <th>Response</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wishLogs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('M d, Y') }}</td>
                                        <td>{{ $log->user->name }}</td>
                                        <td>
                                            @if($log->type === 'birthday')
                                                🎂 Birthday
                                            @else
                                                💑 Wedding Anniversary
                                            @endif
                                        </td>
                                        <td>{{ $log->years }}</td>
                                        <td>{{ $log->sender->name }}</td>
                                        <td>{{ $log->message }}</td>
                                        <td>{{ $log->response ?? 'No response yet' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $wishLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
