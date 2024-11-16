<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Projectsave International</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta property="og:title" content="@yield('og_title', config('app.name'))">
        <meta property="og:description" content="@yield('og_description', 'ProjectSave International Ministry - Winning the losts, building the saints.')">
        <meta property="og:image" content="@yield('og_image', asset('frontend/img/logo.png'))">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:type" content="website">

        <!-- Favicon -->
        <link href="{{ asset('frontend/img/psave_logo.png') }}" rel="icon">

        <!-- Preload critical fonts -->

        <link rel="preload" href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- CSS Libraries -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
        <link href="{{ asset('frontend/lib/flaticon/font/flaticon.css') }}" rel="stylesheet">
<link href="{{ asset('frontend/lib/animate/animate.min.css') }}" rel="stylesheet">
<link href="{{ asset('frontend/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        
        <div id="fb-root"></div>
        <script async defer crossorigin="anonymous" 
                src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0&appId=YOUR_APP_ID" 
                nonce="NONCE_VALUE">
        </script>
    </head>

    <body>
        <!-- Top Bar Start -->
        <div class="top-bar d-none d-md-block">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="top-bar-left">
                            <div class="text">
                                <i class="fa fa-phone-alt"></i>
                                <p>(+234) 07080100893</p>
                            </div>
                            <div class="text">
                                <i class="fa fa-envelope"></i>
                                <p>info@projectsaveng.org</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="top-bar-right">
                            <div class="social">
                                <a href=""><i class="fab fa-twitter"></i></a>
                                <a href=""><i class="fab fa-facebook-f"></i></a>
                                <a href=""><i class="fab fa-linkedin-in"></i></a>
                                <a href=""><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Top Bar End -->

        <!-- Nav Bar Start -->
        <div class="navbar navbar-expand-lg bg-dark navbar-dark">
            <div class="container-fluid">
                <a href="{{ route('home') }}" class="navbar-brand"><img src="{{ asset('frontend/img/psave_logo.png') }}" alt="P'Save Logo" style="height: 40px;"></a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                    <div class="navbar-nav ml-auto">
                        <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                        <a href="{{ route('about') }}" class="nav-item nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                        <a href="{{ route('events.index') }}" class="nav-item nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}">Events</a>
                        <a href="{{ route('blog.index') }}" class="nav-item nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">Blog</a>
                        <a href="{{ route('contact.show') }}" class="nav-item nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}">Contact</a>
                        
                        <form class="form-inline my-2 my-lg-0 ml-4" action="{{ route('search') }}" method="GET">
                            <div class="input-group">
                                <input class="form-control" type="search" name="q" placeholder="Search..." aria-label="Search" value="{{ request('q') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-custom" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Nav Bar End -->