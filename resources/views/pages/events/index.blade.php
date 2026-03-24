<x-layouts.app
    title="Projectsave Events"
    meta-description="Browse upcoming Projectsave events, ministry gatherings, and outreach opportunities."
>
    <x-ui.public-page-hero
        eyebrow="Events"
        title="Upcoming events and ministry gatherings"
        subtitle="Stay informed about outreach opportunities, teaching events, and moments where you can gather, serve, and participate."
    >
        <x-slot:actions>
            <a href="{{ route('contact.show') }}" class="surface-button-primary">Ask about an event</a>
            <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="surface-button-secondary">Volunteer with us</a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section pt-2">
        <div class="surface-frame">
            <x-ui.public-section-heading
                eyebrow="Events overview"
                title="Plan your next ministry moment"
                description="Use this page to track where the ministry will be next and how to prepare for it."
            />

            <div class="row g-4 mt-1">
                @forelse($events as $event)
                    <div class="col-lg-6">
                        <article class="public-card overflow-hidden">
                            @if($event->image)
                                <div class="public-image-frame rounded-bottom-0">
                                    <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" loading="lazy">
                                </div>
                            @endif

                            <div class="p-4 p-lg-5">
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="public-chip">{{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}</span>
                                    @if($event->end_date && $event->end_date !== $event->start_date)
                                        <span class="public-chip">to {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}</span>
                                    @endif
                                </div>

                                <h3 class="h4 mb-3">{{ $event->title }}</h3>

                                <div class="public-meta-list mb-3">
                                    <span><i class="bi bi-geo-alt me-2 text-brand-700"></i>{{ $event->location }}</span>
                                    @if($event->start_time)
                                        <span>
                                            <i class="bi bi-clock me-2 text-brand-700"></i>
                                            {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                                            @if($event->end_time)
                                                - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}
                                            @endif
                                        </span>
                                    @endif
                                </div>

                                <p class="mb-4 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($event->description), 170) }}</p>

                                <a href="{{ route('events.show', $event) }}" class="surface-button-secondary">View event details</a>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <x-ui.empty-state
                            title="No events published yet"
                            message="Upcoming events will appear here as soon as the ministry schedule is published."
                            icon="bi bi-calendar-event"
                        />
                    </div>
                @endforelse
            </div>

            <div class="mt-5">
                {{ $events->links() }}
            </div>
        </div>
    </section>
</x-layouts.app>
