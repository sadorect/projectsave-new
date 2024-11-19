<x-layouts.app>
    <!-- Page Header Start -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>Reset Password</h2>
                </div>
                <div class="col-12">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="">Reset Password</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="password-reset-form">
                    <div class="mb-4 text-muted">
                        Enter your email address and we'll send you a link to reset your password.
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <button type="submit" class="btn btn-custom">
                            Send Reset Link
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
