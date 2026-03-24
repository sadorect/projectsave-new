<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <title>@yield('title', 'Content Workspace') - Projectsave International</title>

        @if (! app()->runningUnitTests())
            @vite('resources/css/admin.css')
        @endif

        @stack('styles')
    </head>
    <body class="surface-admin surface-workspace">
        <a href="#main-content" class="skip-to-content">Skip to main content</a>
        <div class="surface-admin-shell d-lg-flex">
            @include('layouts.content.sidebar')

            <div class="surface-content-shell flex-grow-1">
                <header class="surface-topbar">
                    <div class="d-flex flex-column gap-2 flex-md-row align-items-md-center justify-content-md-between">
                        <div>
                            <div class="surface-kicker mb-2">Content Workspace</div>
                            <h1 class="h3 mb-0">@yield('title', 'Content Workspace')</h1>
                        </div>

                        <a href="{{ route('home') }}" class="surface-button-secondary">View public site</a>
                    </div>
                </header>

                @include('components.alerts')

                <main class="flex-grow-1">
                    @yield('content')
                </main>
            </div>
        </div>

        @if (! app()->runningUnitTests())
            @vite('resources/js/admin.js')
        @endif

        @stack('scripts')
    </body>
</html>
