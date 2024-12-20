<x-layouts.app>
    <!-- Page Header Start -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>Search Results</h2>
                </div>
                <div class="col-12">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="">Search</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <div class="container my-5">
        <div class="search-header text-center mb-5">
            <h3>Search Results for "<span class="text-primary">{{ $query }}</span>"</h3>
            <p class="text-muted">Found {{ $posts->count() + $events->count() }} results</p>
        </div>
        
        @if($posts->count() > 0 || $events->count() > 0)
            @if($posts->count() > 0)
                <div class="section-header mt-5">
                    <p>Blog Posts</p>
                    <h2>Related Articles</h2>
                </div>
                <div class="row">
                    @foreach($posts as $post)
                        <div class="col-lg-6 mb-4">
                            <div class="search-result-card">
                                <div class="row no-gutters">
                                    <div class="col-md-4">
                                        @if($post->image)
                                            <div class="result-img">
                                                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-8">
                                        <div class="result-content">
                                            <span class="result-type"><i class="far fa-newspaper mr-2"></i>Blog Post</span>
                                            <h4><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h4>
                                            <p>{{ Str::limit($post->excerpt, 100) }}</p>
                                            <div class="result-meta">
                                                <span><i class="far fa-calendar mr-1"></i>{{ $post->created_at->format('M d, Y') }}</span>
                                                <span><i class="far fa-user mr-1"></i>{{ $post->author }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($faqs->count() > 0)
            <div class="section-header mt-5">
                <p>FAQs</p>
                <h2>Frequently Asked Questions</h2>
            </div>
            <div class="row">
                @foreach($faqs as $faq)
                    <div class="col-lg-6 mb-4">
                        <div class="search-result-card faq-card">
                            <div class="result-content">
                                <span class="result-type faq-type">
                                    <i class="far fa-question-circle mr-2"></i>FAQ
                                </span>
                                <h4>{{ $faq->question }}</h4>
                                <p>{{ Str::limit($faq->answer, 150) }}</p>
                                <a href="{{ route('faqs.show', $faq->id) }}" class="btn btn-custom btn-sm">Read More</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        

            @if($events->count() > 0)
                <div class="section-header mt-5">
                    <p>Events</p>
                    <h2>Upcoming Activities</h2>
                </div>
                <div class="row">
                    @foreach($events as $event)
                        <div class="col-lg-6 mb-4">
                            <div class="search-result-card event-card">
                                <div class="row no-gutters">
                                    <div class="col-md-4">
                                        @if($event->image)
                                            <div class="result-img">
                                                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-8">
                                        <div class="result-content">
                                            <span class="result-type event-type"><i class="far fa-calendar-alt mr-2"></i>Event</span>
                                            <h4>{{ $event->title }}</h4>
                                            <div class="event-details">
                                                <p><i class="fa fa-map-marker-alt mr-2"></i>{{ $event->location }}</p>
                                                <p><i class="far fa-clock mr-2"></i>{{ date('d M Y', strtotime($event->start_date)) }}</p>
                                            </div>
                                            <a class="btn btn-custom btn-sm" href="{{ route('events.show', $event) }}">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="no-results text-center">
                <i class="fa fa-search fa-3x text-muted mb-3"></i>
                <h4>No results found</h4>
                <p class="text-muted">Try different keywords or browse our sections</p>
                <div class="mt-4">
                    <a href="{{ route('blog.index') }}" class="btn btn-custom mr-2">Browse Blog</a>
                    <a href="{{ route('events.index') }}" class="btn btn-custom">See Events</a>
                </div>
            </div>
        @endif
    </div>

    <style>
        .search-result-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            overflow: hidden;
        }
        
        .search-result-card:hover {
            transform: translateY(-5px);
        }
        
        .result-img {
            height: 100%;
            min-height: 200px;
            overflow: hidden;
        }
        
        .result-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .result-content {
            padding: 20px;
        }
        
        .result-type {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            background: #f8f9fa;
            color: #666;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }
        
        .event-type {
            background: #FF4C4C;
            color: #fff;
        }
        
        .result-content h4 {
            margin-bottom: 10px;
            color: #343a40;
        }
        
        .result-content h4 a {
            color: inherit;
            text-decoration: none;
        }
        
        .result-meta span {
            margin-right: 15px;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .event-details p {
            margin-bottom: 5px;
            color: #6c757d;
        }
        
        .no-results {
            padding: 50px 0;
        }

        .faq-type {
    background: #17a2b8;
    color: #fff;
}

.faq-card .result-content {
    padding: 25px;
}

.faq-card h4 {
    color: #2d3436;
    font-size: 1.1rem;
    margin: 15px 0;
}

    </style>
</x-layouts.app>
