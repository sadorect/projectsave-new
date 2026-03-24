<x-guest-layout>
    <div class="auth-form-intro">
        <span class="auth-kicker">ASOM Enrollment</span>
        <h2 class="auth-form-title">Join Archippus School of Ministry</h2>
        <p class="auth-form-copy">
            Register as a student to access the LMS landing page, published courses, lesson progress, exams, and certificate flow.
        </p>
    </div>

    @if ($errors->any())
        <div class="auth-alert auth-alert-danger mb-4">
            Please correct the highlighted details before submitting your student registration.
        </div>
    @endif

    <form method="POST" action="{{ route('asom.register') }}" class="auth-form-grid">
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

        <button type="submit" class="surface-button-primary w-100 justify-content-center">Create ASOM student account</button>
    </form>

    <div class="auth-help-text mt-4">
        We will ask you to verify your email before taking you into the student workspace.
    </div>

    <div class="auth-link-row">
        Already enrolled? <a href="{{ route('login') }}">Sign in here</a>
    </div>

    <div class="auth-action-stack">
        <a href="{{ route('register') }}" class="surface-button-secondary">Create a standard ministry account</a>
    </div>
</x-guest-layout>
