@props([
    'eyebrow'  => null,
    'title'    => '',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'surface-page-header']) }}>
    @if($eyebrow)
        <div class="surface-eyebrow mb-2">{{ $eyebrow }}</div>
    @endif

    <h1 class="h2 fw-bold mb-0">{{ $title }}</h1>

    @if($subtitle)
        <p class="text-muted mt-2 mb-0">{{ $subtitle }}</p>
    @endif
</div>
