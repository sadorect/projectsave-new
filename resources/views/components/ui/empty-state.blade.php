@props([
    'title',
    'message' => null,
    'icon' => 'bi bi-inbox',
])

<div {{ $attributes->merge(['class' => 'empty-state text-center py-5 px-3']) }}>
    @if($icon)
        <div class="empty-state-icon mb-3">
            <i class="{{ $icon }}" style="font-size: 2.5rem; opacity: 0.4;"></i>
        </div>
    @endif

    <h3 class="empty-state-title h5 mb-2">{{ $title }}</h3>

    @if($message)
        <p class="empty-state-message text-muted mb-3">{{ $message }}</p>
    @endif

    @if(isset($actions) && $actions->isNotEmpty())
        <div class="empty-state-actions d-flex flex-wrap gap-2 justify-content-center mt-3">
            {{ $actions }}
        </div>
    @endif

    @if($slot->isNotEmpty())
        <div class="empty-state-slot mt-3">
            {{ $slot }}
        </div>
    @endif
</div>
