<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <title>@yield('title', 'Member Dashboard') - Projectsave International</title>

        @if (! app()->runningUnitTests())
            @vite('resources/css/admin.css')
        @endif

        @stack('styles')
    </head>
    <body class="surface-admin surface-user">
        <a href="#main-content" class="skip-to-content">Skip to main content</a>
        <div class="surface-admin-shell d-lg-flex">
            @include('layouts.user.sidebar')

            <div class="surface-content-shell flex-grow-1">
                <header class="surface-topbar surface-admin-topbar">
                    <div class="surface-admin-header">
                        <div class="surface-admin-heading">
                            <button
                                type="button"
                                class="surface-admin-sidebar-toggle d-lg-none"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#userSidebar"
                                aria-controls="userSidebar"
                                aria-label="Open account navigation"
                            >
                                <i class="bi bi-list"></i>
                            </button>

                            <div>
                                <div class="surface-kicker mb-2">Member Area</div>
                                <h1 class="h3 mb-1">@yield('title', 'Member Dashboard')</h1>
                                <p class="surface-admin-subtitle mb-0">Manage your account, ministry activity, and learning access from one member workspace.</p>
                            </div>
                        </div>

                        <div class="surface-admin-header-actions">
                            @if(auth()->user()->hasBackofficeAccess())
                                <a href="{{ route(auth()->user()->preferredBackofficeRoute() ?? 'admin.dashboard') }}" class="surface-button-ghost">Open back office</a>
                            @endif
                            <a href="{{ route('home') }}" class="surface-button-secondary">Return to public site</a>
                        </div>
                    </div>
                </header>

                @include('components.alerts')

                <main class="flex-grow-1" id="main-content">
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
