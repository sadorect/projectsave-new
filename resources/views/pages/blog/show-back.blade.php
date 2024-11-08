<x-layouts.app>

    
  <!-- Page Header Start -->
  <div class="page-header">
      <div class="container">
          <div class="row">
              <div class="col-12">
                  <h2>Service</h2>
              </div>
              <div class="col-12">
                  <a href="">Home</a>
                  <a href="">Service</a>
              </div>
          </div>
      </div>
  </div>
  <!-- Page Header End -->
  
<div class="single">
  <div class="container">
      <div class="row">
          <div class="col-lg-8">
              <div class="single-content">
                  @if($post->image)
                      <img src="{{ asset('storage/' . $post->image) }}" />
                  @endif
                  <h2>{{ $post->title }}</h2>
                  @if($post->scripture)
                      <div class="scripture-text my-3">
                          <em>{{ $post->scripture }}</em>
                      </div>
                  @endif
                  @if($post->subtitle)
                      <h4>{{ $post->subtitle }}</h4>
                  @endif
                  <div class="text-content my-4">
                      {{ $post->details }}
                  </div>
                  @if($post->action_point)
                      <div class="action-point mt-4">
                          <h5>Action Point:</h5>
                          <p>{{ $post->action_point }}</p>
                      </div>
                  @endif
              </div>
              <div class="single-bio">
                  <div class="single-bio-text">
                      <h3>{{ $post->author }}</h3>
                      <p>Posted on {{ $post->created_at->format('M d, Y') }}</p>
                  </div>
              </div>
          </div>
          <div class="col-lg-4">
              <!-- Sidebar content here -->
          </div>
      </div>
      <div class="col-lg-4">
          <div class="sidebar">
              <div class="sidebar-widget">
                  <div class="search-widget">
                      <form>
                          <input class="form-control" type="text" placeholder="Search Keyword">
                          <button class="btn"><i class="fa fa-search"></i></button>
                      </form>
                  </div>
              </div>

              <div class="sidebar-widget">
                  <h2 class="widget-title">Categories</h2>
                  <div class="category-widget">
                      <ul>
                          @foreach($post->categories as $category)
                              <li><a href="">{{ $category->name }}</a><span>({{ $category->posts->count() }})</span></li>
                          @endforeach
                      </ul>
                  </div>
              </div>

              <div class="sidebar-widget">
                  <h2 class="widget-title">Tags</h2>
                  <div class="tag-widget">
                      @foreach($post->tags as $tag)
                          <a href="">{{ $tag->name }}</a>
                      @endforeach
                  </div>
              </div>

              <div class="sidebar-widget">
                  <h2 class="widget-title">Recent Posts</h2>
                  <div class="recent-post">
                      @foreach($recentPosts as $recentPost)
                          <div class="post-item">
                              <div class="post-text">
                                  <a href="{{ route('posts.show', $recentPost) }}">{{ $recentPost->title }}</a>
                                  <div class="post-meta">
                                      <p>By<a href="">{{ $recentPost->author }}</a></p>
                                      <p>In<a href="">{{ $recentPost->categories->first()->name ?? 'Uncategorized' }}</a></p>
                                  </div>
                              </div>
                          </div>
                      @endforeach
                  </div>
              </div>

              <div class="sidebar-widget">
                  <h2 class="widget-title">Categories</h2>
                  <div class="category-widget">
                      <ul>
                          @foreach($categories as $category)
                              <li>
                                  <a href="">{{ $category->name }}</a>
                                  <span>({{ $category->posts_count }})</span>
                              </li>
                          @endforeach
                      </ul>
                  </div>
                  
              </div>
          </x-layouts.app>
