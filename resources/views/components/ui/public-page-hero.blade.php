@props([
    'eyebrow'  => null,
    'title'    => '',
    'subtitle' => null,
])

<section class="surface-page-hero">
    <div class="surface-frame">
        <div class="row align-items-center justify-content-between g-4">
            <div class="col-lg-7">
                @if($eyebrow)
                    <div class="surface-eyebrow mb-2">{{ $eyebrow }}</div>
                @endif

                <h1 class="surface-page-hero-title">{{ $title }}</h1>

                @if($subtitle)
                    <p class="surface-page-hero-subtitle mt-3">{{ $subtitle }}</p>
                @endif

                @if(isset($actions))
                    <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
