<x-layouts.app>
    <!-- Page Header Start -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>Upcoming Events</h2>
                </div>
                <div class="col-12">
                    <a href="">Home</a>
                    <a href="">Events</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->
    
    
    <!-- Event Start -->
    <div class="event">
        <div class="container">
            <div class="section-header text-center">
                <p>Upcoming Events</p>
                <h2>Be ready for our upcoming missions outreach</h2>
            </div>
            <div class="row">
                @foreach($events as $event)
                    <div class="col-lg-6">
                        <div class="event-item">
                                                    @if($event->image)
                                                        <img src="{{ asset('storage/' . $event->image) }}" alt="Event Image">
                                                    @endif                            <div class="event-content">
                                <div class="event-meta">
                                    <p><i class="fa fa-calendar-alt"></i>{{ \Carbon\Carbon::parse($event->start_date)->format('d-M-Y') }}</p>
                                    <p><i class="far fa-clock"></i>{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}</p>
                                    <p><i class="fa fa-map-marker-alt"></i>{{ $event->location }}</p>
                                </div>
                                <div class="event-text">
                                    <h3>{{ $event->title }}</h3>
                                    <p>{{ $event->description }}</p>
                                    <a class="btn btn-custom" href="">Join Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-12">
                    {{ $events->links() }}
                </div>
            </div>
        </div>
    </div>
    <!-- Event End -->
</x-layouts.app>
