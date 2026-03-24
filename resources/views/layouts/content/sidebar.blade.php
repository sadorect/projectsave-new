@php($initial = strtoupper(substr(auth()->user()->name, 0, 1)))

<aside class="surface-sidebar surface-sidebar-shell d-flex flex-column gap-4">
    <div class="surface-sidebar-brand">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex h-12 w-12 align-items-center justify-content-center rounded-circle bg-white/10 fw-semibold text-white">
                {{ $initial }}
            </div>
            <div>
                <div class="fs-5 fw-semibold text-white">{{ auth()->user()->name }}</div>
                <small class="text-white-50">Content workspace</small>
            </div>
        </div>
    </div>

    <nav class="d-flex flex-column gap-4 flex-grow-1">
        @foreach($contentNavigation ?? [] as $section)
            <div class="d-flex flex-column gap-2">
                @if(! empty($section['label']))
                    <div class="surface-sidebar-section">{{ $section['label'] }}</div>
                @endif

                <div class="d-flex flex-column gap-1">
                    @foreach($section['items'] as $item)
                        @if($item['url'])
                            <a class="surface-nav-link {{ $item['active'] ? 'active' : '' }}" href="{{ $item['url'] }}">
                                @if($item['icon'])
                                    <i class="{{ $item['icon'] }}"></i>
                                @endif
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    <div class="surface-sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="surface-button-secondary w-100 justify-content-center">Logout</button>
        </form>
    </div>
</aside>
