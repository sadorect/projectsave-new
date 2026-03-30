<x-layouts.app
    :title="$query !== '' ? 'Search results for ' . $query : 'Search Projectsave'"
    :meta-description="$query !== '' ? 'Search Projectsave devotionals, ministry reports, events, and FAQs for ' . $query : 'Search Projectsave devotionals, ministry reports, events, and FAQs.'"
>
    @php
        $totalResults = $results->total();
        $heroTitle    = $query !== '' ? 'Results for "' . $query . '"' : 'Search the ministry site';
        $heroSubtitle = $query !== ''
            ? $totalResults . ' result' . ($totalResults === 1 ? '' : 's') . ' across devotionals, reports, events, and FAQs.'
            : 'Use a keyword to search devotionals, reports, events, and FAQs from one place.';
    @endphp

    <x-ui.public-page-hero
        eyebrow="Search"
        :title="$heroTitle"
        :subtitle="$heroSubtitle"
    />

    <section class="surface-section pt-2">
        <div class="surface-frame">
            @if($totalResults > 0)
                <div class="row g-3 mt-1">
                    @foreach($results as $result)
                        <div class="col-lg-6">
                            <article class="public-results-card">
                                <div class="public-chip mb-3">{{ $result['type_label'] }}</div>
                                <h3 class="h5 mb-2">
                                    <a href="{{ $result['url'] }}" class="text-decoration-none">{{ $result['title'] }}</a>
                                </h3>
                                @if(!empty($result['meta']))
                                    <div class="public-meta-list mb-3">
                                        @foreach($result['meta'] as $m)
                                            @if($m['value'])
                                                <span><i class="{{ $m['icon'] }} me-2 text-brand-700"></i>{{ $m['value'] }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                @if($result['excerpt'])
                                    <p class="mb-0 text-muted">{{ $result['excerpt'] }}</p>
                                @endif
                            </article>
                        </div>
                    @endforeach
                </div>

                @if($results->hasPages())
                    <div class="mt-5 d-flex justify-content-center">
                        {{ $results->links() }}
                    </div>
                @endif
            @else
                <x-ui.empty-state
                    title="No results found"
                    message="Try a different keyword or browse our public sections directly."
                    icon="bi bi-search"
                >
                    <x-slot:actions>
                        <a href="{{ route('blog.index') }}" class="surface-button-secondary">Browse devotionals</a>
                        <a href="{{ route('reports.index') }}" class="surface-button-secondary">Browse reports</a>
                        <a href="{{ route('events.index') }}" class="surface-button-secondary">Browse events</a>
                    </x-slot:actions>
                </x-ui.empty-state>
            @endif
        </div>
    </section>
</x-layouts.app>

