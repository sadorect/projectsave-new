@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Dashboard</h1>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text display-6">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Active Users</h5>
                    <p class="card-text display-6">{{ $stats['active_users'] }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">New Users Today</h5>
                    <p class="card-text display-6">{{ $stats['new_users_today'] }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Pending Partners</h5>
                    <p class="card-text display-6">{{ $stats['pending_partners'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add after existing stats cards -->
<div class="col-md-12 mt-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Today's Celebrations</h5>
            <div class="btn-group">
                <button class="btn btn-outline-primary btn-sm" onclick="filterCelebrations('birthday')">Birthdays</button>
                <button class="btn btn-outline-primary btn-sm" onclick="filterCelebrations('wedding')">Anniversaries</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Celebration</th>
                            <th>Years</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayCelebrants as $celebrant)
                            <tr data-celebration="{{ $celebrant->celebration_type }}">
                                <td>{{ $celebrant->name }}</td>
                                <td>
                                    @if($celebrant->celebration_type === 'birthday')
                                        🎂 Birthday
                                    @else
                                        💑 Wedding Anniversary
                                    @endif
                                </td>
                                <td>{{ $celebrant->years }} years</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="sendWishes({{ $celebrant->id }})">
                                        Send Wishes
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No celebrations today</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    <div class="row">
        <!-- Recent Activity Section -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Recent Activity
                </div>
                <div class="card-body">
                    @if($recent_activity->count() > 0)
                        <div class="list-group">
                            @foreach($recent_activity as $activity)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $activity->title }}</h6>
                                        <small>{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">Created by {{ $activity->author}}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p>No recent activity</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Quick Actions
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">Add New User</a>
                        <a href="#" class="list-group-item list-group-item-action">View Reports</a>
                        <a href="#" class="list-group-item list-group-item-action">System Settings</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add this after the Quick Actions card -->
<div class="col-md-12 mt-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Upcoming Events & Reminders</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date</th>
                            <th>Reminder Status</th>
                            <th>Recipients</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upcomingEvents as $event)
                            <tr>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->date ? \Carbon\Carbon::parse($event->date)->format('M d, Y') : 'Date not set' }}</td>

                                <td>
                                    @if($event->reminderLogs->count() > 0)
                                        <span class="badge bg-success">Sent</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $event->reminderLogs->sum('recipients_count') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info me-2" onclick="previewReminder({{ $event->id }})">
                                        <i class="bi bi-eye"></i> Preview
                                    </button>
                                    <button class="btn btn-sm btn-primary" onclick="sendReminder({{ $event->id }})">
                                        Send Now
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No upcoming events</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    </div>
</div>
@endsection

<script>
function sendReminder(eventId) {
    fetch(`/admin/notification-settings/event-reminders/send/${eventId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>

<!-- Add this at the bottom of the file -->
<div class="modal fade" id="reminderPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reminder Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="preview-tabs">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#emailPreview">Email</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#databasePreview">In-App</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="emailPreview">
                            <div class="email-preview-content"></div>
                        </div>
                        <div class="tab-pane fade" id="databasePreview">
                            <div class="notification-preview-content"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewReminder(eventId) {
    fetch(`/admin/notification-settings/event-reminders/preview/${eventId}`)
        .then(response => response.json())
        .then(data => {
            document.querySelector('.email-preview-content').innerHTML = data.emailPreview;
            document.querySelector('.notification-preview-content').innerHTML = data.notificationPreview;
            new bootstrap.Modal(document.getElementById('reminderPreviewModal')).show();
        });
}
</script>

<script>
    // Add this to your existing dashboard scripts
    function filterCelebrations(type) {
        const rows = document.querySelectorAll('[data-celebration]');
        rows.forEach(row => {
            if (type === 'all' || row.dataset.celebration === type) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Initialize with all celebrations visible
    document.addEventListener('DOMContentLoaded', () => {
        filterCelebrations('all');
    });
    </script>
    
