<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <title>{{ config('app.name', 'Projectsave International') }}</title>

        @if (! app()->runningUnitTests())
            @vite('resources/css/public.css')
        @endif
    </head>
    <body class="surface-public">
        <a href="#main-content" class="skip-to-content">Skip to main content</a>
        <div class="surface-shell">
            @include('components.layouts.header')

            <div class="pt-4">
                @include('components.alerts')
            </div>

            <main class="site-main" id="main-content">
                <section class="surface-section auth-entry-section">
                    <div class="surface-frame">
                        <div class="auth-stage">
                            <aside class="auth-stage-aside">
                                <span class="auth-kicker">Secure account access</span>
                                <h1 class="auth-stage-title">Stay connected to the ministry, your learning journey, and the next step God is opening.</h1>
                                <p class="auth-stage-copy">
                                    Use your account to follow devotionals, respond to ministry opportunities, join Archippus School of Ministry,
                                    and keep your public and student experience connected.
                                </p>

                                <div class="auth-stage-highlights">
                                    <article class="auth-stage-highlight">
                                        <strong>Public ministry</strong>
                                        <span>Move between the website, devotionals, events, FAQs, and partnership calls without losing context.</span>
                                    </article>
                                    <article class="auth-stage-highlight">
                                        <strong>ASOM learning</strong>
                                        <span>Track courses, lesson progress, exams, and certificates in one clearer student workspace.</span>
                                    </article>
                                    <article class="auth-stage-highlight">
                                        <strong>Responsive access</strong>
                                        <span>Use the same experience across desktop and mobile with cleaner entry points and form flows.</span>
                                    </article>
                                </div>

                                <div class="auth-stage-links">
                                    <a href="{{ route('home') }}" class="surface-button-secondary">Return to homepage</a>
                                    <a href="{{ route('lms.courses.index') }}" class="surface-button-ghost">Explore ASOM</a>
                                </div>
                            </aside>

                            <div class="auth-stage-panel">
                                <div class="auth-card">
                                    {{ $slot }}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            @include('components.layouts.footer')
        </div>

        <div id="cookie-consent" class="cookie-consent" role="alertdialog" aria-live="polite" aria-label="Cookie consent" aria-describedby="cookie-consent-message" hidden>
            <div class="surface-frame">
                <div class="d-flex flex-column gap-3 flex-lg-row align-items-lg-center justify-content-lg-between">
                    <p id="cookie-consent-message" class="mb-0 text-sm">
                        We use cookies to improve site performance and your experience across the ministry and LMS surfaces.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('privacy') }}" class="surface-button-secondary">Privacy policy</a>
                        <button id="accept-cookies" type="button" class="surface-button-primary">Accept cookies</button>
                    </div>
                </div>
            </div>
        </div>

        @if (! app()->runningUnitTests())
            @vite('resources/js/public.js')
        @endif
    </body>
</html>
