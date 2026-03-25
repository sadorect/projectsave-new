<aside
    class="surface-sidebar surface-sidebar-shell offcanvas-lg offcanvas-start d-flex flex-column gap-4"
    tabindex="-1"
    id="adminSidebar"
    aria-labelledby="adminSidebarLabel"
>
    <div class="offcanvas-header d-lg-none px-0 pt-0">
        <div>
            <div class="text-sm text-white-50 text-uppercase tracking-[0.2em]">Projectsave</div>
            <div class="fs-5 fw-semibold text-white" id="adminSidebarLabel">Back Office</div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Close navigation"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column gap-4 p-0">
    <a href="{{ route('home') }}" class="surface-sidebar-brand text-decoration-none">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex h-12 w-12 align-items-center justify-content-center rounded-4 bg-white/10 text-white shadow-sm">
                <i class="bi bi-command"></i>
            </div>
            <div>
                <div class="text-sm text-white-50 text-uppercase tracking-[0.2em]">Projectsave</div>
                <div class="fs-5 fw-semibold text-white">Back Office</div>
            </div>
        </div>
    </a>

    <nav class="d-flex flex-column gap-4 flex-grow-1">
        @foreach($adminNavigation ?? [] as $section)
            <div class="d-flex flex-column gap-2">
                @if(! empty($section['label']))
                    <div class="surface-sidebar-section">{{ $section['label'] }}</div>
                @endif

                <ul class="nav flex-column gap-1">
                    @foreach($section['items'] as $item)
                        @if($item['children'] !== [])
                            <li class="nav-item admin-nav-group {{ $item['open'] ? 'is-open' : '' }}">
                                <button
                                    type="button"
                                    class="surface-nav-link admin-nav-toggle justify-content-between w-100 border-0 {{ $item['open'] ? 'active' : '' }}"
                                    data-admin-nav-toggle
                                    data-admin-nav-target="nav-{{ $item['id'] }}"
                                    aria-expanded="{{ $item['open'] ? 'true' : 'false' }}"
                                    aria-controls="nav-{{ $item['id'] }}"
                                >
                                    <span class="d-flex align-items-center gap-3">
                                        @if($item['icon'])
                                            <i class="{{ $item['icon'] }}"></i>
                                        @endif
                                        <span>{{ $item['label'] }}</span>
                                    </span>

                                    <span class="d-flex align-items-center gap-2">
                                        @if($item['badge'])
                                            <span class="badge rounded-pill bg-warning text-dark">{{ $item['badge'] }}</span>
                                        @endif
                                        <i class="bi bi-chevron-down small admin-nav-chevron"></i>
                                    </span>
                                </button>

                                <div class="admin-subnav {{ $item['open'] ? 'is-open' : '' }}" id="nav-{{ $item['id'] }}" data-admin-nav-section>
                                    <ul class="nav flex-column gap-1 admin-subnav-list">
                                        @foreach($item['children'] as $child)
                                            <li class="nav-item">
                                                <a href="{{ $child['url'] }}" class="surface-nav-link admin-subnav-link {{ $child['active'] ? 'active' : '' }}" data-admin-nav-link>
                                                    @if($child['icon'])
                                                        <i class="{{ $child['icon'] }}"></i>
                                                    @endif
                                                    <span>{{ $child['label'] }}</span>
                                                    @if($child['badge'])
                                                        <span class="badge rounded-pill bg-warning text-dark ms-auto">{{ $child['badge'] }}</span>
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @elseif($item['url'])
                            <li class="nav-item">
                                <a href="{{ $item['url'] }}" class="surface-nav-link {{ $item['active'] ? 'active' : '' }}" data-admin-nav-link>
                                    @if($item['icon'])
                                        <i class="{{ $item['icon'] }}"></i>
                                    @endif
                                    <span>{{ $item['label'] }}</span>
                                    @if($item['badge'])
                                        <span class="badge rounded-pill bg-warning text-dark ms-auto">{{ $item['badge'] }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    @endforeach
            </ul>
        </div>
    @endforeach
    </nav>

    <div class="surface-sidebar-footer">
        <div class="dropdown">
            <a
                href="#"
                class="surface-nav-link active justify-content-between"
                id="adminUserMenu"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                <span class="d-flex align-items-center gap-3">
                    <i class="bi bi-person-circle"></i>
                    <span>
                        <span class="d-block">{{ auth()->user()->name }}</span>
                        <small class="text-white-50">{{ auth()->user()->email }}</small>
                    </span>
                </span>
                <i class="bi bi-chevron-up small"></i>
            </a>

            <ul class="dropdown-menu dropdown-menu-dark w-100 text-small shadow" aria-labelledby="adminUserMenu">
                <li><a class="dropdown-item" href="{{ route('user.dashboard') }}">Account dashboard</a></li>
                <li><a class="dropdown-item" href="{{ route('home') }}">Public site</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Sign out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
    </div>
</aside>
