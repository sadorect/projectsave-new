<x-layouts.app
    :title="$post->title . ' | Projectsave Devotional'"
    :meta-description="\Illuminate\Support\Str::limit(strip_tags($post->details), 160)"
>
    <x-ui.public-page-hero
        eyebrow="Devotional"
        :title="$post->title"
        :subtitle="trim((optional($post->published_at)->format('F d, Y') ?? '') . ' · ' . ($post->author ?? 'Projectsave International') . ' · ' . number_format($post->view_count) . ' views', ' ·')"
    >
        <x-slot:actions>
            <a href="{{ route('blog.index') }}" class="surface-button-secondary">
                <i class="bi bi-arrow-left me-2"></i>All devotionals
            </a>
            <a href="{{ route('contact.show') }}" class="surface-button-primary">
                <i class="bi bi-envelope me-2"></i>Contact ministry
            </a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section">
        <div class="surface-frame">
            <div class="row g-5 align-items-start">

                {{-- Article --}}
                <div class="col-lg-8">
                    <article class="devotional-article">

                        @if($post->image)
                            <div class="public-image-frame mb-4" style="aspect-ratio:16/7;border-radius:14px;">
                                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" loading="lazy">
                            </div>
                        @endif

                        @if($post->bible_text)
                            <div class="devotional-scripture-box">
                                <i class="bi bi-book me-2"></i>
                                <div class="public-richtext d-inline">{!! $post->bible_text !!}</div>
                            </div>
                        @endif

                        @if($post->subtitle)
                            <h2 style="font-size:1.18rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem;line-height:1.4;">{{ $post->subtitle }}</h2>
                        @endif

                        @if($post->categories->isNotEmpty())
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                @foreach($post->categories as $category)
                                    <a href="{{ route('blog.index', ['category' => $category->slug]) }}" class="public-chip text-decoration-none">{{ $category->name }}</a>
                                @endforeach
                            </div>
                        @endif

                        <div class="public-richtext">
                            {!! $post->details !!}
                        </div>

                        @if($post->action_point)
                            <div class="devotional-action-point">
                                <div class="devotional-action-label">
                                    <i class="bi bi-lightning-charge-fill"></i>
                                    Action point
                                </div>
                                <div class="public-richtext" style="font-size:0.95rem;color:#374151;line-height:1.75;margin:0;">
                                    {!! $post->action_point !!}
                                </div>
                            </div>
                        @endif

                        @if($post->tags->isNotEmpty())
                            <div class="d-flex flex-wrap gap-2 mt-4">
                                @foreach($post->tags as $tag)
                                    <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="public-chip text-decoration-none">{{ $tag->name }}</a>
                                @endforeach
                            </div>
                        @endif

                        <div class="article-nav">
                            @if($previous)
                                <a href="{{ route('posts.show', $previous->slug) }}" class="article-nav-link">
                                    <i class="bi bi-arrow-left"></i>
                                    Previous
                                </a>
                            @else
                                <span></span>
                            @endif

                            @if($next)
                                <a href="{{ route('posts.show', $next->slug) }}" class="article-nav-link">
                                    Next
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    </article>

                    @if($relatedPosts->isNotEmpty())
                        <div class="mt-5">
                            <span class="page-section-eyebrow">Keep reading</span>
                            <h2 class="page-section-title" style="font-size:1.5rem;">Related devotionals</h2>

                            <div class="row g-3 mt-1">
                                @foreach($relatedPosts as $relatedPost)
                                    <div class="col-md-6">
                                        <article class="devotional-card">
                                            <div class="devotional-card-body">
                                                <span class="public-chip">{{ optional($relatedPost->published_at)->format('M d, Y') }}</span>
                                                <h3 class="m-0">
                                                    <a href="{{ route('posts.show', $relatedPost->slug) }}" class="devotional-card-title">{{ $relatedPost->title }}</a>
                                                </h3>
                                                <p class="devotional-card-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($relatedPost->details), 120) }}</p>
                                            </div>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">
                        <div class="public-sidebar-card">
                            <span class="page-section-eyebrow mb-2 d-block">Search</span>
                            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:0.8rem;">Find another devotional</h3>
                            <form method="GET" action="{{ route('search') }}">
                                <label for="devotional-search" class="visually-hidden">Search keyword</label>
                                <div class="d-flex gap-2">
                                    <input id="devotional-search" class="form-control" type="text" name="q" placeholder="Faith, prayer, mission...">
                                    <button class="surface-button-primary" type="submit"><i class="bi bi-search"></i></button>
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
                            <span class="page-section-eyebrow mb-2 d-block">Recent</span>
                            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:0.8rem;">Recent devotionals</h3>
                            <div class="d-flex flex-column gap-2">
                                @foreach($recentPosts as $recentPost)
                                    <a href="{{ route('posts.show', $recentPost->slug) }}" class="text-decoration-none">
                                        <div style="border:1px solid rgba(148,163,184,0.15);border-radius:12px;padding:0.75rem 1rem;background:#f8fafc;">
                                            <p style="font-size:0.87rem;font-weight:600;color:#0f172a;margin:0 0 0.15rem;">{{ $recentPost->title }}</p>
                                            <small style="color:#94a3b8;font-size:0.75rem;">{{ optional($recentPost->published_at)->format('M d, Y') }}</small>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="public-sidebar-card">
                            <span class="page-section-eyebrow mb-2 d-block">Categories</span>
                            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:0.8rem;">Topics</h3>
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
