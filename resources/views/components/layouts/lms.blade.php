@props([
    'title' => config('app.name', 'Projectsave International') . ' - ASOM Learning',
    'pageTitle' => null,
    'subtitle' => null,
    'eyebrow' => 'ASOM Learning',
    'showSidebar' => null,
    'showMinistryHeader' => true,
    'flushTop' => false,
])

@php
    $navigationItems = collect($lmsNavigation ?? [])->flatMap(fn (array $section) => $section['items'] ?? []);
    $workspaceSidebar = $showSidebar ?? auth()->check();
    $brandUrl = auth()->check() ? route('lms.dashboard') : route('lms.courses.index');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <title>{{ $title }}</title>

        @if (! app()->runningUnitTests())
            @vite('resources/css/lms.css')
        @endif

        @stack('styles')
        @livewireStyles
    </head>
    <body class="surface-lms">
        @if($showMinistryHeader)
            @include('components.layouts.header')
        @endif

        <div class="surface-shell lms-shell">
            <header class="lms-topbar">
                <div class="surface-frame">
                    <nav class="navbar navbar-expand-lg lms-navbar p-0" aria-label="LMS">
                        <a class="lms-brand" href="{{ $brandUrl }}">
                            <span class="lms-brand-mark">
                                <i class="bi bi-mortarboard-fill"></i>
                            </span>
                            <span class="lms-brand-copy">
                                <span class="d-block fw-semibold">Archippus School of Ministry</span>
                                <small class="text-white-50">Equipping students for ministry, service, and certification</small>
                            </span>
                        </a>

                        <button
                            class="navbar-toggler lms-navbar-toggle"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#lmsNavigation"
                            aria-controls="lmsNavigation"
                            aria-expanded="false"
                            aria-label="Toggle LMS navigation"
                        >
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse justify-content-between gap-4 mt-3 mt-lg-0" id="lmsNavigation">
                            <div class="navbar-nav ms-lg-4 gap-2">
                                @foreach($navigationItems as $item)
                                    @if(! empty($item['url']))
                                        <a class="lms-nav-link nav-link {{ $item['active'] ? 'active' : '' }}" href="{{ $item['url'] }}">
                                            @if(! empty($item['icon']))
                                                <i class="{{ $item['icon'] }}"></i>
                                            @endif
                                            <span>{{ $item['label'] }}</span>
                                            @if($item['badge'])
                                                <span class="badge rounded-pill bg-light text-dark">{{ $item['badge'] }}</span>
                                            @endif
                                        </a>
                                    @endif
                                @endforeach
                            </div>

                            <div class="lms-navbar-actions">
                                @auth
                                    <a href="{{ route('user.dashboard') }}" class="lms-topbar-link">
                                        <i class="bi bi-grid"></i>
                                        <span>Main account</span>
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-light rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ auth()->user()->name }}
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><h6 class="dropdown-header">{{ auth()->user()->email }}</h6></li>
                                            <li><a class="dropdown-item" href="{{ route('lms.dashboard') }}">Student workspace</a></li>
                                            <li><a class="dropdown-item" href="{{ route('lms.courses.index') }}">Course catalog</a></li>
                                            <li><a class="dropdown-item" href="{{ route('user.dashboard') }}">Main dashboard</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                @else
                                    <a href="{{ route('login') }}" class="surface-button-ghost text-white">Sign in</a>
                                    <a href="{{ route('asom.register') }}" class="btn btn-light rounded-pill px-4">Join ASOM</a>
                                @endauth
                            </div>
                        </div>
                    </nav>
                </div>
            </header>

            @include('components.alerts')

            <div @class([
                'surface-frame',
                'lms-shell-frame',
                'lms-shell-frame-flush' => $flushTop,
            ])>
                <div class="lms-workspace {{ $workspaceSidebar ? 'has-sidebar' : '' }}">
                    @if($workspaceSidebar)
                        <aside class="lms-sidebar">
                            <div class="lms-sidebar-card">
                                <div class="d-flex align-items-start justify-content-between gap-3">
                                    <div>
                                        <div class="lms-sidebar-label">Student</div>
                                        <h2 class="lms-sidebar-title mb-1">{{ auth()->user()->name }}</h2>
                                        <p class="mb-0 text-white-50 small">{{ auth()->user()->email }}</p>
                                    </div>
                                    <span class="lms-sidebar-badge">
                                        {{ auth()->user()->hasVerifiedEmail() ? 'Verified' : 'Verify email' }}
                                    </span>
                                </div>
                            </div>

                            <div class="lms-sidebar-panel">
                                <div class="lms-sidebar-section">Navigate</div>
                                <nav class="d-grid gap-2" aria-label="Student workspace">
                                    @foreach($navigationItems as $item)
                                        @if(! empty($item['url']))
                                            <a class="lms-sidebar-link {{ $item['active'] ? 'active' : '' }}" href="{{ $item['url'] }}">
                                                @if(! empty($item['icon']))
                                                    <i class="{{ $item['icon'] }}"></i>
                                                @endif
                                                <span>{{ $item['label'] }}</span>
                                            </a>
                                        @endif
                                    @endforeach
                                </nav>
                            </div>

                            <div class="lms-sidebar-panel">
                                <div class="lms-sidebar-section">Support</div>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('contact.show') }}" class="surface-button-secondary justify-content-center">Contact support</a>
                                    <a href="{{ route('home') }}" class="surface-button-ghost justify-content-center">Public site</a>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="surface-button-primary w-100 justify-content-center">Log out</button>
                                    </form>
                                </div>
                            </div>
                        </aside>
                    @endif

                    <main class="lms-main">
                        @if($pageTitle)
                            <section class="surface-section pt-4 pb-3">
                                <x-ui.page-header :eyebrow="$eyebrow" :title="$pageTitle" :subtitle="$subtitle" class="lms-page-header" />
                            </section>
                        @endif

                        <section class="surface-section pt-0">
                            {{ $slot }}
                        </section>
                    </main>
                </div>
            </div>
        </div>

        @include('components.layouts.footer')

        @if (! app()->runningUnitTests())
            @vite('resources/js/lms.js')
        @endif

        @stack('scripts')
        @livewireScripts
    </body>
</html>
