<x-layouts.app
    :title="$event->title . ' | Projectsave Event'"
    :meta-description="\Illuminate\Support\Str::limit(strip_tags($event->description), 160)"
>
    <x-ui.public-page-hero
        eyebrow="Event details"
        :title="$event->title"
        :subtitle="\Carbon\Carbon::parse($event->start_date)->format('F d, Y') . ($event->location ? ' | ' . $event->location : '')"
    >
        <x-slot:actions>
            <a href="{{ route('events.index') }}" class="surface-button-secondary">Back to events</a>
            <a href="{{ route('contact.show') }}" class="surface-button-primary">Ask a question</a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section pt-2">
        <div class="surface-frame">
            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    @if($event->image)
                        <div class="public-image-frame mb-4">
                            <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" loading="lazy">
                        </div>
                    @endif

                    <div class="public-card p-4 p-lg-5">
                        <x-ui.public-section-heading
                            eyebrow="About this event"
                            title="Overview"
                            :description="$event->description"
                        />

                        <div class="public-richtext mt-4">
                            {!! $event->details !!}
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">
                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="Schedule"
                                title="When and where"
                            />

                            <div class="public-meta-list mt-4">
                                <span><i class="bi bi-calendar-event me-2 text-brand-700"></i>{{ \Carbon\Carbon::parse($event->start_date)->format('F d, Y') }}</span>
                                @if($event->end_date && $event->end_date !== $event->start_date)
                                    <span><i class="bi bi-calendar-range me-2 text-brand-700"></i>Ends {{ \Carbon\Carbon::parse($event->end_date)->format('F d, Y') }}</span>
                                @endif
                                @if($event->start_time)
                                    <span>
                                        <i class="bi bi-clock me-2 text-brand-700"></i>
                                        {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                                        @if($event->end_time)
                                            - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}
                                        @endif
                                    </span>
                                @endif
                                <span><i class="bi bi-geo-alt me-2 text-brand-700"></i>{{ $event->location }}</span>
                            </div>
                        </div>

                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="Take part"
                                title="Prepare for the event"
                                description="Contact the ministry team if you need more information about timing, participation, or support."
                            />

                            <div class="d-grid gap-2 mt-4">
                                <a href="{{ route('contact.show') }}" class="surface-button-primary justify-content-center">Contact the team</a>
                                <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="surface-button-secondary justify-content-center">Volunteer with us</a>
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
