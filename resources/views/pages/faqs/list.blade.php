<x-layouts.app>
  <!-- Page Header Start -->
  <div class="page-header">
      <div class="container">
          <div class="row">
              <div class="col-12">
                  <h2>Knowledge Base</h2>
              </div>
              <div class="col-12">
                  <a href="{{ route('home') }}">Home</a>
                  <a href="#">Knowledge Base</a>
              </div>
          </div>
      </div>
  </div>
  <!-- Page Header End -->
  <div class="container">
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8 text-center">
            <h2 class="display-4 mb-3">Biblical Answers for Today's Questions</h2>
            <p class="lead">
                Discover clear, Scripture-based insights on contemporary Christian topics, doctrinal questions, and spiritual growth. Our knowledge base addresses common misconceptions and provides biblical truth to strengthen your faith and understanding.
            </p>
            <div class="mt-4">
                <span class="badge bg-primary me-2">Doctrine</span>
                <span class="badge bg-primary me-2">Christian Living</span>
                <span class="badge bg-primary me-2">Spiritual Growth</span>
                <span class="badge bg-primary">Biblical Truth</span>
            </div>
        </div>
    </div>
</div>

  <div class="container py-5">
      <div class="row">
          <div class="col-lg-8">
              <div class="row">
                  @foreach($faqs as $faq)
                      <div class="col-md-6 mb-4">
                          <div class="card h-100 shadow-sm hover-shadow">
                              <div class="card-body">
                                  <div class="d-flex align-items-center mb-3">
                                      <i class="fas fa-book-open text-primary me-2"></i>
                                      <h5 class="card-title mb-0">
                                          <a href="{{ route('faqs.show', $faq->slug) }}" class="text-dark text-decoration-none">
                                              {{ $faq->title }}
                                          </a>
                                      </h5>
                                  </div>
                                  <p class="card-text text-muted">
                                      {!! Str::limit(strip_tags($faq->details), 150) !!}
                                  </p>
                              </div>
                              <div class="card-footer bg-transparent border-top-0">
                                  <div class="d-flex justify-content-between align-items-center">
                                      <a href="{{ route('faqs.show', $faq->slug) }}" class="btn btn-outline-primary btn-sm">
                                          Read More <i class="fas fa-arrow-right ms-1"></i>
                                      </a>
                                      <small class="text-muted">{{ $faq->created_at->format('M d, Y') }}</small>
                                  </div>
                              </div>
                          </div>
                      </div>
                  @endforeach
              </div>

              <div class="d-flex justify-content-center mt-4">
                  {{ $faqs->links() }}
              </div>
          </div>

          <div class="col-lg-4">
              <div class="card shadow-sm mb-4">
                  <div class="card-body">
                      <h5 class="card-title">Quick Search</h5>
                      <div class="input-group">
                          <input type="text" class="form-control" placeholder="Search articles...">
                          <button class="btn btn-primary" type="button">
                              <i class="fas fa-search"></i>
                          </button>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</x-layouts.app>
