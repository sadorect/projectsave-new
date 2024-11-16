<x-layouts.app>
    @section('og_title', $post->title)
    @section('og_description', Str::limit(strip_tags($post->details), 200))
    @section('og_image', asset('storage/' . $post->image))
    

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
        
        <!-- Single Post Start-->
        <div class="single">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="single-content">
                            @if($post->image)
                      <img src="{{ asset('storage/' . $post->image) }}" />
                  @endif
                  <h2>{{ $post->title }}</h2>
                  @if($post->bible_text)
                      <div class="bible-text my-3">
                          <em>{{ $post->bible_text }}</em>
                      </div>
                  @endif
                  @if($post->subtitle)
                  <h4>{{ $post->subtitle }}</h4>
              @endif
              <div class="text-content my-4">
                {!! $post->details !!}
              </div>
              @if($post->action_point)
                  <div class="action-point mt-4">
                      <h5>Action Point:</h5>
                      <p>{{ $post->action_point }}</p>
                  </div>
              @endif
          </div>
                        <div class="single-tags">
                            @foreach($post->tags as $tag)
                            <a href="">{{ $tag->name }}</a>
                        @endforeach
                            
                        </div>
                        <div class="single-bio">
                            <div class="single-bio-text">
                                <h3>{{ $post->author }}</h3>
                                <p>Posted on {{ $post->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <div class="social-share mt-4">
                            <div class="fb-share-button" 
                                 data-href="{{ url()->current() }}" 
                                 data-layout="button_count"
                                 data-size="large">
                            </div>
                        </div>
                        <style>
                            .social-share {
    margin: 20px 0;
    padding: 15px;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}

                        </style>

                        <div class="single-related">
                            <h2>Related Post</h2>
                            <div class="owl-carousel related-slider">
                                @foreach($relatedPosts as $relatedPost)
                                <div class="post-item">
                                    <div class="post-text">
                                        <a href="{{ route('posts.show', $relatedPost) }}">{{ $relatedPost->title }}</a>
                                        <div class="post-meta">
                                            <p>By<a href="">{{ $relatedPost->author }}</a></p>
                                            <p>In<a href="">{{ $relatedPost->categories->first()->name ?? 'Uncategorized' }}</a></p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach   
                             </div>
                        </div>

                       
                    </div>

                    <div class="col-lg-4">
                        <div class="sidebar">
                            <div class="sidebar-widget">
                                <div class="search-widget">
                                    <form method="GET" action="{{ route('search') }}">
                                        <input class="form-control" type="text" placeholder="Search Keyword">
                                        <button class="btn"><i class="fa fa-search"></i></button>
                                    </form>
                                </div>
                            </div>

                            <div class="sidebar-widget">
                                <h2 class="widget-title">Recent Post</h2>
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
                                <div class="image-widget">
                                    <a href="#"><img src="{{ asset('frontend/img/blog-1.jpg') }}" alt="Image"></a>
                                </div>
                            </div>


                            <div class="sidebar-widget">
                                <div class="image-widget">
                                    <a href="#"><img src="{{ asset('frontend/img/blog-2.jpg') }}" alt="Image"></a>
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

                            <div class="sidebar-widget">
                                <div class="image-widget">
                                    <a href="#"><img src="{{ asset('frontend/img/blog-3.jpg') }}" alt="Image"></a>
                                </div>
                            </div>

                            <div class="sidebar-widget">
                                <h2 class="widget-title">Tags Cloud</h2>
                                <div class="tag-widget">
                                    <a href="">National</a>
                                    <a href="">International</a>
                                    <a href="">Economics</a>
                                    <a href="">Politics</a>
                                    <a href="">Lifestyle</a>
                                    <a href="">Technology</a>
                                    <a href="">Trades</a>
                                </div>
                            </div>

                            <div class="sidebar-widget">
                                <h2 class="widget-title">Text Widget</h2>
                                <div class="text-widget">
                                    <p>
                                        Stay informed with our latest insights and updates. This widget provides a space for important announcements, featured content highlights, or a brief description of our blog's mission. Follow us for thoughtful analysis and engaging stories across various topics.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <!-- Single Post End-->   


</x-layouts.app>


