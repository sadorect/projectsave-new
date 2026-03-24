@props([
    'title' => config('app.name', 'Projectsave International'),
    'metaDescription' => 'Projectsave International Ministry - winning the lost and building the saints through evangelism, discipleship, and ministry training.',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $metaDescription }}">
        <meta name="format-detection" content="telephone=no">
        <meta name="robots" content="index, follow">
        <meta property="og:title" content="{{ $title }}">
        <meta property="og:description" content="{{ $metaDescription }}">
        <meta property="og:image" content="{{ asset('frontend/img/psave_logo.png') }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:type" content="website">
        <link rel="canonical" href="{{ url()->current() }}">
        <link rel="icon" href="{{ asset('frontend/img/psave_logo.png') }}">
        <link rel="alternate" type="application/rss+xml" title="Projectsave International Feed" href="{{ route('feed') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <title>{{ $title }}</title>

        @stack('styles')

        @if (! app()->runningUnitTests())
            @vite('resources/css/public.css')
        @endif
    </head>
    <body class="surface-public">
        @if (config('services.facebook.client_id'))
            <div id="fb-root"></div>
            <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0&appId={{ config('services.facebook.client_id') }}"></script>
        @endif

        <div class="surface-shell">
            @include('components.layouts.header')

            <div class="pt-4">
                @include('components.alerts')
            </div>

            @isset($header)
                <section class="surface-section pt-2">
                    <div class="surface-frame">
                        {{ $header }}
                    </div>
                </section>
            @endisset

            <main class="site-main">
                {{ $slot }}
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

        @stack('scripts')

        <script type="application/ld+json">
            {
                "@@context": "https://schema.org",
                "@@type": "Organization",
                "name": "Projectsave International",
                "url": "{{ config('app.url') }}",
                "logo": "{{ asset('frontend/img/psave_logo.png') }}",
                "contactPoint": {
                    "@@type": "ContactPoint",
                    "telephone": "+234-07080100893",
                    "email": "info@projectsaveng.org",
                    "contactType": "customer service"
                }
            }
        </script>
    </body>
</html>
