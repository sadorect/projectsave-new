<x-layouts.app
    :title="$query !== '' ? 'Search results for ' . $query : 'Search Projectsave'"
    :meta-description="$query !== '' ? 'Search Projectsave devotionals, events, and FAQs for ' . $query : 'Search Projectsave devotionals, events, and FAQs.'"
>
    @php($totalResults = $posts->count() + $events->count() + $faqs->count())

    <x-ui.public-page-hero
        eyebrow="Search"
        :title="$query !== '' ? 'Results for \"' . $query . '\"' : 'Search the ministry site'"
        :subtitle="$query !== '' ? $totalResults . ' result' . ($totalResults === 1 ? '' : 's') . ' across devotionals, events, and FAQs.' : 'Use a keyword to search devotionals, events, and FAQs from one place.'"
    />

    <section class="surface-section pt-2">
        <div class="surface-frame">
            @if($totalResults > 0)
                @if($posts->isNotEmpty())
                    <div class="mb-5">
                        <x-ui.public-section-heading
                            eyebrow="Devotionals"
                            title="Related articles"
                        />

                        <div class="row g-3 mt-1">
                            @foreach($posts as $post)
                                <div class="col-lg-6">
                                    <article class="public-results-card">
                                        <div class="public-chip mb-3">Devotional</div>
                                        <h3 class="h5 mb-2">
                                            <a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none">{{ $post->title }}</a>
                                        </h3>
                                        <p class="mb-3 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($post->details), 140) }}</p>
                                        <div class="public-meta-list">
                                            <span><i class="bi bi-calendar-event me-2 text-brand-700"></i>{{ $post->created_at->format('M d, Y') }}</span>
                                            <span><i class="bi bi-person me-2 text-brand-700"></i>{{ $post->author }}</span>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($faqs->isNotEmpty())
                    <div class="mb-5">
                        <x-ui.public-section-heading
                            eyebrow="FAQs"
                            title="Frequently asked questions"
                        />

                        <div class="row g-3 mt-1">
                            @foreach($faqs as $faq)
                                <div class="col-lg-6">
                                    <article class="public-results-card">
                                        <div class="public-chip mb-3">FAQ</div>
                                        <h3 class="h5 mb-2">
                                            <a href="{{ route('faqs.show', $faq->slug) }}" class="text-decoration-none">{{ $faq->title }}</a>
                                        </h3>
                                        <p class="mb-0 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($faq->details), 150) }}</p>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($events->isNotEmpty())
                    <div>
                        <x-ui.public-section-heading
                            eyebrow="Events"
                            title="Upcoming activities"
                        />

                        <div class="row g-3 mt-1">
                            @foreach($events as $event)
                                <div class="col-lg-6">
                                    <article class="public-results-card">
                                        <div class="public-chip mb-3">Event</div>
                                        <h3 class="h5 mb-2">
                                            <a href="{{ route('events.show', $event) }}" class="text-decoration-none">{{ $event->title }}</a>
                                        </h3>
                                        <div class="public-meta-list mb-3">
                                            <span><i class="bi bi-geo-alt me-2 text-brand-700"></i>{{ $event->location }}</span>
                                            <span><i class="bi bi-calendar-event me-2 text-brand-700"></i>{{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}</span>
                                        </div>
                                        <p class="mb-0 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($event->description), 140) }}</p>
                                    </article>
                                </div>
                            @endforeach
                        </div>
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
                        <a href="{{ route('events.index') }}" class="surface-button-secondary">Browse events</a>
                    </x-slot:actions>
                </x-ui.empty-state>
            @endif
        </div>
    </section>
</x-layouts.app>
