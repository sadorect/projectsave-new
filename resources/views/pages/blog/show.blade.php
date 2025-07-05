<x-layouts.app>
    @section('og_title', $post->title)
    @section('og_description', Str::limit(strip_tags($post->details), 200))
    @section('og_image', asset('storage/' . $post->image))
    @section('meta_robots', 'index, follow')
    

        <!-- Page Header Start -->
        <div class="page-header">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2>Devotional</h2>
                    </div>
                    <div class="col-12">
                        <a href="{{ route('home') }}">Home</a>
                        <a href="{{ route('blog.index') }}">Devotional</a>
                        <a href="#">{{ $post->title }}</a>
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
                            <!-- Add after the article content -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    @if($previous)
                        <a href="{{ route('posts.show', $previous->slug) }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Previous Article
                        </a>
                    @else
                        <div></div>
                    @endif
  
                    @if($next)
                        <a href="{{ route('posts.show', $next->slug) }}" class="btn btn-outline-primary">
                            Next Article <i class="fas fa-arrow-right"></i>
                        </a>
                    @endif
                </div>
            </div>
          </div>
                        </div>
                        <div class="single-bio">
                            <div class="single-bio-text">
                                <h3>{{ $post->author }}</h3>
                                <p>Posted on {{ $post->published_at ? $post->published_at->format('M d, Y') : '' }}</p>
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
<!-- Add this in the sidebar section -->
<div class="blog-sidebar">
    <x-blog.calendar :calendar="$calendar" :currentMonth="$currentMonth" :postDates="$postDates" />
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
                                    <div class="categories-pagination">
                                        @for($i = 1; $i <= ceil($categories->count() / 3); $i++)
                                            <button class="btn btn-sm btn-outline-primary mx-1 category-page" data-page="{{ $i }}">{{ $i }}</button>
                                        @endfor
                                    </div>
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



    

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categories = @json($categories);
            const itemsPerPage = 10;
            
            function displayCategories(page) {
                const start = (page - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const categoriesList = document.getElementById('categories-list');
                
                categoriesList.innerHTML = categories.slice(start, end).map(category => `
                    <li class="category-item">
                        <a href="">${category.name}</a>
                        <span>(${category.posts_count})</span>
                    </li>
                `).join('');
            }
        
            document.querySelectorAll('.category-page').forEach(button => {
                button.addEventListener('click', (e) => {
                    const page = e.target.dataset.page;
                    displayCategories(page);
                    
                    // Update active state
                    document.querySelectorAll('.category-page').forEach(btn => 
                        btn.classList.remove('active'));
                    e.target.classList.add('active');
                });
            });
        });
        </script>
        
        <style>
        .categories-pagination {
            margin-top: 15px;
            text-align: center;
        }
        
        .category-page.active {
            background-color: #FF4C4C;
            color: white;
            border-color: #FF4C4C;
        }
        </style>