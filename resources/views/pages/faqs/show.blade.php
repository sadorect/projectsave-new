<x-layouts.app>
  <!-- Page Header Start -->
  <div class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2>{{ $faq->title }}</h2>
            </div>
            <div class="col-12">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('faqs.list') }}">FAQs</a>
                
            </div>
        </div>
    </div>
</div>
<!-- Page Header End -->

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <article class="blog-post">
              <h2>{{ ucwords($faq->title) }}</h2>
                <div class="post-content">
                    {!! $faq->details !!}
                </div>
            </article>
        

        <!-- Add after the article content -->
        <div class="row mt-4">
          <div class="col-12">
              <div class="d-flex justify-content-between">
                  @if($previous)
                      <a href="{{ route('faqs.show', $previous->slug) }}" class="btn btn-outline-primary">
                          <i class="fas fa-arrow-left"></i> Previous Article
                      </a>
                  @else
                      <div></div>
                  @endif

                  @if($next)
                      <a href="{{ route('faqs.show', $next->slug) }}" class="btn btn-outline-primary">
                          Next Article <i class="fas fa-arrow-right"></i>
                      </a>
                  @endif
              </div>
          </div>
        </div>

</div>

        
          <div class="col-lg-4">
            <!-- Search Widget -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('search') }}" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search knowledge base..." name="q">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        
            <!-- Recent Articles -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Recent Articles</h5>
                </div>
                @php
                    $recentFaqs = App\Models\Faq::where('status', 'published')->latest()->take(5)->get();
                @endphp
                <div class="card-body">
                    @foreach($recentFaqs as $recentFaq)
                        <div class="mb-3">
                            <a href="{{ route('faqs.show', $recentFaq->slug) }}" class="text-decoration-none">
                                <h6 class="mb-1">{{ $recentFaq->title }}</h6>
                                <small class="text-muted">{{ $recentFaq->created_at->format('M d, Y') }}</small>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        
            <!-- Popular Topics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="#" class="btn btn-sm btn-outline-primary">Getting Started</a>
                        <a href="#" class="btn btn-sm btn-outline-primary">Account Setup</a>
                        <a href="#" class="btn btn-sm btn-outline-primary">Troubleshooting</a>
                        <a href="#" class="btn btn-sm btn-outline-primary">Best Practices</a>
                    </div>
                </div>
            </div>
        
            <!-- Help Box -->
            <div class="card bg-light">
                <div class="card-body text-center">
                    <i class="fas fa-headset fa-3x mb-3 text-primary"></i>
                    <h5>Need More Help?</h5>
                    <p>Contact our support team for personalized assistance</p>
                    <a href="{{ route('contact.show') }}" class="btn btn-primary">Contact Support</a>
                </div>
            </div>
        </div>
        
        
    </div>
</div>
</x-layouts.app>
