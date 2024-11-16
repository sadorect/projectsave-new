<x-layouts.app>
  <!-- Page Header Start -->
  <div class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2>Detail Page</h2>
            </div>
            <div class="col-12">
                <a href="">Home</a>
                <a href="">Detail</a>
            </div>
        </div>
    </div>
</div>
<!-- Page Header End -->
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ $event->title }}</h5>
                    <a href="{{ route('events.index') }}" class="btn btn-secondary">Back to Events</a>
                </div>
            </div>
            <div class="card-body">
                @if($event->image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" class="img-fluid rounded">
                    </div>
                @endif

                <div class="event-meta mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Date & Time</h6>
                            <p>
                                <i class="bi bi-calendar"></i> 
                                {{ \Carbon\Carbon::parse($event->start_date)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($event->end_date)->format('F d, Y') }}
                            </p>
                            @if($event->start_time)
                                <p>
                                    <i class="bi bi-clock"></i>
                                    {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>Location</h6>
                            <p><i class="bi bi-geo-alt"></i> {{ $event->location }}</p>
                        </div>
                    </div>
                </div>

                <div class="event-description mb-4">
                    <h6>Description</h6>
                    <p>{{ $event->description }}</p>
                </div>

                <div class="event-details">
                    <h6>Details</h6>
                    {!! $event->details !!}
                </div>
            </div>
        </div>
    </div>


<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Event",
    "name": "{{ $event->title }}",
    "startDate": "{{ $event->start_date }}",
    "endDate": "{{ $event->end_date }}",
    "location": {
        "@type": "Place",
        "name": "{{ $event->location }}"
    },
    "description": "{{ $event->description }}"
}
</script>

</x-layouts.app>
