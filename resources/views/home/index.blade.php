<x-layouts.app
    title="Projectsave International | Outreach, discipleship, and ministry training"
    meta-description="Projectsave International equips believers through evangelism, discipleship, events, devotionals, and the Archippus School of Ministry."
>
    @php
        $leadEvent = $latestEvents->first();
        $leadPost = $posts->first();
        $leadUpdate = $newsUpdates->first();
    @endphp

    <section class="surface-section home-hero-section pt-0 pb-3">
        <div class="home-hero-banner" style="--about-hero-image: url('{{ asset('frontend/img/carousel-1.jpg') }}');">
            <div class="surface-frame">
                <div class="home-hero-content">
                    <div class="home-hero-copy">
                        <div class="home-hero-kicker">Winning the lost. Building the saints.</div>
                        <h1 class="home-hero-title">A Christ-centered ministry for evangelism, discipleship, and ministry formation across nations.</h1>
                        <p class="home-hero-lead">
                            Projectsave International advances the Gospel through public outreaches, biblical teaching, daily devotionals, strategic partnership, and the Archippus School of Ministry.
                        </p>

                        <div class="home-hero-actions">
                            <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="surface-button-primary">Join the mission</a>
                            <a href="{{ route('events.index') }}" class="surface-button-secondary">See upcoming events</a>
                            <a href="{{ route('lms.courses.index') }}" class="surface-button-secondary">Explore ASOM</a>
                        </div>

                        <div class="home-hero-stats">
                            <div class="home-hero-stat">
                                <strong>8+ nations</strong>
                                <span>Reached through evangelism, discipleship, and ministry service.</span>
                            </div>
                            <div class="home-hero-stat">
                                <strong>{{ $latestEvents->count() }} upcoming events</strong>
                                <span>Public ministry moments available for prayer, presence, and participation.</span>
                            </div>
                            <div class="home-hero-stat">
                                <strong>{{ $posts->count() }} recent devotionals</strong>
                                <span>Fresh scriptural encouragement for daily faith, mission, and obedience.</span>
                            </div>
                        </div>
                    </div>

                    <aside class="home-hero-panel">
                        <div class="public-kicker mb-3 text-white">Now and next</div>
                        <h2 class="h3 mb-3">Enter the ministry through a clearer public front door.</h2>
                        <p class="mb-4">
                            Follow what God is doing now, read the latest devotional, and identify your next step in service, partnership, or ministry training.
                        </p>

                        <div class="d-flex flex-column gap-3">
                            <a href="{{ $leadEvent ? route('events.show', $leadEvent) : route('events.index') }}" class="home-hero-highlight text-decoration-none">
                                <span class="home-hero-highlight-label">Upcoming gathering</span>
                                <strong>{{ $leadEvent?->title ?? 'See the next ministry event' }}</strong>
                                <span>
                                    @if($leadEvent)
                                        {{ optional($leadEvent->start_date)->format('M d, Y') }} · {{ $leadEvent->location }}
                                    @else
                                        View public gatherings, outreach dates, and ministry moments.
                                    @endif
                                </span>
                            </a>

                            <a href="{{ $leadPost ? route('posts.show', $leadPost->slug) : route('blog.index') }}" class="home-hero-highlight text-decoration-none">
                                <span class="home-hero-highlight-label">Latest devotional</span>
                                <strong>{{ $leadPost?->title ?? 'Read today’s devotional archive' }}</strong>
                                <span>
                                    @if($leadPost)
                                        {{ optional($leadPost->published_at)->format('M d, Y') }} · {{ $leadPost->author ?? 'Projectsave Team' }}
                                    @else
                                        Scripture-rooted encouragement and teaching for daily growth.
                                    @endif
                                </span>
                            </a>

                            <a href="{{ route('lms.courses.index') }}" class="home-hero-highlight text-decoration-none">
                                <span class="home-hero-highlight-label">School of ministry</span>
                                <strong>Grow through structured biblical training</strong>
                                <span>Discover ASOM courses, learning tracks, and ministry formation resources.</span>
                            </a>
                        </div>

                        @if($leadUpdate)
                            <div class="home-hero-footnote">
                                <span class="home-hero-footnote-label">Latest ministry update</span>
                                <p class="mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($leadUpdate->description), 120) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="surface-section">
        <div class="surface-frame">
            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <x-ui.public-section-heading
                        eyebrow="What drives the work"
                        title="Christ-centered outreach, discipleship, and ministry formation"
                        description="Projectsave exists to preach the Gospel, build believers, and equip people for effective service in their communities and calling."
                    />

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="public-card p-4">
                                <div class="public-chip mb-3">Outreach</div>
                                <h3 class="h5">Taking the Gospel to the nations</h3>
                                <p class="mb-0 text-muted">We support field evangelism, community impact, and practical ministry engagement in underserved places.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="public-card p-4">
                                <div class="public-chip mb-3">Discipleship</div>
                                <h3 class="h5">Building strong believers</h3>
                                <p class="mb-0 text-muted">Teaching, devotionals, and structured follow-up help believers grow in truth, faith, and service.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="public-card p-4">
                                <div class="public-chip mb-3">Training</div>
                                <h3 class="h5">Equipping ministry workers</h3>
                                <p class="mb-0 text-muted">ASOM provides practical biblical training for people preparing to serve with clarity and conviction.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="public-card p-4 h-100">
                        <x-ui.public-section-heading
                            eyebrow="Latest updates"
                            title="What is happening now"
                            description="Recent ministry news, field activity, and current emphasis from the team."
                        />

                        <div class="d-flex flex-column gap-3">
                            @forelse($newsUpdates as $update)
                                <article class="border rounded-4 p-3">
                                    <div class="public-chip mb-2">{{ \Carbon\Carbon::parse($update->date)->format('M d, Y') }}</div>
                                    <h3 class="h6 mb-2">{{ $update->title }}</h3>
                                    @if($update->description)
                                        <p class="mb-0 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($update->description), 120) }}</p>
                                    @endif
                                </article>
                            @empty
                                <x-ui.empty-state
                                    title="Updates are on the way"
                                    message="Fresh ministry updates will appear here as new news items are published."
                                    icon="bi bi-broadcast-pin"
                                />
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="surface-section">
        <div class="surface-frame">
            <x-ui.public-section-heading
                eyebrow="Field stories"
                title="Watch recent ministry reels without the heavy page load"
                description="Video reels now load only when you choose to watch them, keeping the homepage lighter and easier to scan."
            />

            <div class="row g-4">
                @forelse($videoReels as $video)
                    <div class="col-lg-4 col-md-6">
                        <article class="public-video-card" data-video-card>
                            <div class="public-video-poster" data-video-frame>
                                <img src="https://img.youtube.com/vi/{{ $video->youtube_id }}/hqdefault.jpg" alt="{{ $video->title }}" loading="lazy">
                                <button type="button" class="surface-button-primary public-video-trigger" data-video-embed="{{ $video->youtube_id }}">
                                    <i class="bi bi-play-fill"></i>
                                    Watch reel
                                </button>
                            </div>
                            <div class="p-4">
                                <h3 class="h5 mb-2">{{ $video->title }}</h3>
                                <p class="mb-0 text-muted">A short window into the work, stories, and teaching taking place across the ministry.</p>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <x-ui.empty-state
                            title="No reels published yet"
                            message="New field videos will appear here as soon as they are available."
                            icon="bi bi-camera-video"
                        />
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="surface-section">
        <div class="surface-frame">
            <div class="row g-4">
                <div class="col-lg-6">
                    <x-ui.public-section-heading
                        eyebrow="Upcoming events"
                        title="Gather with us in person or online"
                        description="Stay ready for ministry events, teaching gatherings, and outreach opportunities."
                    />

                    <div class="d-flex flex-column gap-3">
                        @forelse($latestEvents as $event)
                            <article class="public-card p-4">
                                <div class="d-flex flex-column gap-3 flex-md-row align-items-md-start">
                                    <div class="public-chip">
                                        {{ optional($event->start_date)->format('M d, Y') }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h3 class="h5 mb-2">{{ $event->title }}</h3>
                                        <div class="public-meta-list mb-3">
                                            <span><i class="bi bi-geo-alt me-2 text-brand-700"></i>{{ $event->location }}</span>
                                            @if($event->start_time)
                                                <span><i class="bi bi-clock me-2 text-brand-700"></i>{{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}@if($event->end_time) - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}@endif</span>
                                            @endif
                                        </div>
                                        <p class="mb-3 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($event->description), 140) }}</p>
                                        <a href="{{ route('events.show', $event) }}" class="surface-button-secondary">View event</a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <x-ui.empty-state
                                title="No upcoming events"
                                message="Check back soon for the next public ministry gathering."
                                icon="bi bi-calendar-event"
                            />
                        @endforelse
                    </div>
                </div>

                <div class="col-lg-6">
                    <x-ui.public-section-heading
                        eyebrow="Recent devotionals"
                        title="Daily encouragement rooted in Scripture"
                        description="Read recent devotionals designed to strengthen faith, clarify calling, and encourage obedience."
                    />

                    <div class="row g-3">
                        @forelse($posts as $post)
                            <div class="col-12">
                                <article class="public-card p-4">
                                    <div class="d-flex flex-column gap-3 flex-md-row align-items-md-start">
                                        <div class="public-chip">{{ optional($post->published_at)->format('M d, Y') }}</div>
                                        <div class="flex-grow-1">
                                            <h3 class="h5 mb-2">
                                                <a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none">{{ $post->title }}</a>
                                            </h3>
                                            <p class="mb-3 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($post->details), 150) }}</p>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($post->categories as $category)
                                                    <span class="public-chip">{{ $category->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
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
                </div>
            </div>
        </div>
    </section>

    <section class="surface-section">
        <div class="surface-frame">
            <div class="public-card p-4 p-lg-5">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-7">
                        <div class="public-kicker mb-3">Join the mission</div>
                        <h2 class="mb-3">Serve, pray, or partner with what God has placed in your hands.</h2>
                        <p class="mb-0 text-muted">
                            Whether you want to join prayer force, serve on the ground, contribute professional skill, or support the work financially, there is a clear next step for you.
                        </p>
                    </div>
                    <div class="col-lg-5">
                        <div class="d-grid gap-2">
                            <a href="{{ route('partners.create', ['type' => 'prayer']) }}" class="surface-button-primary justify-content-center">Join Prayer Force</a>
                            <a href="{{ route('partners.create', ['type' => 'skilled']) }}" class="surface-button-secondary justify-content-center">Offer Professional Skill</a>
                            <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="surface-button-secondary justify-content-center">Volunteer on the Ground</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
