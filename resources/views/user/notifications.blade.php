@extends('layouts.user')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3">Notifications</h1>
                <p class="text-muted">Stay updated with latest activities and announcements</p>
            </div>
            <div>
                <button class="btn btn-outline-primary">Mark All as Read</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            @if($notifications->count() > 0)
                @foreach($notifications as $notification)
                    <div class="card border-0 shadow-sm mb-3 {{ $notification->read_at ? '' : 'border-start border-primary border-4' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0">{{ $notification->data['message'] }}</p>
                        </div>
                    </div>
                @endforeach

                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-bell h1 text-muted"></i>
                        <h5 class="mt-3">No Notifications</h5>
                        <p class="text-muted">You're all caught up!</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Notification Settings</h5>
                    <form action="{{ route('notification-preferences.update') }}" method="POST">
                        @csrf
                        @method('patch')
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="emailNotifications" name="preferences[email]">
                                <label class="form-check-label" for="emailNotifications">Email Notifications</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="prayerUpdates" name="preferences[prayer_updates]">
                                <label class="form-check-label" for="prayerUpdates">Prayer Updates</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="eventReminders" name="preferences[event_reminders]">
                                <label class="form-check-label" for="eventReminders">Event Reminders</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Preferences</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
