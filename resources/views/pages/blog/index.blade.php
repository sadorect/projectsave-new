<x-layouts.app>
    

        <!-- Page Header Start -->
        <div class="page-header">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2>Our Devotional</h2>
                    </div>
                    <div class="col-12">
                        <a href="">Home</a>
                        <a href="">Devotional</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page Header End -->
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="display-4 mb-3">Daily Bread for Spiritual Growth</h2>
                    <p class="lead">
                        Transformative devotional insights drawn from God's Word to equip believers for effective kingdom service. These Spirit-inspired writings will strengthen your faith, deepen your walk with God, and empower you to fulfill the Great Commission.
                    </p>
                    <div class="mt-4">
                        <span class="badge bg-primary me-2">Daily Devotional</span>
                        <span class="badge bg-primary me-2">Spiritual Growth</span>
                        <span class="badge bg-primary me-2">Kingdom Service</span>
                        <span class="badge bg-primary">Divine Empowerment</span>
                    </div>
                </div>
            </div>
        </div>
        
        
        <!-- Blog Start -->
        <div class="blog">
            <div class="container">
               
                <div class="row">
                    @foreach($posts as $post)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm hover-lift">
                                @if($post->image)
                                    <div class="card-img-top position-relative">
                                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="img-fluid rounded-top">
                                        <div class="overlay-gradient"></div>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-bible text-primary me-2"></i>
                                        <h5 class="card-title mb-0">
                                            <a href="{{ route('posts.show', $post->slug) }}" class="text-dark text-decoration-none hover-text-primary">
                                                {{ $post->title }}
                                            </a>
                                        </h5>
                                    </div>
                                    <p class="card-text text-muted">
                                        {!! Str::limit(strip_tags($post->details), 150) !!}
                                    </p>
                                    
                                    <div class="d-flex align-items-center mt-3">
                                        <img src="{{ asset('frontend/img/psave_logo.png') }}" alt="Author" class="rounded-circle me-2" width="30">
                                        <small class="text-muted">{{ $post->author }}</small>
                                        <span class="mx-2">•</span>
                                        <small class="text-muted">{{ $post->published_at->format('M d, Y') }}</small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-top-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            @foreach($post->categories as $category)
                                                <span class="badge bg-light text-primary me-1">{{ $category->name }}</span>
                                            @endforeach
                                        </div>
                                        <a href="{{ route('posts.show', $post->slug) }}" class="btn btn-outline-primary btn-sm">
                                            Read More <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                

                <div class="row">
                    <div class="col-12">
                        <div class="pagination-wrapper">
                            {{ $posts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>        <!-- Blog End -->

<style>
        .hover-lift {
            transition: transform 0.2s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-5px);
        }
        
        .overlay-gradient {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.1));
        }
        
        .hover-text-primary:hover {
            color: var(--bs-primary) !important;
        }
    </style>      
</x-layouts.app>
