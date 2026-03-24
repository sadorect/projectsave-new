<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <title>@yield('title', 'Admin Panel') - Projectsave International</title>

        @if (! app()->runningUnitTests())
            @vite('resources/css/admin.css')
        @endif

        <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>

        @stack('styles')
    </head>
    <body class="surface-admin">
        <div class="surface-admin-shell d-lg-flex">
            @include('admin.layouts.sidebar')

            <div class="surface-admin-main flex-grow-1">
                <header class="surface-topbar surface-admin-topbar">
                    <div class="surface-admin-header">
                        <div class="surface-admin-header-row">
                            <div class="surface-admin-heading">
                                <button
                                    type="button"
                                    class="surface-admin-sidebar-toggle d-lg-none"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#adminSidebar"
                                    aria-controls="adminSidebar"
                                    aria-label="Open admin navigation"
                                >
                                    <i class="bi bi-list"></i>
                                </button>

                                <div>
                                    <div class="surface-kicker mb-2">@yield('page_kicker', 'Back Office')</div>
                                    <h1 class="h3 mb-1">@yield('title', 'Admin Panel')</h1>
                                    <p class="surface-admin-subtitle mb-0">@yield('page_subtitle', 'Operate ministry workflows through one role-aware admin workspace.')</p>
                                </div>
                            </div>

                            <span class="surface-admin-operator d-none d-md-inline-flex">{{ auth()->user()->name }}</span>
                        </div>

                        <div class="surface-admin-header-actions">
                            <a href="{{ route('user.dashboard') }}" class="surface-button-ghost">Account dashboard</a>
                            <a href="{{ route('home') }}" class="surface-button-secondary">View public site</a>
                            <span class="surface-admin-operator d-md-none">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                </header>

                @include('components.alerts')

                <main class="surface-admin-content">
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
