<x-layouts.app>

        <!-- Carousel Start -->
        <div class="carousel">
            <div class="container-fluid">
                <div class="owl-carousel">
                    <div class="carousel-item">
                        <div class="carousel-img">
                            <picture>
                                <source srcset="{{ asset('frontend/img/carousel-1.webp') }}" type="image/webp">
                                <img src="{{ asset('frontend/img/carousel-1.jpg') }}" alt="Image">
                            </picture>
                            
                        </div>
                        <div class="carousel-text">
                            <h2 style="color: white;">Reaching Hearts, Transforming Lives</h2>
                            <p>
                              Taking the message of hope to communities across the world through impactful outreach programs and life-changing encounters with Christ.
                            </p>
                            <div class="carousel-btn">
                                <a class="btn btn-custom" href="#partnership">Join Us</a>
                                <a class="btn btn-custom btn-play"  href="#videoReel">See More </a>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="carousel-img">
                            <picture>
                                <source srcset="{{ asset('frontend/img/carousel-2.webp') }}" type="image/webp">
                                <img src="{{ asset('frontend/img/carousel-2.jpg') }}" alt="Image">
                            </picture>
                        </div>
                        <div class="carousel-text">
                            <h2 style="color: white;">Building Strong Spiritual Foundations</h2>
                            <p>
                              Equipping believers with biblical knowledge and discipleship training to become effective ambassadors of Christ in their communities.
                            </p>
                            <div class="carousel-btn">
                              <a class="btn btn-custom" href="#partnership">Join Us</a>
                              <a class="btn btn-custom btn-play"  href="#videoReel">See More </a>
                          </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="carousel-img">
                            <picture>
                                <source srcset="{{ asset('frontend/img/carousel-3.webp') }}" type="image/webp">
                                <img src="{{ asset('frontend/img/carousel-3.jpg') }}" alt="Image">
                            </picture>
                        </div>
                        <div class="carousel-text">
                            <h2 style="color: white;">Extending God's Love Through Service</h2>
                            <p>
                              Demonstrating Christ's love through community development, youth empowerment, and humanitarian initiatives that meet both spiritual and physical needs.
                            </p>
                            <div class="carousel-btn">
                              <a class="btn btn-custom" href="#partnership">Join Us</a>
                              <a class="btn btn-custom btn-play"  href="#videoReel">See More </a>
                          </div>
                        </div>
                    </div>
                    
            <div class="carousel-item">
              <div class="carousel-img">
                <picture>
                    <source srcset="{{ asset('frontend/img/carousel-3.webp') }}" type="image/webp">
                    <img src="{{ asset('frontend/img/carousel-3.jpg') }}" alt="Image">
                </picture>
              </div>
              <div class="carousel-text">
                  <h2 style="color: white;">United in Purpose, Driven by Faith"</h2>
                  <p>
                    Mobilizing believers to fulfill the Great Commission through strategic evangelistic missions and community transformation programs.
                  </p>
                  <div class="carousel-btn">
                    <a class="btn btn-custom" href="#partnership">Join Us</a>
                    <a class="btn btn-custom btn-play"  href="#videoReel">See More </a>
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
    
    <div id="videoReel" class="video-reel-container">
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
       {!! Cache::remember('mission-stats', now()->addDay(), function() {
        return view('components.mission-stats')->render();
    }) !!}
    



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

         
      </div>
      
  </div>
 
</div>

<!-- Mission Call to Action End -->


        <!-- Volunteer Start -->
