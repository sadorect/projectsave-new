<x-guest-layout>
    <div class="auth-form-intro">
        <span class="auth-kicker">New Password</span>
        <h2 class="auth-form-title">Choose a fresh password</h2>
        <p class="auth-form-copy">
            Set a new password for your account, then return to the ministry or LMS experience with confidence.
        </p>
    </div>

    @if ($errors->any())
        <div class="auth-alert auth-alert-danger mb-4">
            Please correct the highlighted details before resetting your password.
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="auth-form-grid">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="auth-field">
            <label for="email" class="surface-form-label">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $request->email) }}"
                class="surface-form-input @error('email') is-invalid @enderror"
                autocomplete="email"
                required
                autofocus
            >
            @error('email')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password" class="surface-form-label">New password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="surface-form-input @error('password') is-invalid @enderror"
                autocomplete="new-password"
                required
            >
            @error('password')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password_confirmation" class="surface-form-label">Confirm new password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="surface-form-input @error('password_confirmation') is-invalid @enderror"
                autocomplete="new-password"
                required
            >
            @error('password_confirmation')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <x-math-captcha />

        <button type="submit" class="surface-button-primary w-100 justify-content-center">Reset password</button>
    </form>

    <div class="auth-link-row">
        Need to start again? <a href="{{ route('password.request') }}">Request another reset link</a>
    </div>
</x-guest-layout>
