<x-guest-layout>
    <div class="auth-form-intro">
        <span class="auth-kicker">Create Account</span>
        <h2 class="auth-form-title">Open your ministry account</h2>
        <p class="auth-form-copy">
            Create a standard Projectsave account to stay connected with devotionals, events, ministry opportunities, and future learning paths.
        </p>
    </div>

    @if ($errors->any())
        <div class="auth-alert auth-alert-danger mb-4">
            Please correct the highlighted details before continuing.
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="auth-form-grid">
        @csrf

        <div class="auth-field">
            <label for="name" class="surface-form-label">Full name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                class="surface-form-input @error('name') is-invalid @enderror"
                autocomplete="name"
                required
                autofocus
            >
            @error('name')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

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
            >
            @error('email')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password" class="surface-form-label">Password</label>
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
            <label for="password_confirmation" class="surface-form-label">Confirm password</label>
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

        <button type="submit" class="surface-button-primary w-100 justify-content-center">Create account</button>
    </form>

    <div class="auth-link-row">
        Already registered? <a href="{{ route('login') }}">Sign in here</a>
    </div>

    <div class="auth-action-stack">
        <a href="{{ route('asom.register') }}" class="surface-button-secondary">Register for ASOM instead</a>
    </div>
</x-guest-layout>
