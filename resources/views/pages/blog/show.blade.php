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
                        <div class="single-related">
                            <h2>Related Post</h2>
                            <div class="owl-carousel related-slider">
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

                        <!--div class="single-comment">
                            <h2>3 Comments</h2>
                            <ul class="comment-list">
                                <li class="comment-item">
                                    <div class="comment-body">
                                        <div class="comment-img">
                                            <img src="img/user.jpg" />
                                        </div>
                                        <div class="comment-text">
                                            <h3><a href="">Josh Dunn</a></h3>
                                            <span>01 Jan 2045 at 12:00pm</span>
                                            <p>
                                                Lorem ipsum dolor sit amet elit. Integer lorem augue purus mollis sapien, non eros leo in nunc. Donec a nulla vel turpis tempor ac vel justo. In hac platea dictumst. 
                                            </p>
                                            <a class="btn" href="">Reply</a>
                                        </div>
                                    </div>
                                </li>
                                <li class="comment-item">
                                    <div class="comment-body">
                                        <div class="comment-img">
                                            <img src="img/user.jpg" />
                                        </div>
                                        <div class="comment-text">
                                            <h3><a href="">Josh Dunn</a></h3>
                                            <p><span>01 Jan 2045 at 12:00pm</span></p>
                                            <p>
                                                Lorem ipsum dolor sit amet elit. Integer lorem augue purus mollis sapien, non eros leo in nunc. Donec a nulla vel turpis tempor ac vel justo. In hac platea dictumst. 
                                            </p>
                                            <a class="btn" href="">Reply</a>
                                        </div>
                                    </div>
                                    <ul class="comment-child">
                                        <li class="comment-item">
                                            <div class="comment-body">
                                                <div class="comment-img">
                                                    <img src="img/user.jpg" />
                                                </div>
                                                <div class="comment-text">
                                                    <h3><a href="">Josh Dunn</a></h3>
                                                    <p><span>01 Jan 2045 at 12:00pm</span></p>
                                                    <p>
                                                        Lorem ipsum dolor sit amet elit. Integer lorem augue purus mollis sapien, non eros leo in nunc. Donec a nulla vel turpis tempor ac vel justo. In hac platea dictumst. 
                                                    </p>
                                                    <a class="btn" href="">Reply</a>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="comment-form">
                            <h2>Leave a comment</h2>
                            <form>
                                <div class="form-group">
                                    <label for="name">Name *</label>
                                    <input type="text" class="form-control" id="name">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" class="form-control" id="email">
                                </div>
                                <div class="form-group">
                                    <label for="website">Website</label>
                                    <input type="url" class="form-control" id="website">
                                </div>

                                <div class="form-group">
                                    <label for="message">Message *</label>
                                    <textarea id="message" cols="30" rows="5" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Post Comment" class="btn btn-custom">
                                </div>
                            </form>
                        </div-->
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
                                        Lorem ipsum dolor sit amet elit. Integer lorem augue purus mollis sapien, non eros leo in nunc. Donec a nulla vel turpis tempor ac vel justo. In hac platea nec eros. Nunc eu enim non turpis id augue.
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