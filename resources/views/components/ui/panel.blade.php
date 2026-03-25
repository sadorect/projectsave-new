@props([
    'title' => '',
    'subtitle' => '',
])

<div class="admin-dashboard-panel">
    @if ($title || $subtitle)
        <div class="mb-4">
            @if ($title)
                <h3 class="h5 mb-1">{{ $title }}</h3>
            @endif
            @if ($subtitle)
                <p class="text-muted small mb-0">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    {{ $slot }}
</div>
