<x-layouts.app
    title="Ministry Reports | Projectsave"
    meta-description="Follow Projectsave ministry reports, field updates, outreaches, testimonies, and impact stories from the mission field."
>
    <x-ui.public-page-hero
        eyebrow="Ministry Reports"
        title="Field updates from outreaches, discipleship, and mercy missions"
        subtitle="Follow what God is doing through Projectsave across campuses, communities, and cities. Each report captures the story, the impact, and how to keep praying with us."
    >
        <x-slot:actions>
            <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="surface-button-primary">
                <i class="bi bi-people-fill me-2"></i>Join an outreach
            </a>
            <a href="{{ route('contact.show') }}" class="surface-button-secondary">Share a testimony</a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section pt-2">
        <div class="surface-frame">
            <div class="row g-3 mb-5">
                <div class="col-md-3">
                    <div class="public-results-card h-100">
                        <div class="public-chip mb-3">Reports</div>
                        <div style="font-size:2rem;font-weight:800;line-height:1;">{{ number_format($stats['reports']) }}</div>
                        <p class="mb-0 mt-2 text-muted">Published reports from ministry activities and outreach follow-ups.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="public-results-card h-100">
                        <div class="public-chip mb-3">People Reached</div>
                        <div style="font-size:2rem;font-weight:800;line-height:1;">{{ number_format($stats['people_reached']) }}</div>
                        <p class="mb-0 mt-2 text-muted">Recorded contacts and attendees reached through our missions.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="public-results-card h-100">
                        <div class="public-chip mb-3">Souls Impacted</div>
                        <div style="font-size:2rem;font-weight:800;line-height:1;">{{ number_format($stats['souls_impacted']) }}</div>
                        <p class="mb-0 mt-2 text-muted">Lives touched through prayer, gospel ministry, and follow-up care.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="public-results-card h-100">
                        <div class="public-chip mb-3">Volunteers</div>
                        <div style="font-size:2rem;font-weight:800;line-height:1;">{{ number_format($stats['volunteers']) }}</div>
                        <p class="mb-0 mt-2 text-muted">Workers mobilized across outreach teams, prayer, and logistics.</p>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ route('reports.index') }}" class="public-results-card mb-5">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label">Search reports</label>
                        <input type="search" name="q" class="form-control" value="{{ $filters['q'] }}" placeholder="Search by title, summary, or location">
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">All</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" @selected($filters['type'] === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select">
                            <option value="">All</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" @selected($filters['year'] === $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label">Location</label>
                        <select name="location" class="form-select">
                            <option value="">All</option>
                            @foreach($locations as $location)
                                <option value="{{ $location }}" @selected($filters['location'] === $location)>{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 d-flex gap-2">
                        <button type="submit" class="surface-button-primary justify-content-center flex-grow-1">Filter</button>
                        <a href="{{ route('reports.index') }}" class="surface-button-secondary justify-content-center">Reset</a>
                    </div>
                </div>
            </form>

            @if($featuredReport)
                <div class="public-results-card mb-5" style="padding:0;overflow:hidden;">
                    <div class="row g-0 align-items-stretch">
                        <div class="col-lg-5" style="min-height:320px;background:linear-gradient(135deg,#1b0305,#2f0b0f);">
                            @if($featuredReport->featured_image)
                                <img
                                    src="{{ asset('storage/' . $featuredReport->featured_image) }}"
                                    alt="{{ $featuredReport->title }}"
                                    class="w-100 h-100"
                                    style="object-fit:cover;min-height:320px;"
                                >
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-journal-richtext" style="font-size:4rem;color:rgba(255,255,255,0.18);"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-7 p-4 p-lg-5 d-flex flex-column justify-content-center">
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="public-chip">Featured report</span>
                                <span class="public-chip">{{ $featuredReport->report_type }}</span>
                                @if($featuredReport->location)
                                    <span class="public-chip">{{ $featuredReport->location }}</span>
                                @endif
                            </div>
                            <h2 class="page-section-title mb-3">{{ $featuredReport->title }}</h2>
                            <p class="text-muted" style="font-size:1.02rem;line-height:1.8;">{{ $featuredReport->summary }}</p>

                            <div class="d-flex flex-wrap gap-3 mt-3 mb-4 text-muted">
                                <span><i class="bi bi-calendar-event me-2 text-brand-700"></i>{{ optional($featuredReport->report_date)->format('F d, Y') }}</span>
                                @if($featuredReport->lead_team)
                                    <span><i class="bi bi-people me-2 text-brand-700"></i>{{ $featuredReport->lead_team }}</span>
                                @endif
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-sm-4">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="small text-uppercase text-muted mb-2">Reached</div>
                                        <div class="fw-bold fs-4">{{ number_format($featuredReport->people_reached) }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="small text-uppercase text-muted mb-2">Impacted</div>
                                        <div class="fw-bold fs-4">{{ number_format($featuredReport->souls_impacted) }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="small text-uppercase text-muted mb-2">Volunteers</div>
                                        <div class="fw-bold fs-4">{{ number_format($featuredReport->volunteers_count) }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-3">
                                <a href="{{ route('reports.show', $featuredReport) }}" class="surface-button-primary">
                                    Read full report <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                                <a href="{{ route('partners.create', ['type' => 'prayer']) }}" class="surface-button-secondary">
                                    Pray with this work
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                <div>
                    <span class="page-section-eyebrow">Report Archive</span>
                    <h2 class="page-section-title mb-0">Latest outreach and ministry updates</h2>
                </div>
                <span class="text-muted small">{{ number_format($reports->total()) }} report{{ $reports->total() === 1 ? '' : 's' }} found</span>
            </div>

            <div class="row g-4">
                @forelse($reports as $report)
                    <div class="col-lg-4 col-md-6">
                        <article class="public-results-card h-100 d-flex flex-column">
                            @if($report->featured_image)
                                <img src="{{ asset('storage/' . $report->featured_image) }}" alt="{{ $report->title }}" class="w-100 rounded-4 mb-4" style="aspect-ratio:16/10;object-fit:cover;">
                            @endif

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="public-chip">{{ $report->report_type }}</span>
                                @if($report->is_featured)
                                    <span class="public-chip">Featured</span>
                                @endif
                            </div>

                            <h3 class="h4 mb-3">
                                <a href="{{ route('reports.show', $report) }}" class="text-decoration-none">{{ $report->title }}</a>
                            </h3>

                            <div class="public-meta-list mb-3">
                                <span><i class="bi bi-calendar-event me-2 text-brand-700"></i>{{ optional($report->report_date)->format('M d, Y') }}</span>
                                <span><i class="bi bi-geo-alt me-2 text-brand-700"></i>{{ $report->location ?: 'Various locations' }}</span>
                            </div>

                            <p class="text-muted mb-4" style="line-height:1.75;">{{ \Illuminate\Support\Str::limit($report->summary, 160) }}</p>

                            <div class="row g-2 mt-auto mb-4">
                                <div class="col-4">
                                    <div class="border rounded-4 p-2 text-center h-100">
                                        <div class="fw-bold">{{ number_format($report->people_reached) }}</div>
                                        <div class="small text-muted">Reached</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded-4 p-2 text-center h-100">
                                        <div class="fw-bold">{{ number_format($report->souls_impacted) }}</div>
                                        <div class="small text-muted">Impacted</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded-4 p-2 text-center h-100">
                                        <div class="fw-bold">{{ number_format($report->volunteers_count) }}</div>
                                        <div class="small text-muted">Volunteers</div>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('reports.show', $report) }}" class="surface-button-secondary justify-content-center">
                                View report
                            </a>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <x-ui.empty-state
                            title="No reports match these filters"
                            message="Try clearing one of the filters or check back after the next ministry update is published."
                            icon="bi bi-journal-richtext"
                        >
                            <x-slot:actions>
                                <a href="{{ route('reports.index') }}" class="surface-button-secondary">View all reports</a>
                            </x-slot:actions>
                        </x-ui.empty-state>
                    </div>
                @endforelse
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $reports->links() }}
            </div>
        </div>
    </section>

    <section style="background:linear-gradient(135deg,#0a0203,#1e080a);padding-block:clamp(3rem,6vw,5rem);">
        <div class="surface-frame text-center">
            <span class="surface-eyebrow" style="color:rgba(193,18,31,0.85);background:rgba(193,18,31,0.1);border:1px solid rgba(193,18,31,0.2);border-radius:999px;padding:0.25rem 0.85rem;display:inline-flex;margin-bottom:1rem;">Stand with the work</span>
            <h2 style="font-size:clamp(1.6rem,3.5vw,2.5rem);font-weight:800;color:#fff;margin-bottom:1rem;letter-spacing:-0.02em;">Pray, serve, and strengthen the next outreach</h2>
            <p style="color:rgba(248,250,252,0.72);max-width:52ch;margin-inline:auto;line-height:1.75;margin-bottom:2rem;">These reports are not just updates. They are invitations to keep standing with the ministry in prayer, partnership, and practical service.</p>
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                <a href="{{ route('partners.create', ['type' => 'prayer']) }}" class="home-btn-primary">Join Prayer Force</a>
                <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="home-btn-ghost">Serve on the field</a>
            </div>
        </div>
    </section>
</x-layouts.app>