<div id="partnership" class="volunteer" style="background-color: #fff; padding: 80px 0;">
    <div class="container">
        <div class="section-header text-center mb-5">
            <p>Join Our Mission</p>
            <h2>Make an Impact with Your Unique Gift</h2>
        </div>
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="partner-card text-center h-100">
                    <div class="icon-wrapper mb-4">
                        <i class="fas fa-pray fa-3x"></i>
                    </div>
                    <h3 class="mb-3">Prayer Force</h3>
                    <p class="mb-4">Soul-winning without intercession is like pouring water into a basket. According to Ezek22:30, God is looking for men that will stand in the gap, take territories, shake kingdoms and rake in a bountiful harvest into His Kingdom through heart-felt prayers.</p>
                    <a href="{{ route('partners.create', ['type' => 'prayer']) }}" class="btn btn-custom">
                        Join Prayer Force
                    </a>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="partner-card text-center h-100">
                    <div class="icon-wrapper mb-4">
                        <i class="fas fa-hands-helping fa-3x"></i>
                    </div>
                    <h3 class="mb-3">Skilled Partners</h3>
                    <p class="mb-4">If you are a doctor, nurse, pharmacist, medical laboratory scientist, microbiologist, psychologist, linguist, graphic artist, musician or maybe there are skills you possess that can glorify God, join our skilled partners team.</p>
                    <a href="{{ route('partners.create', ['type' => 'skilled']) }}" class="btn btn-custom">
                        Partner with Skills
                    </a>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="partner-card text-center h-100">
                    <div class="icon-wrapper mb-4">
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                    <h3 class="mb-3">Ground Force</h3>
                    <p class="mb-4">If you would love to be part of our ministry as a volunteer that will join us in our trips, meetings or use your talent to serve the master, join our ground force team.</p>
                    <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="btn btn-custom">
                        Join Ground Force
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .partner-card {
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .partner-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .icon-wrapper {
            color: #FF4C4C;
            height: 80px;
            width: 80px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,76,76,0.1);
            border-radius: 50%;
        }
        .btn-custom {
            background: linear-gradient(to right, #FF4C4C, #FF6B6B);
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,76,76,0.4);
            color: white;
        }
    </style>
</div>
<!-- Volunteer End -->
        
        
        <!-- Donate Start -->
        <div class="donate" data-parallax="scroll">
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
                            <picture>
                                <source srcset="{{ asset('frontend/img/donate.webp') }}" type="image/webp">
                                <img src="{{ asset('frontend/img/donate.jpeg') }}" alt="Donate" class="img-fluid">
                            </picture>
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
                                    <picture>
                                        <source srcset="{{ asset('storage/' . str_replace(['.jpg','.jpeg','.png'], '.webp', $event->image)) }}" type="image/webp">
                                        <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}">
                                    </picture>                                </div>
                                
                                @endif
                                <div class="event-content">
                                    <div class="event-meta">
                                        <p><i class="fa fa-calendar-alt"></i>{{ $event->date ? $event->date->format('d-M-y') : 'Date TBA' }}</p>
                                        <p><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}  - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}</p>
                                        <p><i class="fa fa-map-marker-alt"></i>{{ $event->location }}</p>
                                    </div>
                                    <div class="event-text">
                                        <a class="" href="{{ route('events.show', $event->slug) }}"><h3>{{ $event->title }}</h3></a>
                                        <p>{{ Str::limit($event->description, 120) }}</p>
                                        <a class="btn btn-custom" href="{{ route('events.show', $event->slug) }}">Join Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Event End -->
        
        
        
      

        
         
        <!-- Blog Start -->
        <div class="blog">
            <div class="container">
                <div class="section-header text-center">
                    <p>Our Blog</p>
                    <h3>Latest articles directly from our Devotional</h3>
                </div>
                <div class="row">
                    @foreach($posts as $post)
                    <div class="col-lg-4">
                        <div class="blog-item">
                            @if($post->image)
                            <div class="blog-img">
                                <picture>
                                    <source srcset="{{ asset(str_replace(['.jpg','.jpeg','.png'], '.webp', $post->image)) }}" type="image/webp">
                                    <img src="{{ asset($post->image) }}" alt="{{ $post->title }}">
                                </picture>
                            </div>
                            @endif
                            <div class="blog-text">
                                <h4><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h4>
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
