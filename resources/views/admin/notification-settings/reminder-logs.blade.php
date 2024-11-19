@extends('admin.layouts.app')

@section('title', 'Reminder Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Event Reminder Logs</h5>
                    <a href="{{ route('admin.notification-settings.event-reminders') }}" class="btn btn-primary btn-sm">
                        Back to Settings
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Recipients</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Sent At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->event->title }}</td>
                                        <td>{{ $log->recipients_count }}</td>
                                        <td>
                                            <span class="badge bg-{{ $log->status === 'success' ? 'success' : 'danger' }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $log->notes }}</td>
                                        <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No reminder logs found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
