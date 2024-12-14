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
        const calendar = {
            currentDate: new Date(),
            postsData: @json($postDates), // We'll pass this from controller
    
            init() {
                            this.renderCalendar();
                            this.bindEvents();
                            this.initTooltips();
                        },

                        initTooltips() {
                            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                            tooltipTriggerList.map(function (tooltipTriggerEl) {
                                return new bootstrap.Tooltip(tooltipTriggerEl);
                            });
                        },
            renderCalendar() {
                const today = new Date();
                const firstDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
                const lastDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0);
                
                let html = '';
                let date = 1;
                
                for (let i = 0; i < 6; i++) {
                    html += '<tr>';
                    for (let j = 0; j < 7; j++) {
                        if (i === 0 && j < firstDay.getDay()) {
                            html += '<td></td>';
                        } else if (date > lastDay.getDate()) {
                            break;
                        } else {
                            const currentDate = `${this.currentDate.getFullYear()}-${(this.currentDate.getMonth() + 1).toString().padStart(2, '0')}-${date.toString().padStart(2, '0')}`;
                            const hasPost = this.postsData.find(post => post.date === currentDate);
                            const isToday = date === today.getDate() && 
                                           this.currentDate.getMonth() === today.getMonth() && 
                                           this.currentDate.getFullYear() === today.getFullYear();
                            
                            const classes = [];
                            if (hasPost) classes.push('has-post');
                            if (isToday) classes.push('today');
                            
                            html += `<td class="${classes.join(' ')}" 
                                        data-date="${currentDate}"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="${hasPost ? hasPost.title : ''}">${date}</td>`;
                            date++;
                        }
                    }
                    html += '</tr>';
                }
                
                document.querySelector('.calendar-table tbody').innerHTML = html;
                document.querySelector('.current-month').textContent = 
                    this.currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                
                this.initTooltips();
            },
            async fetchPostDates() {
                const response = await fetch(`/blog/dates/${this.currentDate.getFullYear()}/${this.currentDate.getMonth() + 1}`);
                this.postsData = await response.json();
                this.renderCalendar();
                },
    
            bindEvents() {
                document.querySelector('.prev-month').addEventListener('click', (e) => {
                    e.preventDefault();
                    this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                    this.renderCalendar();
                });
    
                document.querySelector('.next-month').addEventListener('click', (e) => {
                    e.preventDefault();
                    this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                    this.renderCalendar();
                });
    
                document.querySelector('.calendar-table').addEventListener('click', (e) => {
                    if (e.target.classList.contains('has-post')) {
                        window.location.href = `/blog?date=${e.target.dataset.date}`;
                    }
                });
            }
        };
        
        console.log(document.querySelectorAll('.has-post').length);
// Initialize the calendar
    
        calendar.init();
    });

    </script>    
    <style>
    .calendar-widget {
        background: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .calendar-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .calendar-table th,
    .calendar-table td {
        text-align: center;
        padding: 8px;
    }
    
    .calendar-table td.has-post {
        background: #FF4C4C;
        color: white;
        border-radius: 50%;
        cursor: pointer;
    }
    
    .calendar-table td.has-post:hover {
        background: #FF6B6B;
    }

    .calendar-table td.today {
    border: 2px solid #FF4C4C;
    font-weight: bold;
}

.calendar-table td.today.has-post {
    border: 2px solid #0000ff;
}

    </style>
    