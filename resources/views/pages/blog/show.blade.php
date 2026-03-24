<x-layouts.app
    :title="$post->title . ' | Projectsave Devotional'"
    :meta-description="\Illuminate\Support\Str::limit(strip_tags($post->details), 160)"
>
    <x-ui.public-page-hero
        eyebrow="Devotional"
        :title="$post->title"
        :subtitle="trim((optional($post->published_at)->format('F d, Y') ?? '') . ' | ' . ($post->author ?? 'Projectsave International'), ' |')"
    >
        <x-slot:actions>
            <a href="{{ route('blog.index') }}" class="surface-button-secondary">Back to devotionals</a>
            <a href="{{ route('contact.show') }}" class="surface-button-primary">Contact the ministry</a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section pt-2">
        <div class="surface-frame">
            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <article class="public-card p-4 p-lg-5">
                        @if($post->image)
                            <div class="public-image-frame mb-4">
                                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" loading="lazy">
                            </div>
                        @endif

                        @if($post->bible_text)
                            <div class="public-chip mb-3">{{ $post->bible_text }}</div>
                        @endif

                        @if($post->subtitle)
                            <h2 class="h4 mb-3">{{ $post->subtitle }}</h2>
                        @endif

                        <div class="public-richtext">
                            {!! $post->details !!}
                        </div>

                        @if($post->action_point)
                            <div class="public-card mt-4 p-4">
                                <div class="public-kicker mb-2">Action point</div>
                                <p class="mb-0">{{ $post->action_point }}</p>
                            </div>
                        @endif

                        @if($post->tags->isNotEmpty())
                            <div class="d-flex flex-wrap gap-2 mt-4">
                                @foreach($post->tags as $tag)
                                    <span class="public-chip">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="d-flex justify-content-between gap-3 mt-5">
                            @if($previous)
                                <a href="{{ route('posts.show', $previous->slug) }}" class="surface-button-secondary">
                                    <i class="bi bi-arrow-left"></i>
                                    Previous article
                                </a>
                            @else
                                <span></span>
                            @endif

                            @if($next)
                                <a href="{{ route('posts.show', $next->slug) }}" class="surface-button-secondary">
                                    Next article
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    </article>

                    @if($relatedPosts->isNotEmpty())
                        <div class="mt-5">
                            <x-ui.public-section-heading
                                eyebrow="Keep reading"
                                title="Related devotionals"
                            />

                            <div class="row g-3 mt-1">
                                @foreach($relatedPosts as $relatedPost)
                                    <div class="col-md-6">
                                        <article class="public-card p-4">
                                            <div class="public-chip mb-3">{{ optional($relatedPost->published_at)->format('M d, Y') }}</div>
                                            <h3 class="h5 mb-2">
                                                <a href="{{ route('posts.show', $relatedPost->slug) }}" class="text-decoration-none">{{ $relatedPost->title }}</a>
                                            </h3>
                                            <p class="mb-0 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($relatedPost->details), 120) }}</p>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">
                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="Search"
                                title="Find another devotional"
                            />

                            <form method="GET" action="{{ route('search') }}" class="mt-4">
                                <label for="devotional-search" class="form-label fw-semibold">Search keyword</label>
                                <div class="d-flex gap-2">
                                    <input id="devotional-search" class="form-control" type="text" name="q" placeholder="Faith, prayer, mission...">
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
                                eyebrow="Categories"
                                title="Topics"
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
