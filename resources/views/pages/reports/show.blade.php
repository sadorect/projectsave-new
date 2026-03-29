<x-layouts.app
    :title="$report->title . ' | Ministry Report'"
    :meta-description="\Illuminate\Support\Str::limit(strip_tags($report->summary), 160)"
>
    <x-ui.public-page-hero
        eyebrow="Ministry Report"
        :title="$report->title"
        :subtitle="optional($report->report_date)->format('F d, Y') . ($report->location ? ' · ' . $report->location : '')"
    >
        <x-slot:actions>
            <a href="{{ route('reports.index') }}" class="surface-button-secondary">
                <i class="bi bi-arrow-left me-2"></i>All reports
            </a>
            <a href="{{ route('partners.create', ['type' => 'prayer']) }}" class="surface-button-primary">
                <i class="bi bi-heart-pulse me-2"></i>Pray with us
            </a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section">
        <div class="surface-frame">
            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    @if($report->featured_image)
                        <div class="event-detail-image mb-4">
                            <img src="{{ asset('storage/' . $report->featured_image) }}" alt="{{ $report->title }}" loading="lazy">
                        </div>
                    @endif

                    <div class="event-detail-card mb-4">
                        <span class="page-section-eyebrow">Report Summary</span>
                        <h2 class="page-section-title">What happened</h2>
                        <p style="font-size:1.05rem;line-height:1.85;color:#374151;margin-bottom:0;">{{ $report->summary }}</p>
                    </div>

                    <div class="event-detail-card mb-4">
                        <span class="page-section-eyebrow">Field Story</span>
                        <h2 class="page-section-title">Detailed outreach report</h2>
                        <div class="public-richtext">
                            {!! $report->details !!}
                        </div>
                    </div>

                    @if(collect($report->gallery ?? [])->isNotEmpty())
                        <div class="event-detail-card mb-4">
                            <span class="page-section-eyebrow">Photo Journal</span>
                            <h2 class="page-section-title">Outreach gallery</h2>
                            <div class="row g-3">
                                @foreach($report->gallery as $imagePath)
                                    <div class="col-md-6">
                                        <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $report->title }} gallery image" class="w-100 rounded-4" style="aspect-ratio:4/3;object-fit:cover;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($report->testimony_title || $report->testimony_quote)
                        <div class="event-detail-card mb-4" style="background:linear-gradient(135deg,rgba(193,18,31,0.06),rgba(15,23,42,0.02));border:1px solid rgba(193,18,31,0.08);">
                            <span class="page-section-eyebrow">Testimony Highlight</span>
                            <h2 class="page-section-title">{{ $report->testimony_title ?: 'A moment worth remembering' }}</h2>
                            <div class="public-richtext" style="font-size:1.05rem;">
                                {!! $report->testimony_quote !!}
                            </div>
                        </div>
                    @endif

                    @if($report->prayer_points)
                        <div class="event-detail-card mb-4">
                            <span class="page-section-eyebrow">Prayer Focus</span>
                            <h2 class="page-section-title">How to pray</h2>
                            <div class="public-richtext">
                                {!! $report->prayer_points !!}
                            </div>
                        </div>
                    @endif

                    @if($report->next_steps)
                        <div class="event-detail-card">
                            <span class="page-section-eyebrow">Next Steps</span>
                            <h2 class="page-section-title">Where the work goes from here</h2>
                            <div class="public-richtext">
                                {!! $report->next_steps !!}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">
                        <div class="event-schedule-card">
                            <span class="page-section-eyebrow mb-3 d-block">Report Snapshot</span>
                            <div class="event-schedule-item">
                                <div class="event-schedule-icon"><i class="bi bi-journal-text"></i></div>
                                <div>
                                    <div class="event-schedule-label">Ministry type</div>
                                    <div class="event-schedule-value">{{ $report->report_type }}</div>
                                </div>
                            </div>
                            <div class="event-schedule-item">
                                <div class="event-schedule-icon"><i class="bi bi-calendar-event-fill"></i></div>
                                <div>
                                    <div class="event-schedule-label">Report date</div>
                                    <div class="event-schedule-value">{{ optional($report->report_date)->format('l, F d, Y') }}</div>
                                </div>
                            </div>
                            @if($report->location)
                                <div class="event-schedule-item">
                                    <div class="event-schedule-icon"><i class="bi bi-geo-alt-fill"></i></div>
                                    <div>
                                        <div class="event-schedule-label">Location</div>
                                        <div class="event-schedule-value">{{ $report->location }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($report->lead_team)
                                <div class="event-schedule-item">
                                    <div class="event-schedule-icon"><i class="bi bi-people-fill"></i></div>
                                    <div>
                                        <div class="event-schedule-label">Lead team</div>
                                        <div class="event-schedule-value">{{ $report->lead_team }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="event-schedule-card">
                            <span class="page-section-eyebrow mb-3 d-block">Impact Numbers</span>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="border rounded-4 p-3">
                                        <div class="small text-uppercase text-muted mb-1">People reached</div>
                                        <div class="fw-bold fs-3">{{ number_format($report->people_reached) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="small text-uppercase text-muted mb-1">Impacted</div>
                                        <div class="fw-bold fs-4">{{ number_format($report->souls_impacted) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="small text-uppercase text-muted mb-1">Volunteers</div>
                                        <div class="fw-bold fs-4">{{ number_format($report->volunteers_count) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="event-schedule-card">
                            <span class="page-section-eyebrow mb-2 d-block">Respond</span>
                            <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:0.5rem;">Stand with this mission</h3>
                            <p style="font-size:0.88rem;color:#6b7280;line-height:1.7;margin-bottom:1.25rem;">Support the ministry through prayer, partnership, volunteering, or sharing this update with someone who will pray.</p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('partners.create', ['type' => 'prayer']) }}" class="surface-button-primary justify-content-center">
                                    <i class="bi bi-heart-pulse-fill me-2"></i>Join Prayer Force
                                </a>
                                <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="surface-button-secondary justify-content-center">
                                    <i class="bi bi-people me-2"></i>Volunteer with us
                                </a>
                            </div>
                        </div>

                        @if($relatedReports->isNotEmpty())
                            <div class="event-schedule-card">
                                <span class="page-section-eyebrow mb-3 d-block">More Reports</span>
                                <div class="d-flex flex-column gap-3">
                                    @foreach($relatedReports as $relatedReport)
                                        <article class="border rounded-4 p-3">
                                            <div class="small text-muted mb-2">{{ $relatedReport->report_type }} · {{ optional($relatedReport->report_date)->format('M d, Y') }}</div>
                                            <h3 style="font-size:1rem;font-weight:700;margin-bottom:0.5rem;">
                                                <a href="{{ route('reports.show', $relatedReport) }}" class="text-decoration-none">{{ $relatedReport->title }}</a>
                                            </h3>
                                            <p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($relatedReport->summary, 100) }}</p>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "Article",
            "headline": "{{ $report->title }}",
            "datePublished": "{{ optional($report->published_at)->toIso8601String() }}",
            "dateModified": "{{ optional($report->updated_at)->toIso8601String() }}",
            "about": "{{ $report->report_type }}",
            "description": "{{ strip_tags($report->summary) }}",
            "image": [
                @if($report->featured_image)
                    "{{ asset('storage/' . $report->featured_image) }}"
                @endif
            ]
        }
    </script>
</x-layouts.app>
