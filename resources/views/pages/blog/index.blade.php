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
        :subtitle="$selectedDate
            ? 'Archive for ' . $selectedDate . '. Read the devotionals published on that day.'
            : ($selectedCategory
                ? 'Browsing devotionals in the ' . $selectedCategory->name . ' category.'
                : ($selectedTag
                    ? 'Browsing devotionals tagged with ' . $selectedTag->name . '.'
                    : 'Scripture-rooted teaching and devotional encouragement that strengthens faith, clarifies calling, and supports kingdom service.'))"
    >
        <x-slot:actions>
            @if($selectedDate || $selectedCategory || $selectedTag)
                <a href="{{ route('blog.index') }}" class="surface-button-secondary">
                    <i class="bi bi-x-circle me-2"></i>Clear filters
                </a>
            @else
                <a href="{{ route('faqs.list') }}" class="surface-button-secondary">Browse FAQs</a>
            @endif
            <a href="{{ route('search') }}?q=faith" class="surface-button-primary">
                <i class="bi bi-search me-2"></i>Search the archive
            </a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section">
        <div class="surface-frame">
            <div class="row g-5 align-items-start">

                {{-- Main content --}}
                <div class="col-lg-8">

                    {{-- Featured devotional --}}
                    @if($featuredPost)
                        <div class="devotional-featured-card mb-5">
                            @if($featuredPost->image)
                                <div class="devotional-featured-image">
                                    <img src="{{ asset('storage/' . $featuredPost->image) }}" alt="{{ $featuredPost->title }}" loading="lazy">
                                </div>
                            @endif
                            <div class="devotional-featured-body {{ !$featuredPost->image ? 'p-lg-4' : '' }}">
                                <span class="devotional-featured-label">
                                    <i class="bi bi-bookmark-star-fill"></i>
                                    {{ $selectedDate ? 'Featured from this day' : 'Featured devotional' }}
                                </span>
                                <h2 class="devotional-featured-title">{{ $featuredPost->title }}</h2>
                                <p class="devotional-featured-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($featuredPost->details), 220) }}</p>
                                <div class="devotional-featured-meta">
                                    <span><i class="bi bi-person-fill"></i>{{ $featuredPost->author }}</span>
                                    <span><i class="bi bi-calendar-event-fill"></i>{{ optional($featuredPost->published_at)->format('M d, Y') }}</span>
                                    <span><i class="bi bi-bar-chart-fill"></i>{{ number_format($featuredPost->view_count) }} views</span>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('posts.show', $featuredPost->slug) }}" class="surface-button-primary" style="font-size:0.88rem;">
                                        Read the devotional <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Archive grid --}}
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <span class="page-section-eyebrow">Archive</span>
                            <h2 class="page-section-title mb-0" style="font-size:1.5rem;">
                                {{ $selectedDate ? 'Devotionals on ' . $selectedDate : ($selectedCategory ? $selectedCategory->name . ' devotionals' : ($selectedTag ? 'Tagged: ' . $selectedTag->name : 'Recent devotionals')) }}
                            </h2>
                        </div>
                    </div>

                    <div class="row g-4">
                        @forelse($remainingPosts as $post)
                            <div class="col-md-6">
                                <article class="devotional-card">
                                    @if($post->image)
                                        <div class="devotional-card-image">
                                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" loading="lazy">
                                        </div>
                                    @else
                                        <div style="aspect-ratio:16/9;background:linear-gradient(135deg,rgba(193,18,31,0.06),rgba(188,111,44,0.06));display:flex;align-items:center;justify-content:center;">
                                            <i class="bi bi-journal-richtext" style="font-size:2.5rem;color:rgba(193,18,31,0.25);"></i>
                                        </div>
                                    @endif
                                    <div class="devotional-card-body">
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="public-chip">{{ optional($post->published_at)->format('M d, Y') }}</span>
                                            @foreach($post->categories as $category)
                                                <a href="{{ route('blog.index', ['category' => $category->slug]) }}" class="public-chip text-decoration-none">{{ $category->name }}</a>
                                            @endforeach
                                        </div>
                                        <h3 class="m-0" style="font-size:0.0rem;">
                                            <a href="{{ route('posts.show', $post->slug) }}" class="devotional-card-title">{{ $post->title }}</a>
                                        </h3>
                                        <p class="devotional-card-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($post->details), 140) }}</p>
                                        <div class="d-flex align-items-center gap-2 text-muted" style="font-size:0.8rem;">
                                            <i class="bi bi-bar-chart-fill"></i>
                                            <span>{{ number_format($post->view_count) }} views</span>
                                        </div>
                                        <a href="{{ route('posts.show', $post->slug) }}" class="surface-button-secondary" style="font-size:0.82rem;padding:0.45rem 0.9rem;align-self:flex-start;margin-top:auto;">
                                            Read more <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
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

                    <div class="mt-5 d-flex justify-content-center">
                        {{ $posts->links() }}
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">
                        <div class="public-sidebar-card">
                            <span class="page-section-eyebrow mb-2 d-block">Search</span>
                            <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:0.4rem;">Find a devotional</h3>
                            <p style="font-size:0.85rem;color:#6b7280;line-height:1.65;margin-bottom:1rem;">Search by topic, Scripture reference, or ministry theme.</p>
                            <form method="GET" action="{{ route('search') }}">
                                <label for="devotional-archive-search" class="visually-hidden">Search keyword</label>
                                <div class="d-flex gap-2">
                                    <input id="devotional-archive-search" class="form-control" type="text" name="q" placeholder="Faith, prayer, mission...">
                                    <button class="surface-button-primary" type="submit" style="white-space:nowrap;"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>

                        <x-blog.calendar
                            :calendar="$calendar"
                            :current-month="$currentMonth"
                            :calendar-month="$calendarMonth"
                            :calendar-year="$calendarYear"
                            :calendar-start-month="$calendarStartMonth"
                            :calendar-start-year="$calendarStartYear"
                            :calendar-end-month="$calendarEndMonth"
                            :calendar-end-year="$calendarEndYear"
                            :post-calendar-days="$postCalendarDays"
                        />

                        <div class="public-sidebar-card">
                            <span class="page-section-eyebrow mb-2 d-block">Recent</span>
                            <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:1rem;">Recent devotionals</h3>
                            <div class="d-flex flex-column gap-2">
                                @foreach($recentPosts as $recentPost)
                                    <a href="{{ route('posts.show', $recentPost->slug) }}" class="text-decoration-none">
                                        <div style="border:1px solid rgba(148,163,184,0.15);border-radius:12px;padding:0.75rem 1rem;background:#f8fafc;transition:background 180ms,border-color 180ms;">
                                            <p style="font-size:0.88rem;font-weight:600;color:#0f172a;margin:0 0 0.2rem;">{{ $recentPost->title }}</p>
                                            <small style="color:#94a3b8;font-size:0.75rem;">{{ optional($recentPost->published_at)->format('M d, Y') }}</small>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="public-sidebar-card">
                            <span class="page-section-eyebrow mb-2 d-block">Topics</span>
                            <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:1rem;">Browse categories</h3>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($categories as $category)
                                    <a href="{{ route('blog.index', ['category' => $category->slug]) }}" class="public-chip text-decoration-none">{{ $category->name }} ({{ $category->posts_count }})</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
