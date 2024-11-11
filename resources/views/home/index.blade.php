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
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="news-ticker">
                            <div class="section-header">
                                <h4>Latest Updates</h4>
                            </div>
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
                        </div>
                    </div>                </div>    
            </div>
        </div>
    </div>
        <!-- About End -->

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


        
        
        <!-- Facts Start -->
        <!--div class="facts" data-parallax="scroll" data-image-src="{{ asset('frontend/img/facts.jpg') }}">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="facts-item">
                            <i class="flaticon-home"></i>
                            <div class="facts-text">
                                <h3 class="facts-plus" data-toggle="counter-up">150</h3>
                                <p>Countries</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="facts-item">
                            <i class="flaticon-charity"></i>
                            <div class="facts-text">
                                <h3 class="facts-plus" data-toggle="counter-up">400</h3>
                                <p>Volunteers</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="facts-item">
                            <i class="flaticon-kindness"></i>
                            <div class="facts-text">
                                <h3 class="facts-dollar" data-toggle="counter-up">10000</h3>
                                <p>Our Goal</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="facts-item">
                            <i class="flaticon-donation"></i>
                            <div class="facts-text">
                                <h3 class="facts-dollar" data-toggle="counter-up">5000</h3>
                                <p>Raised</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div-->
        <!-- Facts End -->
      
        
        
        <!-- Donate Start -->
        <div class="donate" data-parallax="scroll" data-image-src="{{ asset('frontend/img/donate.jpg') }}">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <div class="donate-content">
                            <div class="section-header">
                                <p>Donate Now</p>
                                <h2>Let's donate to needy people for better lives</h2>
                            </div>
                            <div class="donate-text">
                                <p>
                                    Lorem ipsum dolor sit amet elit. Phasellus nec pretium mi. Curabitur facilisis ornare velit non. Aliquam metus tortor, auctor id gravida, viverra quis sem. Curabitur non nisl nec nisi maximus. Aenean convallis porttitor. Aliquam interdum at lacus non blandit.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="donate-form">
                            <form>
                                <div class="control-group">
                                    <input type="text" class="form-control" placeholder="Name" required="required" />
                                </div>
                                <div class="control-group">
                                    <input type="email" class="form-control" placeholder="Email" required="required" />
                                </div>
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-custom active">
                                        <input type="radio" name="options" checked> $10
                                    </label>
                                    <label class="btn btn-custom">
                                        <input type="radio" name="options"> $20
                                    </label>
                                    <label class="btn btn-custom">
                                        <input type="radio" name="options"> $30
                                    </label>
                                </div>
                                <div>
                                    <button class="btn btn-custom" type="submit">Donate Now</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Donate End -->
        @php
         
         $latestEvents = App\Models\Event::latest()->take(3)->get();

        @endphp
        
        <!-- Event Start -->
        <div class="event">
            <div class="container">
                <div class="section-header text-center">
                    <p>Upcoming Events</p>
                    <h2>Be ready for our upcoming charity events</h2>
                </div>
                <div class="row">
                    @foreach($latestEvents as $event)
                        <div class="col-lg-6">
                            <div class="event-item">
                                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}">
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
        <div class="volunteer" data-parallax="scroll" data-image-src="{{ asset('frontend/img/volunteer.jpg') }}">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-5">
                        <div class="volunteer-form">
                            <form>
                                <div class="control-group">
                                    <input type="text" class="form-control" placeholder="Name" required="required" />
                                </div>
                                <div class="control-group">
                                    <input type="email" class="form-control" placeholder="Email" required="required" />
                                </div>
                                <div class="control-group">
                                    <textarea class="form-control" placeholder="Why you want to become a volunteer?" required="required"></textarea>
                                </div>
                                <div>
                                    <button class="btn btn-custom" type="submit">Become a volunteer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="volunteer-content">
                            <div class="section-header">
                                <p>Become A Volunteer</p>
                                <h2>Let’s make a difference in the lives of others</h2>
                            </div>
                            <div class="volunteer-text">
                                <p>
                                    Lorem ipsum dolor sit amet elit. Phasellus nec pretium mi. Curabitur facilisis ornare velit non. Aliquam metus tortor, auctor id gravida, viverra quis sem. Curabitur non nisl nec nisi maximus. Aenean convallis porttitor. Aliquam interdum at lacus non blandit.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Volunteer End -->
        
             
        
        <!-- Contact Start -->
        <div class="contact">
            <div class="container">
                <div class="section-header text-center">
                    <p>Get In Touch</p>
                    <h2>Contact for any query</h2>
                </div>
                <div class="contact-img">
                    <img src="{{ asset('frontend/img/contact.jpg') }}" alt="Image">
                </div>
                <div class="contact-form">
                        <div id="success"></div>
                        <form name="sentMessage" id="contactForm" novalidate="novalidate">
                            <div class="control-group">
                                <input type="text" class="form-control" id="name" placeholder="Your Name" required="required" data-validation-required-message="Please enter your name" />
                                <p class="help-block text-danger"></p>
                            </div>
                            <div class="control-group">
                                <input type="email" class="form-control" id="email" placeholder="Your Email" required="required" data-validation-required-message="Please enter your email" />
                                <p class="help-block text-danger"></p>
                            </div>
                            <div class="control-group">
                                <input type="text" class="form-control" id="subject" placeholder="Subject" required="required" data-validation-required-message="Please enter a subject" />
                                <p class="help-block text-danger"></p>
                            </div>
                            <div class="control-group">
                                <textarea class="form-control" id="message" placeholder="Message" required="required" data-validation-required-message="Please enter your message"></textarea>
                                <p class="help-block text-danger"></p>
                            </div>
                            <div>
                                <button class="btn btn-custom" type="submit" id="sendMessageButton">Send Message</button>
                            </div>
                        </form>
                    </div>
            </div>
        </div>
        <!-- Contact End -->


        <!-- Blog Start -->
        <div class="blog">
            <div class="container">
                <div class="section-header text-center">
                    <p>Our Blog</p>
                    <h2>Latest news & articles directly from our blog</h2>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="blog-item">
                            <div class="blog-img">
                                <img src="{{ asset('frontend/img/blog-1.jpg') }}" alt="Image">
                            </div>
                            <div class="blog-text">
                                <h3><a href="#">Lorem ipsum dolor sit</a></h3>
                                <p>
                                    Lorem ipsum dolor sit amet elit. Neca pretim miura bitur facili ornare velit non vulpte liqum metus tortor
                                </p>
                            </div>
                            <div class="blog-meta">
                                <p><i class="fa fa-user"></i><a href="">Admin</a></p>
                                <p><i class="fa fa-comments"></i><a href="">15 Comments</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="blog-item">
                            <div class="blog-img">
                                <img src="{{ asset('frontend/img/blog-2.jpg') }}" alt="Image">
                            </div>
                            <div class="blog-text">
                                <h3><a href="#">Lorem ipsum dolor sit</a></h3>
                                <p>
                                    Lorem ipsum dolor sit amet elit. Neca pretim miura bitur facili ornare velit non vulpte liqum metus tortor
                                </p>
                            </div>
                            <div class="blog-meta">
                                <p><i class="fa fa-user"></i><a href="">Admin</a></p>
                                <p><i class="fa fa-comments"></i><a href="">15 Comments</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="blog-item">
                            <div class="blog-img">
                                <img src="{{ asset('frontend/img/blog-3.jpg') }}" alt="Image">
                            </div>
                            <div class="blog-text">
                                <h3><a href="#">Lorem ipsum dolor sit</a></h3>
                                <p>
                                    Lorem ipsum dolor sit amet elit. Neca pretim miura bitur facili ornare velit non vulpte liqum metus tortor
                                </p>
                            </div>
                            <div class="blog-meta">
                                <p><i class="fa fa-user"></i><a href="">Admin</a></p>
                                <p><i class="fa fa-comments"></i><a href="">15 Comments</a></p>
                            </div>
                        </div>
                    </div>
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