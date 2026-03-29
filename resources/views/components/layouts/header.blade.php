@php
    $siteName = $siteSettings['site_name'] ?? 'Projectsave International';
    $siteShortName = $siteSettings['site_short_name'] ?? 'Projectsave';
    $siteTagline = $siteSettings['site_tagline'] ?? 'Winning the lost. Building the saints.';
    $logoUrl = $siteSettings['logo_url'] ?? null;
    $contactPhone = $siteSettings['contact_phone'] ?? null;
    $contactPhoneHref = $siteSettings['contact_phone_href'] ?? null;
    $contactEmail = $siteSettings['contact_email'] ?? null;
    $contactEmailHref = $siteSettings['contact_email_href'] ?? null;
    $socialLinks = collect($siteSettings['social_links'] ?? []);
    $brandInitial = strtoupper(substr($siteShortName ?: $siteName, 0, 1));
    $navigationItems = collect($publicNavigation ?? [])->flatMap(fn (array $section) => $section['items'] ?? []);
    $fallbackNavigationItems = collect([
        ['label' => 'Home', 'route' => 'home', 'patterns' => ['home']],
        ['label' => 'About', 'route' => 'about', 'patterns' => ['about']],
        ['label' => 'Events', 'route' => 'events.index', 'patterns' => ['events.*']],
        ['label' => 'Reports', 'route' => 'reports.index', 'patterns' => ['reports.*']],
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
                    @if(filled($contactPhone) && filled($contactPhoneHref))
                        <a href="{{ $contactPhoneHref }}" class="site-topbar-link">
                            <i class="fas fa-phone-alt text-brand-400"></i>
                            <span>{{ $contactPhone }}</span>
                        </a>
                    @endif
                    @if(filled($contactEmail) && filled($contactEmailHref))
                        <a href="{{ $contactEmailHref }}" class="site-topbar-link">
                            <i class="fas fa-envelope text-brand-400"></i>
                            <span>{{ $contactEmail }}</span>
                        </a>
                    @endif

                    @if($socialLinks->isNotEmpty())
                        <div class="site-social-links" aria-label="Social media links">
                            @foreach($socialLinks as $socialLink)
                                <a href="{{ $socialLink['url'] }}" class="site-social-link" aria-label="{{ $siteShortName }} on {{ $socialLink['label'] }}" rel="noopener noreferrer" target="_blank">
                                    <i class="{{ $socialLink['icon'] }}"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="site-navbar-shell sticky-top">
        <div class="surface-frame py-2 py-lg-3">
            <nav class="navbar navbar-expand-lg p-0" aria-label="Primary">
                <a href="{{ route('home') }}" class="site-brand-mark">
                    <span class="site-brand-badge">
                        @if(filled($logoUrl))
                            <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-8 w-8 rounded-xl object-cover">
                        @else
                            <span class="d-inline-flex align-items-center justify-content-center h-100 w-100 fw-bold text-brand-700">{{ $brandInitial }}</span>
                        @endif
                    </span>
                    <span class="site-brand-copy">
                        <strong>{{ $siteName }}</strong>
                        <span>{{ $siteTagline }}</span>
                    </span>
                </a>

                <div class="d-flex align-items-center gap-2 ms-auto d-lg-none">
                    @auth
                        <a href="{{ $dashboardRoute }}" class="site-account-link site-mobile-cta">Dashboard</a>
                    @endauth

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
                                placeholder="Search devotionals, reports, FAQs, and events"
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
                            <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="site-nav-give-btn">
                                <i class="fas fa-hand-holding-heart"></i>
                                <span>Partner</span>
                            </a>

                            @auth
                                <a href="{{ $dashboardRoute }}" class="site-account-link">
                                    <i class="fas fa-th-large" style="font-size:0.75rem;opacity:0.7;"></i>
                                    Dashboard
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="surface-button-ghost">Logout</button>
                                </form>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</header>
