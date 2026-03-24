<x-guest-layout>
    <div class="auth-form-intro">
        <span class="auth-kicker">Security Check</span>
        <h2 class="auth-form-title">Confirm your password</h2>
        <p class="auth-form-copy">
            This protected action needs one more password confirmation before we continue.
        </p>
    </div>

    @if ($errors->any())
        <div class="auth-alert auth-alert-danger mb-4">
            Please confirm your password to proceed.
        </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}" class="auth-form-grid">
        @csrf

        <div class="auth-field">
            <label for="password" class="surface-form-label">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="surface-form-input @error('password') is-invalid @enderror"
                autocomplete="current-password"
                required
            >
            @error('password')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <x-math-captcha />

        <button type="submit" class="surface-button-primary w-100 justify-content-center">Confirm password</button>
    </form>
</x-guest-layout>
