<x-layouts.app
    :title="$event->title . ' | Projectsave Event'"
    :meta-description="\Illuminate\Support\Str::limit(strip_tags($event->description), 160)"
>
    <x-ui.public-page-hero
        eyebrow="Event details"
        :title="$event->title"
        :subtitle="\Carbon\Carbon::parse($event->start_date)->format('F d, Y') . ($event->location ? ' · ' . $event->location : '')"
    >
        <x-slot:actions>
            <a href="{{ route('events.index') }}" class="surface-button-secondary">
                <i class="bi bi-arrow-left me-2"></i>All events
            </a>
            <a href="{{ route('contact.show') }}" class="surface-button-primary">
                <i class="bi bi-chat-dots me-2"></i>Ask a question
            </a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section">
        <div class="surface-frame">
            <div class="row g-4 align-items-start">

                {{-- Main content --}}
                <div class="col-lg-8">
                    @if($event->image)
                        <div class="event-detail-image">
                            <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" loading="lazy">
                        </div>
                    @endif

                    <div class="event-detail-card">
                        <span class="page-section-eyebrow">About this event</span>
                        <h2 class="page-section-title">Event overview</h2>

                        @if($event->description)
                            <p style="font-size:1.05rem;line-height:1.8;color:#374151;margin-bottom:1.5rem;">{{ $event->description }}</p>
                        @endif

                        @if($event->details)
                            <div class="public-richtext">
                                {!! $event->details !!}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">

                        {{-- Schedule card --}}
                        <div class="event-schedule-card">
                            <span class="page-section-eyebrow mb-3 d-block">Schedule</span>
                            <h3 style="font-size:1.1rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem;">When &amp; where</h3>

                            <div>
                                <div class="event-schedule-item">
                                    <div class="event-schedule-icon"><i class="bi bi-calendar-event-fill"></i></div>
                                    <div>
                                        <div class="event-schedule-label">Start date</div>
                                        <div class="event-schedule-value">{{ \Carbon\Carbon::parse($event->start_date)->format('l, F d, Y') }}</div>
                                    </div>
                                </div>

                                @if($event->end_date && $event->end_date !== $event->start_date)
                                    <div class="event-schedule-item">
                                        <div class="event-schedule-icon"><i class="bi bi-calendar-range-fill"></i></div>
                                        <div>
                                            <div class="event-schedule-label">End date</div>
                                            <div class="event-schedule-value">{{ \Carbon\Carbon::parse($event->end_date)->format('l, F d, Y') }}</div>
                                        </div>
                                    </div>
                                @endif

                                @if($event->start_time)
                                    <div class="event-schedule-item">
                                        <div class="event-schedule-icon"><i class="bi bi-clock-fill"></i></div>
                                        <div>
                                            <div class="event-schedule-label">Time</div>
                                            <div class="event-schedule-value">
                                                {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                                                @if($event->end_time)
                                                    &ndash; {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="event-schedule-item">
                                    <div class="event-schedule-icon"><i class="bi bi-geo-alt-fill"></i></div>
                                    <div>
                                        <div class="event-schedule-label">Location</div>
                                        <div class="event-schedule-value">{{ $event->location }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CTA card --}}
                        <div class="event-schedule-card">
                            <span class="page-section-eyebrow mb-2 d-block">Take part</span>
                            <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:0.5rem;">Prepare for this event</h3>
                            <p style="font-size:0.88rem;color:#6b7280;line-height:1.7;margin-bottom:1.25rem;">Contact the ministry for timing, directions, or volunteer coordination details.</p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('contact.show') }}" class="surface-button-primary justify-content-center">
                                    <i class="bi bi-envelope-fill me-2"></i>Contact the team
                                </a>
                                <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="surface-button-secondary justify-content-center">
                                    <i class="bi bi-person-check me-2"></i>Volunteer with us
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "Event",
            "name": "{{ $event->title }}",
            "startDate": "{{ $event->start_date }}",
            "endDate": "{{ $event->end_date }}",
            "location": {
                "@@type": "Place",
                "name": "{{ $event->location }}"
            },
            "description": "{{ strip_tags($event->description) }}"
        }
    </script>
</x-layouts.app>
