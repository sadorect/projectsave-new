@extends('admin.layouts.app')

@section('title', 'Event Reminder Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Event Reminder Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.notification-settings.event-reminders.update') }}" method="POST">
                        @csrf
                        @method('patch')

                        <div class="form-group mb-4">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="reminderEnabled" 
                                       name="reminder_enabled" 
                                       {{ $settings['reminder_enabled'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="reminderEnabled">Enable Automatic Reminders</label>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label>Days Before Event</label>
                            <input type="number" 
                                   class="form-control" 
                                   name="reminder_days" 
                                   value="{{ $settings['reminder_days'] }}" 
                                   min="1" 
                                   max="7">
                            <small class="text-muted">How many days before the event should reminders be sent?</small>
                        </div>

                        <div class="form-group mb-4">
                            <label>Send Time</label>
                            <input type="time" 
                                   class="form-control" 
                                   name="reminder_time" 
                                   value="{{ $settings['reminder_time'] }}">
                            <small class="text-muted">What time should reminders be sent? (24-hour format)</small>
                        </div>

                        <div class="card mb-4">
                            <div class="card-body bg-light">
                                <h6>Last Reminder Run</h6>
                                <p>{{ $settings['last_run'] ? \Carbon\Carbon::parse($settings['last_run'])->diffForHumans() : 'Never' }}</p>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

