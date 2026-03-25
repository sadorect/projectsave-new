<x-layouts.app
    title="Projectsave International | Outreach, Discipleship & Ministry Training"
    meta-description="Projectsave International equips believers through evangelism, discipleship, events, devotionals, and the Archippus School of Ministry."
>
    @php
        $leadEvent = $latestEvents->first();
        $leadPost  = $posts->first();
        $leadUpdate = $newsUpdates->first();
    @endphp

    {{-- ═══════════════════════════════════════════════════
         HERO — Full-bleed cinematic banner
    ═══════════════════════════════════════════════════ --}}
    <section class="home-hero">
        <div class="home-hero-backdrop" style="
            background-image:
                linear-gradient(155deg, rgba(10,14,20,.88) 0%, rgba(10,14,20,.52) 55%, rgba(76,29,17,.75) 100%),
                url('{{ asset('frontend/img/carousel-1.jpg') }}');
        " aria-hidden="true"></div>

        <div class="home-hero-body">
            <div class="surface-frame">
                <div class="home-hero-layout">

                    {{-- Left copy --}}
                    <div class="home-hero-copy">
                        <div class="home-hero-overline">
                            <span class="home-hero-overline-dot"></span>
                            Winning the lost &middot; Building the saints
                        </div>

                        <h1 class="home-hero-headline">
                            A Christ-centred ministry for evangelism, discipleship,
                            and formation across nations.
                        </h1>

                        <p class="home-hero-lead">
                            Projectsave International advances the Gospel through public outreaches,
                            biblical teaching, daily devotionals, strategic partnerships, and
                            the Archippus School of Ministry.
                        </p>

                        <div class="home-hero-ctas">
                            <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="home-btn-primary">
                                <i class="bi bi-arrow-right-circle-fill"></i> Join the mission
                            </a>
                            <a href="{{ route('events.index') }}" class="home-btn-ghost">
                                See upcoming events
                            </a>
                        </div>

                        <div class="home-hero-stats-row">
                            <div class="home-hero-stat">
                                <span class="home-hero-stat-value">8+</span>
                                <span class="home-hero-stat-label">Nations reached</span>
                            </div>
                            <div class="home-hero-stat-divider" aria-hidden="true"></div>
                            <div class="home-hero-stat">
                                <span class="home-hero-stat-value">{{ $latestEvents->count() ?: '3+' }}</span>
                                <span class="home-hero-stat-label">Upcoming events</span>
                            </div>
                            <div class="home-hero-stat-divider" aria-hidden="true"></div>
                            <div class="home-hero-stat">
                                <span class="home-hero-stat-value">ASOM</span>
                                <span class="home-hero-stat-label">School of Ministry</span>
                            </div>
                        </div>
                    </div>

                    {{-- Right live-feed panel --}}
                    <aside class="home-hero-panel" aria-label="Live ministry activity">
                        <div class="home-hero-panel-header">
                            <span class="home-hero-panel-pulse" aria-hidden="true"></span>
                            Live from the ministry
                        </div>

                        <div class="home-hero-feed">
                            <a href="{{ $leadEvent ? route('events.show', $leadEvent) : route('events.index') }}"
                               class="home-hero-feed-item text-decoration-none">
                                <div class="home-hero-feed-icon">
                                    <i class="bi bi-calendar-event-fill"></i>
                                </div>
                                <div class="home-hero-feed-body">
                                    <span class="home-hero-feed-label">Next gathering</span>
                                    <strong>{{ $leadEvent?->title ?? 'See upcoming events' }}</strong>
                                    @if($leadEvent)
                                        <span>{{ optional($leadEvent->start_date)->format('M d, Y') }} &middot; {{ $leadEvent->location }}</span>
                                    @else
                                        <span>View public gatherings, outreach dates and moments.</span>
                                    @endif
                                </div>
                                <i class="bi bi-chevron-right home-hero-feed-arrow"></i>
                            </a>

                            <a href="{{ $leadPost ? route('posts.show', $leadPost->slug) : route('blog.index') }}"
                               class="home-hero-feed-item text-decoration-none">
                                <div class="home-hero-feed-icon">
                                    <i class="bi bi-book-half"></i>
                                </div>
                                <div class="home-hero-feed-body">
                                    <span class="home-hero-feed-label">Latest devotional</span>
                                    <strong>{{ $leadPost?->title ?? 'Read the devotional archive' }}</strong>
                                    @if($leadPost)
                                        <span>{{ optional($leadPost->published_at)->format('M d, Y') }} &middot; {{ $leadPost->author ?? 'Projectsave Team' }}</span>
                                    @else
                                        <span>Scripture-rooted teaching for daily growth.</span>
                                    @endif
                                </div>
                                <i class="bi bi-chevron-right home-hero-feed-arrow"></i>
                            </a>

                            <a href="{{ route('lms.courses.index') }}" class="home-hero-feed-item text-decoration-none">
                                <div class="home-hero-feed-icon">
                                    <i class="bi bi-mortarboard-fill"></i>
                                </div>
                                <div class="home-hero-feed-body">
                                    <span class="home-hero-feed-label">School of ministry</span>
                                    <strong>Structured biblical training — ASOM</strong>
                                    <span>Courses, learning tracks, and ministry formation.</span>
                                </div>
                                <i class="bi bi-chevron-right home-hero-feed-arrow"></i>
                            </a>
                        </div>

                        @if($leadUpdate)
                            <div class="home-hero-panel-footer">
                                <i class="bi bi-broadcast-pin me-2"></i>
                                <span>{{ \Illuminate\Support\Str::limit(strip_tags($leadUpdate->description ?? $leadUpdate->title), 110) }}</span>
                            </div>
                        @endif
                    </aside>

                </div>
            </div>
        </div>

        <div class="home-hero-scroll" aria-hidden="true">
            <span></span><span></span><span></span>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════
         MISSION PILLARS
    ═══════════════════════════════════════════════════ --}}
    <section class="home-pillars-section surface-section">
        <div class="surface-frame">
            <div class="home-section-intro text-center">
                <div class="surface-eyebrow mb-3">What drives the work</div>
                <h2 class="home-section-title">Christ-centred outreach,<br>discipleship &amp; ministry formation</h2>
                <p class="home-section-desc mx-auto">
                    Projectsave exists to preach the Gospel, build believers, and equip people for
                    effective service in their communities and calling.
                </p>
            </div>

            <div class="home-pillars-grid">
                <div class="home-pillar-card">
                    <div class="home-pillar-icon"><i class="bi bi-globe2"></i></div>
                    <h3>Outreach</h3>
                    <p>Taking the Gospel to the nations through field evangelism, community impact,
                       and practical engagement in underserved places.</p>
                    <a href="{{ route('about') }}" class="home-pillar-link">
                        Learn more <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="home-pillar-card home-pillar-card--featured">
                    <div class="home-pillar-icon"><i class="bi bi-people-fill"></i></div>
                    <h3>Discipleship</h3>
                    <p>Teaching, devotionals, and structured follow-up help believers grow in truth,
                       faith, and active service to their communities.</p>
                    <a href="{{ route('blog.index') }}" class="home-pillar-link">
                        Read devotionals <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="home-pillar-card">
                    <div class="home-pillar-icon"><i class="bi bi-award-fill"></i></div>
                    <h3>Training</h3>
                    <p>ASOM provides practical biblical training for people preparing to serve with
                       clarity, conviction, and a confirmed calling.</p>
                    <a href="{{ route('lms.courses.index') }}" class="home-pillar-link">
                        Explore ASOM <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════
         GLOBAL IMPACT — Mission statistics
    ═══════════════════════════════════════════════════ --}}
    <section class="home-impact-section surface-section">
        <div class="surface-frame">
            <div class="home-section-intro text-center">
                <div class="surface-eyebrow mb-3">Global impact</div>
                <h2 class="home-section-title">Why the mission is urgent</h2>
                <p class="home-section-desc mx-auto">
                    Understanding the scale of the task helps sharpen the focus of the work.
                    These are the numbers that drive every outreach, training, and partnership Projectsave is involved in.
                </p>
            </div>

            <div class="home-impact-grid">
                <div class="home-impact-stat">
                    <div class="home-impact-stat-icon"><i class="bi bi-globe-americas"></i></div>
                    <div class="home-impact-stat-value">8.12B+</div>
                    <div class="home-impact-stat-label">World Population</div>
                    <div class="home-impact-stat-desc">Current world population and growing</div>
                </div>

                <div class="home-impact-stat">
                    <div class="home-impact-stat-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="home-impact-stat-value">17,281</div>
                    <div class="home-impact-stat-label">People Groups</div>
                    <div class="home-impact-stat-desc">Total distinct people groups worldwide</div>
                </div>

                <div class="home-impact-stat home-impact-stat--alert">
                    <div class="home-impact-stat-icon"><i class="bi bi-exclamation-circle-fill"></i></div>
                    <div class="home-impact-stat-value">7,246</div>
                    <div class="home-impact-stat-label">Unreached Groups</div>
                    <div class="home-impact-stat-desc">People groups with less than 2% evangelical Christians</div>
                </div>

                <div class="home-impact-stat home-impact-stat--alert">
                    <div class="home-impact-stat-icon"><i class="bi bi-person-x-fill"></i></div>
                    <div class="home-impact-stat-value">41.8%</div>
                    <div class="home-impact-stat-label">UPG Population</div>
                    <div class="home-impact-stat-desc">Of world population &mdash; 3.39 billion people</div>
                </div>

                <div class="home-impact-stat">
                    <div class="home-impact-stat-icon"><i class="bi bi-cross-fill"></i></div>
                    <div class="home-impact-stat-value">2.63B</div>
                    <div class="home-impact-stat-label">Global Christians</div>
                    <div class="home-impact-stat-desc">Total Christians worldwide (all denominations)</div>
                </div>

                <div class="home-impact-stat home-impact-stat--accent">
                    <div class="home-impact-stat-icon"><i class="bi bi-bar-chart-fill"></i></div>
                    <div class="home-impact-stat-value">57,000:1</div>
                    <div class="home-impact-stat-label">Evangelical Ratio</div>
                    <div class="home-impact-stat-desc">Evangelical Christians per unreached people group</div>
                </div>
            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════
         TAKE ACTION — Urgency + Projectsave progress
    ═══════════════════════════════════════════════════ --}}
    <section class="home-urgency-section surface-section">
        <div class="home-urgency-bg" aria-hidden="true"></div>
        <div class="surface-frame">
            <div class="home-urgency-layout">

                {{-- Left: urgency statement -------------------------}}
                <div class="home-urgency-copy">
                    <div class="surface-eyebrow mb-3" style="color: var(--surface-accent, #bc6f2c);">Take action</div>
                    <h2 class="home-urgency-headline">What to do with these numbers</h2>
                    <div class="home-urgency-callout">
                        <div class="home-urgency-callout-number">70,000</div>
                        <p class="home-urgency-callout-text">
                            People die every day and cross into a Christ-less eternity without
                            ever hearing the Gospel of Jesus Christ.
                        </p>
                    </div>
                    <p class="home-urgency-body">
                        These are not statistics to observe — they are a mandate to act.
                        Every outreach, every trained believer, every partnership moves the number.
                        The urgency of now demands a response.
                    </p>
                    <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="home-btn-primary mt-2 d-inline-flex">
                        <i class="bi bi-arrow-right-circle-fill"></i> Answer the call
                    </a>
                </div>

                {{-- Right: Projectsave progress --------------------}}
                <div class="home-urgency-progress">
                    <div class="home-urgency-progress-header">
                        <i class="bi bi-pin-map-fill me-2"></i>
                        Our progress so far
                    </div>

                    <div class="home-urgency-progress-grid">
                        <div class="home-progress-item">
                            <div class="home-progress-item-value">8</div>
                            <div class="home-progress-item-label">Countries reached</div>
                            <div class="home-progress-item-track">
                                <div class="home-progress-item-fill" style="width: 46%"></div>
                            </div>
                        </div>

                        <div class="home-progress-item">
                            <div class="home-progress-item-value">
                                <i class="bi bi-people-fill" style="font-size:1.6rem; font-weight:400;"></i>
                            </div>
                            <div class="home-progress-item-label">Several people groups impacted</div>
                            <div class="home-progress-item-track">
                                <div class="home-progress-item-fill" style="width: 30%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="home-urgency-gospel-row">
                        <div class="home-urgency-gospel-item">
                            <i class="bi bi-broadcast-pin"></i>
                            <div>
                                <strong>The Urgency of Now</strong>
                                <span>70,000 people die daily without hearing Christ</span>
                            </div>
                        </div>
                        <div class="home-urgency-gospel-item">
                            <i class="bi bi-flag-fill"></i>
                            <div>
                                <strong>Our Message</strong>
                                <span>One Gospel for all nations</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════
         EVENTS + DEVOTIONALS — Two-pane grid
    ═══════════════════════════════════════════════════ --}}
    <section class="home-content-section surface-section">
        <div class="surface-frame">
            <div class="row g-5">

                {{-- Events --}}
                <div class="col-lg-6">
                    <header class="home-col-header">
                        <div class="home-col-header-text">
                            <div class="surface-eyebrow mb-2">Upcoming events</div>
                            <h2 class="home-col-title">Gather with us in person or online</h2>
                        </div>
                        <a href="{{ route('events.index') }}" class="home-col-header-link">
                            All events <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </header>

                    <div class="home-event-list">
                        @forelse($latestEvents as $event)
                            <article class="home-event-card">
                                <div class="home-event-date">
                                    <span class="home-event-month">{{ optional($event->start_date)->format('M') }}</span>
                                    <span class="home-event-day">{{ optional($event->start_date)->format('d') }}</span>
                                </div>
                                <div class="home-event-body">
                                    <h3 class="home-event-title">
                                        <a href="{{ route('events.show', $event) }}" class="text-decoration-none">{{ $event->title }}</a>
                                    </h3>
                                    <div class="home-event-meta">
                                        <span><i class="bi bi-geo-alt-fill"></i> {{ $event->location }}</span>
                                        @if($event->start_time)
                                            <span><i class="bi bi-clock-fill"></i> {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}</span>
                                        @endif
                                    </div>
                                    <p class="home-event-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($event->description), 120) }}</p>
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

                {{-- Devotionals --}}
                <div class="col-lg-6">
                    <header class="home-col-header">
                        <div class="home-col-header-text">
                            <div class="surface-eyebrow mb-2">Recent devotionals</div>
                            <h2 class="home-col-title">Daily encouragement rooted in Scripture</h2>
                        </div>
                        <a href="{{ route('blog.index') }}" class="home-col-header-link">
                            All devotionals <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </header>

                    <div class="d-flex flex-column gap-4">
                        @forelse($posts as $post)
                            <article class="home-post-card">
                                <div class="home-post-card-inner">
                                    <div class="home-post-chip">{{ optional($post->published_at)->format('M d, Y') }}</div>
                                    <h3 class="home-post-title">
                                        <a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none">{{ $post->title }}</a>
                                    </h3>
                                    <p class="home-post-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($post->details), 140) }}</p>
                                    @if($post->categories->isNotEmpty())
                                        <div class="home-post-cats">
                                            @foreach($post->categories as $cat)
                                                <span class="home-post-cat-tag">{{ $cat->name }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <x-ui.empty-state
                                title="No devotionals available"
                                message="Published devotionals will appear here as soon as they are available."
                                icon="bi bi-journal-richtext"
                            />
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════
         VIDEO REELS
    ═══════════════════════════════════════════════════ --}}
    @if($videoReels->isNotEmpty())
    <section class="home-reels-section surface-section">
        <div class="surface-frame">
            <div class="home-section-intro text-center">
                <div class="surface-eyebrow mb-3">Field stories</div>
                <h2 class="home-section-title">Watch the ministry in action</h2>
                <p class="home-section-desc mx-auto">
                    Short clips from outreaches, teachings, and testimonies across our ministry network.
                </p>
            </div>

            <div class="home-reels-grid">
                @foreach($videoReels as $video)
                    <article class="home-reel-card" data-video-card>
                        <div class="home-reel-thumb" data-video-frame>
                            <img src="https://img.youtube.com/vi/{{ $video->youtube_id }}/hqdefault.jpg"
                                 alt="{{ $video->title }}" loading="lazy">
                            <div class="home-reel-overlay">
                                <button type="button"
                                        class="home-reel-play-btn"
                                        data-video-embed="{{ $video->youtube_id }}"
                                        aria-label="Play {{ $video->title }}">
                                    <i class="bi bi-play-fill"></i>
                                </button>
                            </div>
                        </div>
                        <div class="home-reel-caption">
                            <h3>{{ $video->title }}</h3>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif


    {{-- ═══════════════════════════════════════════════════
         LATEST UPDATES — Ministry news ticker
    ═══════════════════════════════════════════════════ --}}
    @if($newsUpdates->isNotEmpty())
    <section class="home-updates-section">
        <div class="surface-frame">
            <div class="home-updates-layout">
                <div class="home-updates-label">
                    <i class="bi bi-broadcast-pin me-2"></i> Ministry updates
                </div>
                <div class="home-updates-list">
                    @foreach($newsUpdates->take(4) as $update)
                        <div class="home-update-item">
                            <span class="home-update-date">{{ \Carbon\Carbon::parse($update->date)->format('d M') }}</span>
                            <span class="home-update-title">{{ $update->title }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif


    {{-- ═══════════════════════════════════════════════════
         ASOM SPOTLIGHT
    ═══════════════════════════════════════════════════ --}}
    <section class="home-asom-section surface-section">
        <div class="surface-frame">
            <div class="home-asom-card">
                <div class="home-asom-card-bg" aria-hidden="true"></div>
                <div class="home-asom-content">
                    <div class="home-asom-copy">
                        <div class="home-asom-badge">
                            <i class="bi bi-mortarboard-fill me-2"></i> Archippus School of Ministry
                        </div>
                        <h2 class="home-asom-headline">Structured biblical training for every called believer</h2>
                        <p class="home-asom-desc">
                            ASOM delivers structured online courses, ministry assessments, and certified
                            training tracks for pastors, evangelists, and emerging leaders ready to serve
                            with conviction.
                        </p>
                        <div class="home-asom-ctas">
                            <a href="{{ route('lms.courses.index') }}" class="home-btn-primary">
                                <i class="bi bi-mortarboard-fill"></i> Browse courses
                            </a>
                            <a href="{{ route('login') }}" class="home-btn-glass">
                                Sign in to continue
                            </a>
                        </div>
                    </div>
                    <div class="home-asom-features">
                        <div class="home-asom-feature">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Online, self-paced biblical courses</span>
                        </div>
                        <div class="home-asom-feature">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Ministry assessments and exams</span>
                        </div>
                        <div class="home-asom-feature">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Certificates issued on completion</span>
                        </div>
                        <div class="home-asom-feature">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Open to all called believers</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════
         PARTNERSHIP CTA — Full-bleed dark section
    ═══════════════════════════════════════════════════ --}}
    <section class="home-cta-section surface-section">
        <div class="surface-frame">
            <div class="home-cta-layout">
                <div class="home-cta-copy">
                    <div class="surface-eyebrow mb-3" style="color: rgba(255,255,255,.55);">Join the mission</div>
                    <h2 class="home-cta-headline">Serve, pray, or partner with what God has placed in your hands.</h2>
                    <p class="home-cta-desc">
                        Whether you want to join the prayer force, serve on the ground, contribute a
                        professional skill, or support the ministry financially — there is a clear next
                        step for you.
                    </p>
                </div>

                <div class="home-cta-cards">
                    <a href="{{ route('volunteer.prayer-force') }}" class="home-cta-card">
                        <div class="home-cta-card-icon"><i class="bi bi-heart-fill"></i></div>
                        <strong>Prayer Force</strong>
                        <span>Intercede for the ministry, its leaders and outreach work.</span>
                    </a>
                    <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="home-cta-card">
                        <div class="home-cta-card-icon"><i class="bi bi-person-walking"></i></div>
                        <strong>Ground Force</strong>
                        <span>Serve at events, trips and ministry operations.</span>
                    </a>
                    <a href="{{ route('partners.create', ['type' => 'skilled']) }}" class="home-cta-card">
                        <div class="home-cta-card-icon"><i class="bi bi-briefcase-fill"></i></div>
                        <strong>Skilled Partner</strong>
                        <span>Offer professional skills to strengthen the work.</span>
                    </a>
                    <a href="{{ route('contact.show') }}" class="home-cta-card">
                        <div class="home-cta-card-icon"><i class="bi bi-envelope-fill"></i></div>
                        <strong>Get in touch</strong>
                        <span>Reach the team directly with questions or special interest.</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</x-layouts.app>
