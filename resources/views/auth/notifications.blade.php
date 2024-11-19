<x-layouts.app>
    <!-- Page Header Start -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>Notification Settings</h2>
                </div>
                <div class="col-12">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('profile') }}">Profile</a>
                    <a href="">Notifications</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Email Notifications</h4>
                        
                        <form method="POST" action="{{ route('notification-preferences.update') }}">
                            @csrf
                            @method('patch')

                            <div class="notification-group mb-4">
                                <h5>Updates & News</h5>
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="newsletter" name="preferences[newsletter]">
                                    <label class="custom-control-label" for="newsletter">Newsletter & Ministry Updates</label>
                                </div>
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="blog_posts" name="preferences[blog_posts]">
                                    <label class="custom-control-label" for="blog_posts">New Blog Posts</label>
                                </div>
                            </div>

                            <div class="notification-group mb-4">
                                <h5>Events & Activities</h5>
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="events" name="preferences[events]">
                                    <label class="custom-control-label" for="events">Upcoming Events</label>
                                </div>
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="prayer_requests" name="preferences[prayer_requests]">
                                    <label class="custom-control-label" for="prayer_requests">Prayer Requests</label>
                                </div>
                            </div>

                            <div class="notification-group mb-4">
                                <h5>Account Updates</h5>
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="security_alerts" name="preferences[security_alerts]" checked disabled>
                                    <label class="custom-control-label" for="security_alerts">Security Alerts (Required)</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-custom">Save Preferences</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
