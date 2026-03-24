<x-guest-layout>
    <div class="auth-form-intro">
        <span class="auth-kicker">Password Help</span>
        <h2 class="auth-form-title">Reset your password</h2>
        <p class="auth-form-copy">
            Enter your email address and we will send you a secure link to reset your password.
        </p>
    </div>

    @if (session('status'))
        <div class="auth-alert auth-alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="auth-alert auth-alert-danger mb-4">
            Please check the email address and try again.
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="auth-form-grid">
        @csrf

        <div class="auth-field">
            <label for="email" class="surface-form-label">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                class="surface-form-input @error('email') is-invalid @enderror"
                autocomplete="email"
                required
                autofocus
            >
            @error('email')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <x-math-captcha />

        <button type="submit" class="surface-button-primary w-100 justify-content-center">Send reset link</button>
    </form>

    <div class="auth-link-row">
        Remembered your password? <a href="{{ route('login') }}">Return to login</a>
    </div>
</x-guest-layout>
