<x-layouts.app
    title="Projectsave Devotionals"
    meta-description="Read devotionals and ministry articles designed to strengthen believers and equip them for kingdom service."
>
    @php
        $featuredPost = $posts->count() ? $posts->getCollection()->first() : null;
        $remainingPosts = $posts->count() > 1 ? $posts->getCollection()->slice(1) : collect();
    @endphp

    <x-ui.public-page-hero
        eyebrow="Devotionals"
        title="Daily bread for spiritual growth"
        :subtitle="$selectedDate ? 'Archive for ' . $selectedDate . '. Read the devotionals published on that day and explore the wider archive from the sidebar.' : 'Read Scripture-rooted teaching and devotional encouragement that strengthens faith, clarifies calling, and supports kingdom service.'"
    >
        <x-slot:actions>
            @if($selectedDate)
                <a href="{{ route('blog.index') }}" class="surface-button-secondary">Clear date filter</a>
            @else
                <a href="{{ route('faqs.list') }}" class="surface-button-secondary">Browse FAQs</a>
            @endif
            <a href="{{ route('search') }}?q=faith" class="surface-button-primary">Search the archive</a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section pt-2">
        <div class="surface-frame">
            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    @if($featuredPost)
                        <div class="public-card overflow-hidden mb-5">
                            <div class="row g-0 align-items-stretch">
                                @if($featuredPost->image)
                                    <div class="col-xl-5">
                                        <div class="public-image-frame rounded-0 h-100">
                                            <img src="{{ asset('storage/' . $featuredPost->image) }}" alt="{{ $featuredPost->title }}" loading="lazy">
                                        </div>
                                    </div>
                                @endif
                                <div class="{{ $featuredPost->image ? 'col-xl-7' : 'col-12' }}">
                                    <div class="p-4 p-lg-5">
                                        <div class="public-chip mb-3">{{ $selectedDate ? 'Featured from this day' : 'Featured devotional' }}</div>
                                        <h2 class="mb-3">{{ $featuredPost->title }}</h2>
                                        <p class="mb-3 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($featuredPost->details), 220) }}</p>
                                        <div class="public-meta-list mb-4">
                                            <span><i class="bi bi-person me-2 text-brand-700"></i>{{ $featuredPost->author }}</span>
                                            <span><i class="bi bi-calendar-event me-2 text-brand-700"></i>{{ optional($featuredPost->published_at)->format('M d, Y') }}</span>
                                        </div>
                                        <a href="{{ route('posts.show', $featuredPost->slug) }}" class="surface-button-primary">Read the devotional</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <x-ui.public-section-heading
                        eyebrow="Archive"
                        :title="$selectedDate ? 'Devotionals published on ' . $selectedDate : 'Recent devotionals'"
                        :description="$selectedDate ? 'Browse every devotional published on the selected day or clear the filter to return to the full archive.' : 'Explore recent articles published by the ministry.'"
                    />

                    <div class="row g-4 mt-1">
                        @forelse($remainingPosts as $post)
                            <div class="col-xl-6">
                                <article class="public-card overflow-hidden">
                                    @if($post->image)
                                        <div class="public-image-frame rounded-bottom-0">
                                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" loading="lazy">
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            <span class="public-chip">{{ optional($post->published_at)->format('M d, Y') }}</span>
                                            @foreach($post->categories as $category)
                                                <span class="public-chip">{{ $category->name }}</span>
                                            @endforeach
                                        </div>
                                        <h3 class="h5 mb-3">
                                            <a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none">{{ $post->title }}</a>
                                        </h3>
                                        <p class="mb-0 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($post->details), 150) }}</p>
                                    </div>
                                </article>
                            </div>
                        @empty
                            <div class="col-12">
                                <x-ui.empty-state
                                    title="No devotionals available"
                                    message="Published devotionals will appear here as soon as they are available."
                                    icon="bi bi-journal-richtext"
                                />
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-5">
                        {{ $posts->links() }}
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">
                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="Search"
                                title="Find a devotional"
                                description="Search the archive by topic, Scripture emphasis, or ministry theme."
                            />

                            <form method="GET" action="{{ route('search') }}" class="mt-4">
                                <label for="devotional-archive-search" class="form-label fw-semibold">Search keyword</label>
                                <div class="d-flex gap-2">
                                    <input id="devotional-archive-search" class="form-control" type="text" name="q" placeholder="Faith, prayer, mission...">
                                    <button class="surface-button-primary" type="submit">Go</button>
                                </div>
                            </form>
                        </div>

                        <x-blog.calendar
                            :calendar="$calendar"
                            :current-month="$currentMonth"
                            :calendar-month="$calendarMonth"
                            :calendar-year="$calendarYear"
                            :post-calendar-days="$postCalendarDays"
                        />

                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="Recent"
                                title="Recent devotionals"
                            />

                            <div class="d-flex flex-column gap-3 mt-4">
                                @foreach($recentPosts as $recentPost)
                                    <a href="{{ route('posts.show', $recentPost->slug) }}" class="text-decoration-none">
                                        <div class="border rounded-4 p-3">
                                            <h3 class="h6 mb-1">{{ $recentPost->title }}</h3>
                                            <small class="text-muted">{{ optional($recentPost->published_at)->format('M d, Y') }}</small>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="Topics"
                                title="Browse categories"
                            />

                            <div class="d-flex flex-wrap gap-2 mt-4">
                                @foreach($categories as $category)
                                    <span class="public-chip">{{ $category->name }} ({{ $category->posts_count }})</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
