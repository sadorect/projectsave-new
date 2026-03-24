<x-guest-layout>
    <div class="auth-form-intro">
        <span class="auth-kicker">Login</span>
        <h2 class="auth-form-title">Welcome back</h2>
        <p class="auth-form-copy">
            Sign in to continue into your ministry account, student workspace, and personalized public experience.
        </p>
    </div>

    @if (session('status'))
        <div class="auth-alert auth-alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="auth-alert auth-alert-danger mb-4">
            Please review the highlighted fields and try again.
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form-grid">
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

        <div class="auth-inline">
            <div class="form-check mb-0">
                <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                <label class="form-check-label text-muted" for="remember_me">Keep me signed in</label>
            </div>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot your password?</a>
            @endif
        </div>

        <x-math-captcha />

        <button type="submit" class="surface-button-primary w-100 justify-content-center">Sign in</button>
    </form>

    <div class="auth-link-row">
        Need an account? <a href="{{ route('register') }}">Create a ministry account</a>
    </div>

    <div class="auth-action-stack">
        <a href="{{ route('asom.register') }}" class="surface-button-secondary">Join ASOM directly</a>
    </div>
</x-guest-layout>
