@extends('layouts.user')

@section('title', 'Account Settings')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">Account Settings</h1>
            <p class="text-muted">Manage your account preferences and security settings</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Password Change Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Change Password</h5>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')
                        
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <x-math-captcha error-bag="updatePassword" />

                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
            </div>

            <!-- Communication Preferences -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Communication Preferences</h5>
                    <form method="POST" action="{{ route('user.preferences.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label class="form-label">Preferred Language</label>
                            <select name="language" class="form-select">
                                <option value="en" {{ auth()->user()->language === 'en' ? 'selected' : '' }}>English</option>
                                <option value="fr" {{ auth()->user()->language === 'fr' ? 'selected' : '' }}>French</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Time Zone</label>
                            <select name="timezone" class="form-select">
                                <option value="UTC" {{ auth()->user()->timezone === 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ auth()->user()->timezone === 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                <option value="Europe/London" {{ auth()->user()->timezone === 'Europe/London' ? 'selected' : '' }}>London</option>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <input type="hidden" name="preferences[email]" value="0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="settingsEmailNotifications" name="preferences[email]" value="1" {{ !empty($accountPreferences['email']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="settingsEmailNotifications">Email notifications</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" name="preferences[event_reminders]" value="0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="settingsEventReminders" name="preferences[event_reminders]" value="1" {{ !empty($accountPreferences['event_reminders']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="settingsEventReminders">Event reminders</label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Preferences</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Account Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Account Actions</h5>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-warning">Download My Data</button>
                        <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete your account? This action cannot be undone.</p>
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    <div class="mb-3">
                        <label class="form-label">Please enter your password to confirm</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-danger">Delete Account</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
