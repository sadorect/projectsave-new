<x-layouts.app
    title="Projectsave Events"
    meta-description="Browse upcoming Projectsave events, ministry gatherings, and outreach opportunities."
>
    <x-ui.public-page-hero
        eyebrow="Events"
        title="Be ready for our upcoming missions outreach"
        subtitle="Track where the ministry will be next. Join an outreach, attend a teaching event, or volunteer to serve. Every event is a kingdom moment."
    >
        <x-slot:actions>
            <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="surface-button-primary">
                <i class="fas fa-hands-helping me-2"></i>Join the team
            </a>
            <a href="{{ route('contact.show') }}" class="surface-button-secondary">Ask about an event</a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section">
        <div class="surface-frame">

            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-5">
                <div>
                    <span class="page-section-eyebrow">Events calendar</span>
                    <h2 class="page-section-title mb-0">Upcoming gatherings</h2>
                </div>
                <a href="{{ route('contact.show') }}" class="surface-button-secondary">
                    <i class="bi bi-question-circle me-2"></i>Questions? Contact us
                </a>
            </div>

            <div class="events-grid">
                @forelse($events as $event)
                    <article class="event-card">
                        <div class="event-card-image">
                            @if($event->image)
                                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" loading="lazy">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background:linear-gradient(135deg,#1a0305,#3d0a0e);">
                                    <i class="bi bi-calendar-event" style="font-size:3rem;color:rgba(255,255,255,0.18);"></i>
                                </div>
                            @endif
                            <div class="event-card-date-badge">
                                <span class="badge-day">{{ \Carbon\Carbon::parse($event->start_date)->format('d') }}</span>
                                {{ \Carbon\Carbon::parse($event->start_date)->format('M Y') }}
                            </div>
                        </div>

                        <div class="event-card-body">
                            <div class="event-card-meta">
                                <span><i class="bi bi-geo-alt-fill"></i>{{ $event->location }}</span>
                                @if($event->start_time)
                                    <span>
                                        <i class="bi bi-clock-fill"></i>
                                        {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                                        @if($event->end_time)–{{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}@endif
                                    </span>
                                @endif
                                @if($event->end_date && $event->end_date !== $event->start_date)
                                    <span>
                                        <i class="bi bi-calendar-range-fill"></i>
                                        Ends {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}
                                    </span>
                                @endif
                            </div>

                            <h3 class="event-card-title">{{ $event->title }}</h3>

                            <p class="event-card-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($event->description), 160) }}</p>

                            <div class="event-card-footer">
                                <a href="{{ route('events.show', $event) }}" class="surface-button-primary" style="font-size:0.85rem;padding:0.55rem 1.1rem;">
                                    View details <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div style="grid-column:1/-1;">
                        <x-ui.empty-state
                            title="No events scheduled yet"
                            message="Upcoming events will appear here as soon as the ministry schedule is published. Check back soon."
                            icon="bi bi-calendar-event"
                        />
                    </div>
                @endforelse
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $events->links() }}
            </div>
        </div>
    </section>

    {{-- CTA Band --}}
    <section style="background:linear-gradient(135deg,#0a0203,#1e080a);padding-block:clamp(3rem,6vw,5rem);">
        <div class="surface-frame text-center">
            <span class="surface-eyebrow" style="color:rgba(193,18,31,0.85);background:rgba(193,18,31,0.1);border:1px solid rgba(193,18,31,0.2);border-radius:999px;padding:0.25rem 0.85rem;display:inline-flex;margin-bottom:1rem;">Get involved</span>
            <h2 style="font-size:clamp(1.6rem,3.5vw,2.5rem);font-weight:800;color:#fff;margin-bottom:1rem;letter-spacing:-0.02em;">Ready to serve at our next event?</h2>
            <p style="color:rgba(248,250,252,0.72);max-width:52ch;margin-inline:auto;line-height:1.75;margin-bottom:2rem;">We welcome volunteers, intercessors, and skilled helpers. Join the ground force and be part of what God is doing through Projectsave.</p>
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="home-btn-primary">
                    <i class="fas fa-hands-helping me-2"></i>Volunteer now
                </a>
                <a href="{{ route('volunteer.prayer-force') }}" class="home-btn-ghost">
                    <i class="fas fa-pray me-2"></i>Join Prayer Force
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
