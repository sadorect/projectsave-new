<x-layouts.app
    title="About Projectsave International"
    meta-description="Learn the story, beliefs, and ministry channels behind Projectsave International."
>
    <section class="surface-section about-hero-section pt-0 pb-3">
        <div class="about-hero-banner" style="--about-hero-image: url('{{ asset('frontend/img/about.jpg') }}');">
            <div class="surface-frame">
                <div class="about-hero-content">
                    <div class="about-hero-copy">
                        <div class="about-hero-kicker">About Projectsave International</div>
                        <h1 class="about-hero-title">A Gospel ministry with a clear mission, a global outlook, and a discipleship mandate.</h1>
                        <p class="about-hero-lead">
                            Projectsave International exists to proclaim Christ, disciple believers through sound biblical teaching, and equip men and women for effective kingdom service in their communities and across nations.
                        </p>

                        <div class="about-hero-actions">
                            <a href="{{ route('events.index') }}" class="surface-button-primary">See upcoming events</a>
                            <a href="{{ route('contact.show') }}" class="surface-button-secondary">Contact the team</a>
                        </div>

                        <div class="about-hero-stats">
                            <div class="about-hero-stat">
                                <strong>Mission</strong>
                                <span>Reach the lost, build believers, and strengthen workers for Gospel ministry.</span>
                            </div>
                            <div class="about-hero-stat">
                                <strong>Vision</strong>
                                <span>See Christ-centered communities transformed through evangelism, discipleship, and service.</span>
                            </div>
                            <div class="about-hero-stat">
                                <strong>Commitment</strong>
                                <span>Present biblical truth with clarity, compassion, and practical ministry action.</span>
                            </div>
                        </div>
                    </div>

                    <aside class="about-hero-panel" aria-label="Ministry distinctives">
                        <div class="public-kicker mb-3 text-white">How we serve</div>
                        <h2 class="h3 mb-3">Projecting the ministry with clarity, credibility, and service.</h2>
                        <p class="mb-4">
                            We present the ministry as a trusted Christian voice for outreach, theological formation, and sustained support for believers, leaders, and communities.
                        </p>

                        <ul class="about-hero-list">
                            <li>
                                <i class="bi bi-globe2"></i>
                                <span>Global-minded mission expression through outreach, partnership, publishing, and digital discipleship.</span>
                            </li>
                            <li>
                                <i class="bi bi-book-half"></i>
                                <span>Scripture-first teaching that helps believers grow in conviction, character, and service.</span>
                            </li>
                            <li>
                                <i class="bi bi-people"></i>
                                <span>Practical equipping for pastors, evangelists, volunteers, and emerging ministry leaders.</span>
                            </li>
                        </ul>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    <section class="surface-section pt-2">
        <div class="surface-frame">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="public-image-frame">
                        <img src="{{ asset('frontend/img/about.jpg') }}" alt="Projectsave ministry outreach" loading="lazy">
                    </div>
                </div>
                <div class="col-lg-6">
                    <x-ui.public-section-heading
                        eyebrow="Our story"
                        title="Winning the lost, building the saints"
                        description="Projectsave International, also known as HarvestField Ministry, is a non-denominational Christian ministry devoted to preaching the Gospel and grounding believers in the revealed truth of God's Word."
                    />

                    <div class="public-richtext">
                        <p>Our mission is centered on evangelism and discipleship. We reach the lost with the message of Jesus Christ and help believers grow through teaching, training, and practical ministry engagement.</p>
                        <p>From field outreaches to ministry development, our aim is to see lives transformed by the Gospel and communities strengthened through Christ-centered service.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="surface-section">
        <div class="surface-frame">
            <x-ui.public-section-heading
                eyebrow="Core beliefs"
                title="What we stand for"
                description="Our ministry convictions are shaped by Scripture, centered on Christ, and expressed through faithful mission."
            />

            <div class="row g-3">
                <div class="col-md-6 col-xl-4">
                    <div class="public-card p-4">
                        <h3 class="h5">The Bible</h3>
                        <p class="mb-0 text-muted">We believe the Bible is the inspired and authoritative written Word of God.</p>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="public-card p-4">
                        <h3 class="h5">The Trinity</h3>
                        <p class="mb-0 text-muted">We believe in one God, eternally existent as Father, Son, and Holy Spirit.</p>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="public-card p-4">
                        <h3 class="h5">Salvation in Christ</h3>
                        <p class="mb-0 text-muted">We believe salvation and cleansing from sin are available through the blood of Jesus Christ.</p>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="public-card p-4">
                        <h3 class="h5">The Holy Spirit</h3>
                        <p class="mb-0 text-muted">We believe in the person, work, gifts, and empowering presence of the Holy Spirit.</p>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="public-card p-4">
                        <h3 class="h5">Mission</h3>
                        <p class="mb-0 text-muted">We believe the Gospel must be taken to unreached and unevangelized peoples with urgency and compassion.</p>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="public-card p-4">
                        <h3 class="h5">Hope and Healing</h3>
                        <p class="mb-0 text-muted">We believe in divine healing, spiritual transformation, and the blessed hope of Christ's return.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="surface-section">
        <div class="surface-frame">
            <x-ui.public-section-heading
                eyebrow="Channels of impact"
                title="How the ministry serves"
                description="Projectsave serves through outreach, training, publishing, partnership, and daily discipleship content."
            />

            <div class="impact-disclosure-group">
                <details class="impact-disclosure" open>
                    <summary>Outreaches</summary>
                    <div class="impact-disclosure-content">
                        We partner with missionaries and local workers to organize evangelistic campaigns, medical missions, welfare efforts, community service, and children-focused ministry across Africa and beyond.
                    </div>
                </details>

                <details class="impact-disclosure">
                    <summary>Teaching and training</summary>
                    <div class="impact-disclosure-content">
                        We organize conferences, seminars, and training programs that equip pastors, evangelists, leaders, and Christian workers to handle Scripture faithfully and serve effectively.
                    </div>
                </details>

                <details class="impact-disclosure">
                    <summary>Mission support and partnership</summary>
                    <div class="impact-disclosure-content">
                        Ministry partnership makes Gospel work possible. We welcome prayer partners, skilled partners, volunteers, and financial supporters who want to help carry the message of Christ to the nations.
                    </div>
                </details>

                <details class="impact-disclosure">
                    <summary>Publishing and media</summary>
                    <div class="impact-disclosure-content">
                        We spread the Gospel through print, digital teaching, devotional publishing, and ministry media that reaches people consistently beyond physical gatherings.
                    </div>
                </details>

                <details class="impact-disclosure">
                    <summary>Daily devotionals</summary>
                    <div class="impact-disclosure-content">
                        Our devotional ministry reaches readers across multiple nations each day, helping believers stay rooted in truth, prayer, and obedience to Christ.
                    </div>
                </details>
            </div>
        </div>
    </section>

    <section class="surface-section">
        <div class="surface-frame">
            <div class="public-card p-4 p-lg-5">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-8">
                        <div class="public-kicker mb-3">Next step</div>
                        <h2 class="mb-3">Move from interest to involvement.</h2>
                        <p class="mb-0 text-muted">Explore events, join the mission, or begin ministry training through ASOM.</p>
                    </div>
                    <div class="col-lg-4">
                        <div class="d-grid gap-2">
                            <a href="{{ route('partners.create', ['type' => 'prayer']) }}" class="surface-button-primary justify-content-center">Become a partner</a>
                            <a href="{{ route('lms.courses.index') }}" class="surface-button-secondary justify-content-center">Explore ASOM</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
