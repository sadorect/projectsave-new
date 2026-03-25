@php
    $user = auth()->user();
    $initial = strtoupper(substr($user->name, 0, 1));
    $learningRoute = $user->isAsomStudent() ? route('asom.welcome') : route('asom');

    $accountSections = [
        [
            'label' => 'Account',
            'items' => [
                ['label' => 'Account Home', 'route' => route('user.dashboard'), 'icon' => 'bi bi-house-door', 'active' => request()->routeIs('user.dashboard')],
                ['label' => 'Profile', 'route' => route('user.profile'), 'icon' => 'bi bi-person', 'active' => request()->routeIs('user.profile*')],
                ['label' => 'Settings', 'route' => route('user.settings'), 'icon' => 'bi bi-sliders', 'active' => request()->routeIs('user.settings*')],
                ['label' => 'Notifications', 'route' => route('user.notifications'), 'icon' => 'bi bi-bell', 'active' => request()->routeIs('user.notifications*')],
                ['label' => 'Partnerships', 'route' => route('user.partnerships'), 'icon' => 'bi bi-people', 'active' => request()->routeIs('user.partnerships*')],
            ],
        ],
        [
            'label' => 'Learning',
            'items' => [
                [
                    'label' => $user->isAsomStudent() ? 'ASOM Workspace' : 'Explore ASOM',
                    'route' => $learningRoute,
                    'icon' => 'bi bi-mortarboard',
                    'active' => request()->routeIs('asom') || request()->routeIs('asom.welcome') || request()->routeIs('lms.*'),
                ],
            ],
        ],
    ];

    $workspaceItems = collect([
        $user->hasBackofficeAccess() ? ['label' => 'Primary Workspace', 'route' => route($user->dashboardRoute()), 'icon' => 'bi bi-briefcase', 'active' => request()->routeIs('admin.*') || request()->routeIs('news.*') || request()->routeIs('videos.*')] : null,
        ($user->isAdmin() || $user->hasPermission('manage-files')) ? ['label' => 'My Files', 'route' => route('user.files'), 'icon' => 'bi bi-folder2-open', 'active' => request()->routeIs('user.files') || request()->routeIs('files.*')] : null,
    ])->filter()->values();
@endphp

<aside
    class="surface-sidebar surface-sidebar-shell offcanvas-lg offcanvas-start d-flex flex-column gap-4"
    tabindex="-1"
    id="userSidebar"
    aria-labelledby="userSidebarLabel"
>
    <div class="offcanvas-header d-lg-none px-0 pt-0">
        <div>
            <div class="text-sm text-white-50 text-uppercase tracking-[0.2em]">Projectsave</div>
            <div class="fs-5 fw-semibold text-white" id="userSidebarLabel">Account Area</div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#userSidebar" aria-label="Close navigation"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column gap-4 p-0">
        <div class="surface-sidebar-brand">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex h-12 w-12 align-items-center justify-content-center rounded-circle bg-white/10 fw-semibold text-white">
                    {{ $initial }}
                </div>
                <div>
                    <div class="fs-5 fw-semibold text-white">{{ $user->name }}</div>
                    <small class="text-white-50">{{ $user->isAsomStudent() ? 'ASOM student account' : 'Member account' }}</small>
                </div>
            </div>
        </div>

        <nav class="d-flex flex-column gap-4 flex-grow-1">
            @foreach($accountSections as $section)
                <div class="d-flex flex-column gap-2">
                    <div class="surface-sidebar-section">{{ $section['label'] }}</div>

                    <div class="d-flex flex-column gap-1">
                        @foreach($section['items'] as $item)
                            <a class="surface-nav-link {{ $item['active'] ? 'active' : '' }}" href="{{ $item['route'] }}">
                                <i class="{{ $item['icon'] }}"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @if($workspaceItems->isNotEmpty())
                <div class="d-flex flex-column gap-2">
                    <div class="surface-sidebar-section">Workspaces</div>

                    <div class="d-flex flex-column gap-1">
                        @foreach($workspaceItems as $item)
                            <a class="surface-nav-link {{ $item['active'] ? 'active' : '' }}" href="{{ $item['route'] }}">
                                <i class="{{ $item['icon'] }}"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="d-flex flex-column gap-2">
                <div class="surface-sidebar-section">Sensitive</div>

                <a class="surface-nav-link {{ request()->routeIs('user.account.deletion*') ? 'active' : '' }}" href="{{ route('user.account.deletion') }}">
                    <i class="bi bi-trash"></i>
                    <span>Delete Account</span>
                </a>
            </div>
        </nav>

        <div class="surface-sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="surface-button-secondary w-100 justify-content-center">Logout</button>
            </form>
        </div>
    </div>
</aside>
