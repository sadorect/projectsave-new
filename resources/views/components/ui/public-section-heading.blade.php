@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'align' => 'start',
])

<div class="public-section-heading mb-4 text-{{ $align }}">
    @if($eyebrow)
        <div class="public-kicker mb-2">{{ $eyebrow }}</div>
    @endif
    <h2 class="public-section-title">{{ $title }}</h2>
    @if($description)
        <p class="public-section-description text-muted">{{ $description }}</p>
    @endif
</div>
