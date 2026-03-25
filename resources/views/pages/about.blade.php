<x-layouts.app
    title="About Projectsave International | Our Story, Beliefs & Mission"
    meta-description="Learn the story, core beliefs, and ministry channels behind Projectsave International — a Christ-centred ministry for evangelism, discipleship, and ministry formation."
>

    {{-- ═══════════════════════════════════════════════════
         HERO — Cinematic (matches homepage style)
    ═══════════════════════════════════════════════════ --}}
    <section class="home-hero about-hero">
        <div class="home-hero-backdrop" style="
            background-image:
                linear-gradient(155deg, rgba(10,14,20,.88) 0%, rgba(10,14,20,.52) 55%, rgba(30,20,9,.78) 100%),
                url('{{ asset('frontend/img/about.jpg') }}');
        " aria-hidden="true"></div>

        <div class="home-hero-body">
            <div class="surface-frame">
                <div class="home-hero-layout">

                    <div class="home-hero-copy">
                        <div class="home-hero-overline">
                            <span class="home-hero-overline-dot"></span>
                            About Projectsave International
                        </div>
                        <h1 class="home-hero-headline">
                            A Gospel ministry with a clear mission, a global outlook, and a discipleship mandate.
                        </h1>
                        <p class="home-hero-lead">
                            Projectsave International exists to proclaim Christ, disciple believers through sound biblical teaching,
                            and equip men and women for effective kingdom service in their communities and across the nations.
                        </p>
                        <div class="home-hero-ctas">
                            <a href="{{ route('events.index') }}" class="home-btn-primary">
                                <i class="bi bi-calendar-event-fill"></i> See upcoming events
                            </a>
                            <a href="{{ route('contact.show') }}" class="home-btn-ghost">
                                Contact the team
                            </a>
                        </div>
                        <div class="home-hero-stats-row">
                            <div class="home-hero-stat">
                                <span class="home-hero-stat-value">8+</span>
                                <span class="home-hero-stat-label">Nations reached</span>
                            </div>
                            <div class="home-hero-stat-divider" aria-hidden="true"></div>
                            <div class="home-hero-stat">
                                <span class="home-hero-stat-value">Non-denom</span>
                                <span class="home-hero-stat-label">Open to all believers</span>
                            </div>
                            <div class="home-hero-stat-divider" aria-hidden="true"></div>
                            <div class="home-hero-stat">
                                <span class="home-hero-stat-value">ASOM</span>
                                <span class="home-hero-stat-label">Ministry training school</span>
                            </div>
                        </div>
                    </div>

                    <aside class="home-hero-panel" aria-label="How we serve">
                        <div class="home-hero-panel-header">
                            <span class="home-hero-panel-pulse" aria-hidden="true"></span>
                            How we serve
                        </div>
                        <div class="home-hero-feed">
                            <div class="home-hero-feed-item">
                                <div class="home-hero-feed-icon"><i class="bi bi-globe2"></i></div>
                                <div class="home-hero-feed-body">
                                    <span class="home-hero-feed-label">Global outreach</span>
                                    <strong>Field evangelism across nations</strong>
                                    <span>Campaigns, missions, and community engagement in underserved places.</span>
                                </div>
                            </div>
                            <div class="home-hero-feed-item">
                                <div class="home-hero-feed-icon"><i class="bi bi-book-half"></i></div>
                                <div class="home-hero-feed-body">
                                    <span class="home-hero-feed-label">Biblical teaching</span>
                                    <strong>Scripture-first discipleship</strong>
                                    <span>Teaching and devotionals to build conviction, character, and calling.</span>
                                </div>
                            </div>
                            <div class="home-hero-feed-item">
                                <div class="home-hero-feed-icon"><i class="bi bi-mortarboard-fill"></i></div>
                                <div class="home-hero-feed-body">
                                    <span class="home-hero-feed-label">Ministry formation</span>
                                    <strong>ASOM — structured training</strong>
                                    <span>Equipping pastors, evangelists, and emerging leaders with practical training.</span>
                                </div>
                            </div>
                        </div>
                    </aside>

                </div>
            </div>
        </div>

        <div class="home-hero-scroll" aria-hidden="true">
            <span></span><span></span><span></span>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════
         OUR STORY
    ═══════════════════════════════════════════════════ --}}
    <section class="surface-section about-story-section">
        <div class="surface-frame">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5">
                    <div class="about-story-image-wrap">
                        <img src="{{ asset('frontend/img/about.jpg') }}" alt="Projectsave International ministry outreach" loading="lazy">
                        <div class="about-story-image-badge">
                            <i class="bi bi-globe-americas"></i>
                            <div>
                                <strong>8+ Nations</strong>
                                <span>Reached with the Gospel</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="surface-eyebrow mb-3">Our story</div>
                    <h2 class="home-section-title mb-4">Winning the lost,<br>building the saints</h2>

                    <blockquote class="about-scripture-quote">
                        <p>"Go into all the world and preach the Gospel to every creature."</p>
                        <cite>&mdash; Mark 16:15</cite>
                    </blockquote>

                    <div class="about-story-text">
                        <p>
                            Projectsave International, also known as HarvestField Ministry, is a non-denominational
                            Christian ministry committed to preaching the Gospel of our Lord Jesus Christ to the nations
                            of the world, and to building the saints of God with the revealed truth of God's Word
                            <em>(Acts 20:32)</em>.
                        </p>
                        <p>
                            Our mission projects are centred on evangelism and discipleship through the teaching of
                            God's undiluted Word. We have a message to the lost, unreached, and the dying world —
                            <strong>the Gospel</strong>.
                        </p>
                        <p>
                            From field outreaches to structured ministry training, our aim is to see lives transformed
                            by the Gospel and communities strengthened through Christ-centred service.
                        </p>
                    </div>

                    <div class="about-story-pillars">
                        <div class="about-story-pillar">
                            <strong>Mission</strong>
                            <span>Reach the lost, build believers, and strengthen workers for Gospel ministry.</span>
                        </div>
                        <div class="about-story-pillar">
                            <strong>Vision</strong>
                            <span>Christ-centred communities transformed through evangelism, discipleship, and service.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════
         CORE BELIEFS — Full 11 beliefs
    ═══════════════════════════════════════════════════ --}}
    <section class="surface-section about-beliefs-section">
        <div class="surface-frame">
            <div class="home-section-intro text-center">
                <div class="surface-eyebrow mb-3">Our beliefs</div>
                <h2 class="home-section-title">What we stand for</h2>
                <p class="home-section-desc mx-auto">
                    Our ministry convictions are shaped by Scripture, centred on Christ, and expressed
                    through faithful mission and obedient service.
                </p>
            </div>

            <div class="about-beliefs-grid">

                <div class="about-belief-card">
                    <div class="about-belief-icon"><i class="bi bi-book-fill"></i></div>
                    <h3>The Bible</h3>
                    <p>We believe the Bible is the inspired and only authoritative written Word of God.</p>
                </div>

                <div class="about-belief-card">
                    <div class="about-belief-icon"><i class="bi bi-triangle-fill"></i></div>
                    <h3>The Trinity</h3>
                    <p>We believe in one God, eternally existent in three persons: Father, Son, and Holy Spirit.</p>
                </div>

                <div class="about-belief-card about-belief-card--featured">
                    <div class="about-belief-icon"><i class="bi bi-cross-fill"></i></div>
                    <h3>Jesus Christ</h3>
                    <p>We believe in the deity of Jesus Christ — His virgin birth, sinless life, miracles, atoning death, and bodily resurrection.</p>
                </div>

                <div class="about-belief-card">
                    <div class="about-belief-icon"><i class="bi bi-wind"></i></div>
                    <h3>The Holy Spirit</h3>
                    <p>We believe in the Holy Spirit as essential for personal salvation and empowerment for Christian life and ministry.</p>
                </div>

                <div class="about-belief-card">
                    <div class="about-belief-icon"><i class="bi bi-heart-fill"></i></div>
                    <h3>Salvation</h3>
                    <p>We believe in cleansing from sin through the precious blood of Jesus Christ shed for our redemption.</p>
                </div>

                <div class="about-belief-card">
                    <div class="about-belief-icon"><i class="bi bi-globe2"></i></div>
                    <h3>Mission</h3>
                    <p>We believe in evangelistic mission to reach the unreached and unsaved with the message of salvation.</p>
                </div>

                <div class="about-belief-card">
                    <div class="about-belief-icon"><i class="bi bi-star-fill"></i></div>
                    <h3>Blessed Hope</h3>
                    <p>We believe in the blessed hope — the rapture of the Church and the visible return of Jesus Christ.</p>
                </div>

                <div class="about-belief-card">
                    <div class="about-belief-icon"><i class="bi bi-shield-fill-check"></i></div>
                    <h3>Divine Healing</h3>
                    <p>The redemptive work of Christ provides healing for the body in response to believing prayer and faith.</p>
                </div>

                <div class="about-belief-card">
                    <div class="about-belief-icon"><i class="bi bi-fire"></i></div>
                    <h3>Holy Spirit Baptism</h3>
                    <p>The baptism in the Holy Spirit is given to believers who ask for it, with the evidence of speaking in tongues.</p>
                </div>

                <div class="about-belief-card">
                    <div class="about-belief-icon"><i class="bi bi-gift-fill"></i></div>
                    <h3>Spiritual Gifts</h3>
                    <p>We believe in the gifts of the Holy Spirit and the five-fold ministry gifts for the building up of the Church.</p>
                </div>

                <div class="about-belief-card">
                    <div class="about-belief-icon"><i class="bi bi-infinity"></i></div>
                    <h3>Eternal Life</h3>
                    <p>We believe in eternal life for the saved and that hell and eternal damnation is the lot of everyone that is unsaved.</p>
                </div>

            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════
         CHANNELS OF IMPACT
    ═══════════════════════════════════════════════════ --}}
    <section class="surface-section about-channels-section">
        <div class="surface-frame">
            <div class="row g-5 align-items-start">
                <div class="col-lg-4">
                    <div class="about-channels-intro">
                        <div class="surface-eyebrow mb-3">What we do</div>
                        <h2 class="home-section-title mb-4">Channels of impact</h2>
                        <p class="home-section-desc" style="margin: 0;">
                            Projectsave serves through outreach, training, publishing, partnership,
                            and daily discipleship content — reaching people wherever they are.
                        </p>
                        <a href="{{ route('contact.show') }}" class="home-btn-primary d-inline-flex mt-4">
                            <i class="bi bi-send-fill"></i> Get in touch
                        </a>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="about-channels-grid">

                        <article class="about-channel-card">
                            <div class="about-channel-number">01</div>
                            <div class="about-channel-icon"><i class="bi bi-broadcast-pin"></i></div>
                            <h3>Outreaches</h3>
                            <p>
                                We partner with missionaries and local workers to organise evangelistic campaigns,
                                medical missions, welfare efforts, community service, and children-focused ministry
                                across Africa and beyond.
                            </p>
                        </article>

                        <article class="about-channel-card">
                            <div class="about-channel-number">02</div>
                            <div class="about-channel-icon"><i class="bi bi-mortarboard-fill"></i></div>
                            <h3>Teaching &amp; Training</h3>
                            <p>
                                We organise conferences, seminars, and training programmes that equip pastors,
                                evangelists, leaders, and Christian workers to handle Scripture faithfully and
                                serve effectively.
                            </p>
                        </article>

                        <article class="about-channel-card">
                            <div class="about-channel-number">03</div>
                            <div class="about-channel-icon"><i class="bi bi-people-fill"></i></div>
                            <h3>Mission Support &amp; Partnership</h3>
                            <p>
                                Ministry partnership makes Gospel work possible. We welcome prayer partners,
                                skilled contributors, volunteers, and financial supporters who want to help
                                carry the message of Christ to the nations.
                            </p>
                        </article>

                        <article class="about-channel-card">
                            <div class="about-channel-number">04</div>
                            <div class="about-channel-icon"><i class="bi bi-newspaper"></i></div>
                            <h3>Publishing the Gospel</h3>
                            <p>
                                We spread the Gospel through print, digital teaching, devotional publishing,
                                and ministry media that reaches people consistently beyond physical gatherings
                                and geographic boundaries.
                            </p>
                        </article>

                        <article class="about-channel-card">
                            <div class="about-channel-number">05</div>
                            <div class="about-channel-icon"><i class="bi bi-journal-heart"></i></div>
                            <h3>Daily Devotionals</h3>
                            <p>
                                Our devotional ministry reaches readers across multiple nations every day,
                                helping believers stay rooted in truth, prayer, and faithful obedience to Christ.
                            </p>
                        </article>

                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════
         CTA — Move from interest to involvement
    ═══════════════════════════════════════════════════ --}}
    <section class="home-cta-section surface-section">
        <div class="surface-frame">
            <div class="home-cta-layout">
                <div class="home-cta-copy">
                    <div class="surface-eyebrow mb-3" style="color: rgba(255,255,255,.55);">Next step</div>
                    <h2 class="home-cta-headline">Move from interest to involvement.</h2>
                    <p class="home-cta-desc">
                        Explore events, become a partner, begin ministry training through ASOM,
                        or simply reach out to the team. There is a clear next step for you.
                    </p>
                </div>

                <div class="home-cta-cards">
                    <a href="{{ route('partners.create', ['type' => 'prayer']) }}" class="home-cta-card">
                        <div class="home-cta-card-icon"><i class="bi bi-heart-fill"></i></div>
                        <strong>Become a partner</strong>
                        <span>Prayer, skill, or ground — find your role.</span>
                    </a>
                    <a href="{{ route('lms.courses.index') }}" class="home-cta-card">
                        <div class="home-cta-card-icon"><i class="bi bi-mortarboard-fill"></i></div>
                        <strong>Explore ASOM</strong>
                        <span>Structured biblical training, online and free.</span>
                    </a>
                    <a href="{{ route('events.index') }}" class="home-cta-card">
                        <div class="home-cta-card-icon"><i class="bi bi-calendar-event-fill"></i></div>
                        <strong>Upcoming events</strong>
                        <span>Attend the next public ministry gathering.</span>
                    </a>
                    <a href="{{ route('contact.show') }}" class="home-cta-card">
                        <div class="home-cta-card-icon"><i class="bi bi-envelope-fill"></i></div>
                        <strong>Get in touch</strong>
                        <span>Reach the team with questions or interest.</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</x-layouts.app>
