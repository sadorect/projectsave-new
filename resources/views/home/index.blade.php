<x-layouts.app>

        <!-- Carousel Start -->
        <div class="carousel">
            <div class="container-fluid">
                <div class="owl-carousel">
                    <div class="carousel-item">
                        <div class="carousel-img">
                            <img src="{{ asset('frontend/img/carousel-1.jpg') }}" alt="Image">
                        </div>
                        <div class="carousel-text">
                            <h1>Reaching Hearts, Transforming Lives</h1>
                            <p>
                              Taking the message of hope to communities across the world through impactful outreach programs and life-changing encounters with Christ.
                            </p>
                            <div class="carousel-btn">
                                <a class="btn btn-custom" href="">Join Us</a>
                                <a class="btn btn-custom btn-play" data-toggle="modal" data-src="https://www.youtube.com/embed/DWRcNpR6Kdc" data-target="#videoModal">See More </a>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="carousel-img">
                            <img src="{{ asset('frontend/img/carousel-2.jpg') }}" alt="Image">
                        </div>
                        <div class="carousel-text">
                            <h1>Building Strong Spiritual Foundations</h1>
                            <p>
                              Equipping believers with biblical knowledge and discipleship training to become effective ambassadors of Christ in their communities.
                            </p>
                            <div class="carousel-btn">
                              <a class="btn btn-custom" href="">Join Us</a>
                              <a class="btn btn-custom btn-play" data-toggle="modal" data-src="https://www.youtube.com/embed/DWRcNpR6Kdc" data-target="#videoModal">See More </a>
                          </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="carousel-img">
                            <img src="{{ asset('frontend/img/carousel-3.jpg') }}" alt="Image">
                        </div>
                        <div class="carousel-text">
                            <h1>Extending God's Love Through Service</h1>
                            <p>
                              Demonstrating Christ's love through community development, youth empowerment, and humanitarian initiatives that meet both spiritual and physical needs.
                            </p>
                            <div class="carousel-btn">
                              <a class="btn btn-custom" href="">Join Us</a>
                              <a class="btn btn-custom btn-play" data-toggle="modal" data-src="https://www.youtube.com/embed/DWRcNpR6Kdc" data-target="#videoModal">See More </a>
                          </div>
                        </div>
                    </div>
                    
            <div class="carousel-item">
              <div class="carousel-img">
                  <img src="{{ asset('frontend/img/carousel-3.jpg') }}" alt="Image">
              </div>
              <div class="carousel-text">
                  <h1>United in Purpose, Driven by Faith"</h1>
                  <p>
                    Mobilizing believers to fulfill the Great Commission through strategic evangelistic missions and community transformation programs.
                  </p>
                  <div class="carousel-btn">
                    <a class="btn btn-custom" href="">Join Us</a>
                    <a class="btn btn-custom btn-play" data-toggle="modal" data-src="https://www.youtube.com/embed/DWRcNpR6Kdc" data-target="#videoModal">See More </a>
                </div>
              </div>
          </div>
        </div>

            <!-- Carousel End -->

        <!-- Add this right after the Carousel End comment -->
       

        <!-- About Start -->
        <div class="about">
            <div class="container">
                <div class="row align-items-center">
                    <!--div class="col-lg-6">
                        <div class="about-img" data-parallax="scroll" data-image-src="{{ asset('frontend/img/about.jpg') }}"></div>
                    </div-->
                    <div class="col-lg-6">
                        <div class="section-header">
                            <p>Welcome to </p>
                            <h2>Projectsave International</h2>
                        </div>
                        <div class="about-tab">
                            <ul class="nav nav-pills nav-justified">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="pill" href="#tab-content-1">About</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="pill" href="#tab-content-3">Vision</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="pill" href="#tab-content-2">Our Beliefs</a>
                                </li>
                                
                            
                            </ul>

                            <div class="tab-content">
                                <div id="tab-content-1" class="container tab-pane active">
                                  ProjectSave Int'l Ministry, a.k.a HarvestField Ministry is a non-
                                  denominational Christian ministry that is committed to preaching the
                                  gospel of our Lord Jesus Christ to the nations of the world as
                                  commanded in Mark 16:15 and also to build the saints of God with the
                                  revealed truth of God's word (Acts 20:32). Our mission projects are
                                  centred on evangelism and discipleship through the teaching of God's
                                  undiluted word. We have a message to the lost, unreached and to the
                                  dying world- THE GOSPEL.
                                </div>
                                <div id="tab-content-2" class="container tab-pane fade">
                                  <ul class="mission-list">
                                    <li>We believe THE BIBLE is the inspired and only authoritative written Word of God.</li>
                                    
                                    <li>We believe there is ONE GOD, eternally existent in three Persons also known as TRINITY:
                                        <ul>
                                            <li>God The Father</li>
                                            <li>God The Son</li>
                                            <li>God The Holy Spirit</li>
                                        </ul>
                                    </li>
                                    Read more about our beliefs <a href="{{ route('about') }}">here</a>.
                                    </li>
                                </ul>
                                </div>
                                <div id="tab-content-3" class="container tab-pane fade">
                                    <h4>Vision: Winning The Lost Building The Saints</h4>
                                    <p>The world needs Jesus. This is the reason for our vigorous engagement in evangelistic missions to the unreached and unevangelized.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="news-ticker">
                            <div class="section-header">
                                <h4>Latest Updates</h4>
                            </div>
                            @if($newsUpdates && $newsUpdates->count() > 0)
                            <div class="ticker-wrapper">
                                <div class="ticker-content">
                                    @foreach($newsUpdates as $update)
                                        <div class="ticker-item">
                                            <span class="date" style="color: red;">{{ \Carbon\Carbon::parse($update->date)->format('M d, Y') ?? 'Date not available' }}</span>
                                            <p>{{ $update->title }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @else
                             <div class="ticker-wrapper" style="height: 300px; overflow: hidden; position: relative;">

                                <ul class="ticker-items">
                                    <li class="ticker-item">

                                        <span class="date" style="color: red;">Jan 15, 2024</span>
                                        <p>New mission field opened in Southeast Asia reaching unreached people groups</p>
                                    </li>
                                    <li class="ticker-item">

                                        <span class="date" style="color: red;">Jan 10, 2024</span>
                                        <p>Youth discipleship program launches in local communities</p>
                                    </li>
                                    <li class="ticker-item">

                                        <span class="date" style="color: red;">Jan 5, 2024</span>
                                        <p>Successful completion of medical mission in rural areas</p>
                                    </li>
                                    <li class="ticker-item">

                                        <span class="date" style="color: red;">Dec 28, 2023</span>
                                        <p>Annual leadership conference announced for March 2024</p>
                                    </li>
                                    <li class="ticker-item">

                                        <span class="date" style="color: red;">Dec 20, 2023</span>
                                        <p>Christmas outreach program reaches over 1000 families</p>
                                    </li>
                                </ul>
                                <style>
                                    .ticker-items {
                                        animation: scroll 20s linear infinite;
                                        position: absolute;
                                        width: 100%;
                                    }
                                    @keyframes scroll {
                                        0% { transform: translateY(0); }
                                        100% { transform: translateY(-100%); }
                                    }
                                    .ticker-wrapper:hover .ticker-items {
                                        animation-play-state: paused;
                                    }
                                </style>
                            </div>
                            @endif
                        </div>
                    </div> 
                  </div>    
            </div>
        </div>
    </div>
        <!-- About End -->



<!-- Add this before the Mission Statistics Start section -->
<!-- Add this before the Mission Statistics Start section -->


    @if($videoReels && $videoReels->count() > 0)
    
    <div class="video-reel-container">
        <div class="container position-relative">
            <div class="scroll-controls">
                <button class="scroll-btn scroll-left" onclick="scrollVideos('left')">
                    <i class="fa fa-chevron-left"></i>
                </button>
                <button class="scroll-btn scroll-right" onclick="scrollVideos('right')">
                    <i class="fa fa-chevron-right"></i>
                </button>
            </div>
            <div class="video-scroll-wrapper" id="videoWrapper">
                <div class="video-scroll-content">
                    @foreach($videoReels as $video)
                        <div class="video-container">
                            <iframe class="reel-video" 
                                src="https://www.youtube.com/embed/{{ $video->youtube_id }}?enablejsapi=1" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                            </iframe>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @else

                <div class="video-reel-container">
                    <div class="container position-relative">
                        <div class="scroll-controls">
                            <button class="scroll-btn scroll-left" onclick="scrollVideos('left')">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <button class="scroll-btn scroll-right" onclick="scrollVideos('right')">
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="video-scroll-wrapper" id="videoWrapper">
                            <div class="video-scroll-content">
                                <div class="video-container">
                                    <iframe width="240" height="200" src="https://www.youtube.com/embed/IGrlaKNJ4Zs" title="Top Christian Worship Music 2024" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

                                    <iframe width="240" height="200" src="https://www.youtube.com/embed/NVfPBgaJj00" title="The Journey to Okogbo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

                                    <iframe width="240" height="200" src="https://www.youtube.com/embed/A8Oq6HimvU4" title="Outreach to the Guoro Kingdom" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

                                    <iframe width="240" height="200" src="https://www.youtube.com/embed/IGrlaKNJ4Zs" title="Top Christian Worship Music 2024" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

                                    <iframe width="240" height="200" src="https://www.youtube.com/embed/NVfPBgaJj00" title="The Journey to Okogbo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

                                    <iframe width="240" height="200" src="https://www.youtube.com/embed/A8Oq6HimvU4" title="Outreach to the Guoro Kingdom" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
        @endif

<style>
    .video-reel-container {
        width: 100%;
        padding: 20px 0;
        background: #ffffff;
    }
    
    .video-scroll-wrapper {
        width: 100%;
        overflow-x: auto;
        scroll-behavior: smooth;
    }
    
    .video-scroll-content {
        display: flex;
        gap: 20px;
        padding: 0 20px;
    }
    
    .video-container {
        display: flex;
        gap: 20px;
        min-width: max-content;
    }
    
    .scroll-controls {
        position: absolute;
        width: 100%;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        display: flex;
        justify-content: space-between;
        padding: 0 10px;
    }
    
    .scroll-btn {
        background: rgb(255, 0, 0);
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        color: white;
        transition: background 0.3s;
    }
    
    .scroll-btn:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    iframe {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
</style>
    
<script>
    function scrollVideos(direction) {
        const wrapper = document.getElementById('videoWrapper');
        const scrollAmount = 260; // Width of video + gap
        
        if (direction === 'left') {
            wrapper.scrollLeft -= scrollAmount;
        } else {
            wrapper.scrollLeft += scrollAmount;
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const wrapper = document.getElementById('videoWrapper');
        
        // Hide scroll buttons when at the start/end
        wrapper.addEventListener('scroll', function() {
            const leftBtn = document.querySelector('.scroll-left');
            const rightBtn = document.querySelector('.scroll-right');
            
            leftBtn.style.opacity = wrapper.scrollLeft <= 0 ? '0.5' : '1';
            rightBtn.style.opacity = 
                (wrapper.scrollLeft + wrapper.clientWidth >= wrapper.scrollWidth) ? '0.5' : '1';
        });
    });
</script>




       <!-- Mission Statistics Start -->
<div class="service">
  <div class="container">
      <div class="section-header text-center">
          <p>Global Impact</p>
          <h2>Mission Statistics</h2>
      </div>
      <div class="row">
          <div class="col-lg-4 col-md-6">
              <div class="service-item">
                  <div class="service-icon">
                      <i class="fa fa-globe"></i>
                  </div>
                  <div class="service-text">
                      <h3>World Population</h3>
                      <p><span class="facts-plus">8.12</span>B+<br>Current world population and growing</p>
                  </div>
              </div>
          </div>

          <div class="col-lg-4 col-md-6">
              <div class="service-item">
                  <div class="service-icon">
                      <i class="fa fa-users"></i>
                  </div>
                  <div class="service-text">
                      <h3>People Groups</h3>
                      <p><span class="facts-plus">17,281</span><br>Total distinct people groups worldwide</p>
                  </div>
              </div>
          </div>

          <div class="col-lg-4 col-md-6">
              <div class="service-item">
                  <div class="service-icon">
                      <i class="fa fa-heart"></i>
                  </div>
                  <div class="service-text">
                      <h3>Unreached Groups</h3>
                      <p><span class="facts-plus">7,246</span><br>People groups with less than 2% evangelical Christians</p>
                  </div>
              </div>
          </div>

          <div class="col-lg-4 col-md-6">
              <div class="service-item">
                  <div class="service-icon">
                      <i class="fa fa-percentage"></i>
                  </div>
                  <div class="service-text">
                      <h3>UPG Population</h3>
                      <p><span class="facts-plus">41.8</span>%<br>Of world population (3.39 billion people)</p>
                  </div>
              </div>
          </div>

          <div class="col-lg-4 col-md-6">
              <div class="service-item">
                  <div class="service-icon">
                      <i class="fa fa-church"></i>
                  </div>
                  <div class="service-text">
                      <h3>Global Christians</h3>
                      <p><span class="facts-plus">2.63</span>B<br>Total Christians worldwide (all denominations)</p>
                  </div>
              </div>
          </div>

          <div class="col-lg-4 col-md-6">
              <div class="service-item">
                  <div class="service-icon">
                      <i class="fa fa-cross"></i>
                  </div>
                  <div class="service-text">
                      <h3>Evangelical Ratio</h3>
                      <p><span class="facts-plus">57,000</span>:1<br>Evangelical Christians per unreached people group</p>
                  </div>
              </div>
          </div>
      </div>
      <div class="footnote text-muted mt-3">
        <small>Source: <a href="https://www.thetravelingteam.org/stats" target="_blank">https://www.thetravelingteam.org/stats</a></small>
      </div>
  </div>
</div>
<!-- Mission Statistics End -->

<!-- Mission Call to Action Start -->
<div class="service">
  <div class="container">
      <div class="section-header text-center">
          <p>Take Action</p>
          <h2>What To Do With These Information</h2>
      </div>
      <div class="row">
          <div class="col-lg-8 mx-auto">
              <div class="service-item">
                  <div class="service-icon">
                      <i class="fa fa-heartbeat"></i>
                  </div>
                  <div class="service-text">
                      <h3>The Urgency of Now</h3>
                      <p>70,000 people die daily and cross into Christ-less eternity without hearing about Christ.</p>
                  </div>
              </div>
          </div>

          <div class="col-lg-12">
              <div class="row mt-4">
                  <div class="col-lg-4 col-md-6">
                      <div class="service-item">
                          <div class="service-icon">
                              <i class="fa fa-globe-americas"></i>
                          </div>
                          <div class="service-text">
                              <h3>Our Progress</h3>
                              <p><span class="facts-plus">8</span> countries reached so far</p>
                          </div>
                      </div>
                  </div>

                  <div class="col-lg-4 col-md-6">
                      <div class="service-item">
                          <div class="service-icon">
                              <i class="fa fa-users"></i>
                          </div>
                          <div class="service-text">
                              <h3>Our Reach</h3>
                              <p>Several people groups impacted</p>
                          </div>
                      </div>
                  </div>

                  <div class="col-lg-4 col-md-6">
                      <div class="service-item">
                          <div class="service-icon">
                              <i class="fa fa-cross"></i>
                          </div>
                          <div class="service-text">
                              <h3>Our Message</h3>
                              <p>One Gospel for all nations</p>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <div class="col-lg-12 text-center mt-4">
              <div class="donate-btn">
                  <a href="{{ route('contact.show') }}" class="btn btn-custom">Join Our Mission</a>
                  <a href="#" class="btn btn-custom">Support Us</a>
              </div>
          </div>
      </div>
      
  </div>
 
</div>

<!-- Mission Call to Action End -->


      
        
        
        <!-- Donate Start -->
        <div class="donate" data-parallax="scroll" data-image-src="{{ asset('frontend/img/donate.jpeg') }}">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <div class="donate-content">
                            <div class="section-header">
                                <p>Donate Now</p>
                                <h2>Partner with us to reach the nations</h2>
                            </div>
                            <div class="donate-text">
                                <p>
                                    Now that you know what we do, we would be glad to have you join our 1
million star partners that will use their finances to spread the Gospel to
the nations of the earth proclaiming the power in the blood of Jesus to
save, heal and deliver. Do not say "I will wait till I am super-rich before I
make donations", the little in your hands that you can willingly give will
make a whole lot of difference. You can send your money to the field of
souls. Let the Holy Spirit lay it in your heart to partner with us:                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="donate-image">
                            <img src="{{ asset('frontend/img/donate.jpeg') }}" alt="Donate" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Donate End -->
        
        
        <!-- Event Start -->
       
        <div class="event">
            <div class="container">
                <div class="section-header text-center">
                    <p>Upcoming Events</p>
                    <h2>Be ready for our upcoming impactful events</h2>
                </div>
                <div class="row">
                    @foreach($latestEvents as $event)
                        <div class="col-lg-6">
                            <div class="event-item">
                                @if($event->image)
                                <div class="blog-img">
                                    <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}">
                                </div>
                                
                                @endif
                                <div class="event-content">
                                    <div class="event-meta">
                                        <p><i class="fa fa-calendar-alt"></i>{{ $event->date ? $event->date->format('d-M-y') : 'Date TBA' }}</p>
                                        <p><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}  - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}</p>
                                        <p><i class="fa fa-map-marker-alt"></i>{{ $event->location }}</p>
                                    </div>
                                    <div class="event-text">
                                        <a class="" href="{{ route('events.show', $event->id) }}"><h3>{{ $event->title }}</h3></a>
                                        <p>{{ Str::limit($event->description, 120) }}</p>
                                        <a class="btn btn-custom" href="{{ route('events.show', $event->id) }}">Join Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Event End -->
        
        
        
        <!-- Volunteer Start -->
        <div class="volunteer" style="background-color: #fff; padding: 80px 0;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-5">
                        <div class="volunteer-options text-center" style="background-color: #f8f9fa; padding: 30px; border-radius: 15px; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
                            <h3 class="mb-4" style="color: #343a40;">Join Our Team</h3>
                            <div class="d-grid gap-3">
                                <a href="{{ route('volunteer.prayer-force') }}" class="btn btn-custom btn-lg mb-3" style="background: linear-gradient(to right, #FF4C4C, #FF6B6B); border: none; transition: all 0.3s;">
                                    <i class="fas fa-pray mr-2"></i> Prayer Force Team
                                </a>
                                <a href="{{ route('volunteer.financial') }}" class="btn btn-custom btn-lg mb-3" style="background: linear-gradient(to right, #FF4C4C, #FF6B6B); border: none; transition: all 0.3s;">
                                    <i class="fas fa-hand-holding-usd mr-2"></i> Financial Partners
                                </a>
                                <a href="{{ route('volunteer.skilled') }}" class="btn btn-custom btn-lg mb-3" style="background: linear-gradient(to right, #FF4C4C, #FF6B6B); border: none; transition: all 0.3s;">
                                    <i class="fas fa-tools mr-2"></i> Skilled Volunteers
                                </a>
                                <a href="{{ route('volunteer.ground-force') }}" class="btn btn-custom btn-lg" style="background: linear-gradient(to right, #FF4C4C, #FF6B6B); border: none; transition: all 0.3s;">
                                    <i class="fas fa-users mr-2"></i> Ground Force Team
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="volunteer-content" style="padding-left: 30px;">
                            <div class="section-header">
                                <p style="color: #FF4C4C;">Become A Volunteer</p>
                                <h2 style="color: #343a40;">Let's make a difference in the lives of others</h2>
                            </div>
                            <div class="volunteer-text">
                                <p style="color: #6c757d; line-height: 1.8;">
                                    Join our diverse team of volunteers and make a real impact in your community. Whether you can offer prayers, financial support, professional skills, or hands-on assistance, there's a perfect role for you in our mission to create positive change.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Volunteer End -->
        
         
        <!-- Blog Start -->
        <div class="blog">
            <div class="container">
                <div class="section-header text-center">
                    <p>Our Blog</p>
                    <h2>Latest articles directly from our blog</h2>
                </div>
                <div class="row">
                    @foreach($posts as $post)
                    <div class="col-lg-4">
                        <div class="blog-item">
                            @if($post->image)
                            <div class="blog-img">
                                <img src="{{ asset($post->image) }}" alt="{{ $post->title }}">
                            </div>
                            @endif
                            <div class="blog-text">
                                <h3><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h3>
                                <p>
                                    {{ Str::limit($post->excerpt, 120) }}
                                </p>
                            </div>
                           
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Blog End -->
        <script type="text/javascript">
        $(window).scroll(function() {
            $('.about-img').parallax("50%", 0.1);
        });
        </script>
      </x-layouts.app>
