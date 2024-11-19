<x-layouts.app>
    <!-- Page Header Start -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>Security Settings</h2>
                </div>
                <div class="col-12">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('profile') }}">Profile</a>
                    <a href="">Security</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Change Password</h4>
                        
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('put')

                            <div class="form-group">
                                <label>Current Password</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>

                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>

                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>

                            <button type="submit" class="btn btn-custom">Update Password</button>
                        </form>

                        <hr class="my-4">

                        <h4 class="card-title mb-4">Two-Factor Authentication</h4>
                        <div class="two-factor-section">
                            <p>Add additional security to your account using two-factor authentication.</p>
                            <button class="btn btn-custom">Enable 2FA</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
