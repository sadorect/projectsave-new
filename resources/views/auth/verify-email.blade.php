<x-guest-layout>
    <div class="auth-form-intro">
        <span class="auth-kicker">Verify Email</span>
        <h2 class="auth-form-title">Confirm your email address</h2>
        <p class="auth-form-copy">
            Before continuing, click the verification link we sent to your inbox. This keeps your account and student access secure.
        </p>
    </div>

    @if (session('status') === 'verification-link-sent')
        <div class="auth-alert auth-alert-success mb-4">
            A fresh verification link has been sent to your email address.
        </div>
    @endif

    <div class="auth-form-grid">
        <form method="POST" action="{{ route('verification.send') }}" class="auth-form-grid">
            @csrf
            <x-math-captcha />
            <button type="submit" class="surface-button-primary w-100 justify-content-center">
                Resend verification email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="surface-button-secondary w-100 justify-content-center">
                Log out
            </button>
        </form>
    </div>

    <div class="auth-help-text mt-4">
        If you registered for ASOM, email verification is required before entering the student workspace.
    </div>
</x-guest-layout>
