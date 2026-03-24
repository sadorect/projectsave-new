@php
    $navigationItems = collect($publicNavigation ?? [])->flatMap(fn (array $section) => $section['items'] ?? []);
    $fallbackNavigationItems = collect([
        ['label' => 'Home', 'route' => 'home', 'patterns' => ['home']],
        ['label' => 'About', 'route' => 'about', 'patterns' => ['about']],
        ['label' => 'Events', 'route' => 'events.index', 'patterns' => ['events.*']],
        ['label' => 'Devotionals', 'route' => 'blog.index', 'patterns' => ['blog.*', 'posts.show']],
        ['label' => 'FAQs', 'route' => 'faqs.list', 'patterns' => ['faqs.*']],
        ['label' => 'Contact', 'route' => 'contact.show', 'patterns' => ['contact.*']],
        ['label' => 'ASOM', 'route' => 'lms.courses.index', 'patterns' => ['lms.*', 'asom', 'asom.welcome']],
    ])
        ->filter(fn (array $item) => \Illuminate\Support\Facades\Route::has($item['route']))
        ->map(fn (array $item) => [
            'label' => $item['label'],
            'url' => route($item['route']),
            'active' => collect($item['patterns'])->contains(fn (string $pattern) => request()->routeIs($pattern)),
        ]);

    if ($navigationItems->isEmpty()) {
        $navigationItems = $fallbackNavigationItems;
    }

    $dashboardRoute = auth()->check() ? route(auth()->user()->dashboardRoute()) : null;
@endphp

<header class="site-header">
    <div class="site-topbar">
        <div class="surface-frame">
            <div class="site-topbar-content">
                <div class="site-topbar-copy d-none d-lg-flex">
                    <span class="site-topbar-copy-pill">Global ministry</span>
                    <span>Evangelism, discipleship, and ministry training with a Christ-centered witness.</span>
                </div>

                <div class="site-topbar-links">
                    <a href="tel:+2347080100893" class="site-topbar-link">
                        <i class="fas fa-phone-alt text-brand-400"></i>
                        <span>(+234) 07080100893</span>
                    </a>
                    <a href="mailto:info@projectsaveng.org" class="site-topbar-link">
                        <i class="fas fa-envelope text-brand-400"></i>
                        <span>info@projectsaveng.org</span>
                    </a>

                    <div class="site-social-links" aria-label="Social media links">
                        <a href="https://facebook.com/projectsave02" class="site-social-link" aria-label="Projectsave on Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://instagram.com/projectsave_ministries" class="site-social-link" aria-label="Projectsave on Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="site-navbar-shell sticky-top">
        <div class="surface-frame py-2 py-lg-3">
            <nav class="navbar navbar-expand-lg p-0" aria-label="Primary">
                <a href="{{ route('home') }}" class="site-brand-mark">
                    <span class="site-brand-badge">
                        <img src="{{ asset('frontend/img/psave_logo.png') }}" alt="Projectsave International" class="h-8 w-8 rounded-xl object-cover">
                    </span>
                    <span class="site-brand-copy">
                        <strong>Projectsave International</strong>
                        <span>Winning the lost. Building the saints.</span>
                    </span>
                </a>

                <div class="d-flex align-items-center gap-2 ms-auto d-lg-none">
                    @guest
                        <a href="{{ route('login') }}" class="site-account-link site-mobile-cta">Login</a>
                    @else
                        <a href="{{ $dashboardRoute }}" class="site-account-link site-mobile-cta">Dashboard</a>
                    @endguest

                    <button
                        class="site-navbar-toggle"
                        type="button"
                        data-site-nav-toggle
                        aria-controls="publicNavigation"
                        aria-expanded="false"
                        aria-label="Toggle navigation"
                    >
                        <span class="site-navbar-toggle-box" aria-hidden="true">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                    </button>
                </div>

                <div class="site-navigation-panel mt-3 mt-lg-0" id="publicNavigation">
                    <div class="site-mobile-menu">
                        <form action="{{ route('search') }}" method="GET" class="site-mobile-search d-lg-none">
                            <label class="visually-hidden" for="site-search-mobile">Search the site</label>
                            <input
                                id="site-search-mobile"
                                class="site-search-input site-search-input-light"
                                type="search"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Search devotionals, FAQs, and events"
                            >
                            <button class="site-search-button" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>

                        <div class="navbar-nav site-nav-links ms-lg-5 flex-wrap gap-1">
                            @foreach($navigationItems as $item)
                                @if(! empty($item['url']))
                                    <a href="{{ $item['url'] }}" class="site-nav-link nav-link {{ $item['active'] ? 'active' : '' }}">
                                        {{ $item['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2 site-nav-actions">
                            @guest
                                <a href="{{ route('login') }}" class="site-account-link">Login</a>
                                <a href="{{ route('register') }}" class="surface-button-secondary">Create account</a>
                            @else
                                <a href="{{ $dashboardRoute }}" class="site-account-link">Dashboard</a>
                                <a href="{{ route('asom.welcome') }}" class="site-account-link">My learning</a>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="surface-button-ghost">Logout</button>
                                </form>
                            @endguest
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</header>
