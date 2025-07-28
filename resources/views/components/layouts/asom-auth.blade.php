<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'ASOM Dashboard' }} - {{ config('app.name', 'Projectsave International') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            font-family: 'Figtree', sans-serif;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
        
        .dashboard-nav {
            background: rgba(255,255,255,0.9);
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
            margin-bottom: 1rem;
        }
        
        .user-profile-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .user-menu {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border: none;
        }
        
        .user-menu .dropdown-toggle::after {
            display: none;
        }
        
        .user-menu:hover {
            background: rgba(255,255,255,0.9);
        }
        
        .breadcrumb-nav {
            background: rgba(255,255,255,0.9);
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
            margin-bottom: 1rem;
        }
        
        .breadcrumb-nav .breadcrumb {
            margin-bottom: 0;
        }
        
        .content-wrapper {
            padding: 0 0 2rem 0;
        }
        
        .btn-dashboard {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-dashboard:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
            text-decoration: none;
        }
        
        .btn-secondary-dashboard {
            background: rgba(255,255,255,0.9);
            color: #667eea;
            border: 1px solid rgba(102, 126, 234, 0.3);
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-secondary-dashboard:hover {
            background: white;
            color: #667eea;
            text-decoration: none;
            border-color: #667eea;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 1rem 0;
                text-align: center;
            }
            
            .user-profile-section {
                justify-content: center;
                margin-top: 1rem;
            }
            
            .breadcrumb-nav {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <h1 class="mb-0 me-3">
                            <i class="fas fa-graduation-cap me-2"></i>{{ $pageTitle ?? 'ASOM Learning' }}
                        </h1>
                    </div>
                    @isset($subtitle)
                        <p class="mb-0 opacity-75 mt-1">{{ $subtitle }}</p>
                    @endisset
                </div>
                <div class="col-md-6">
                    <div class="user-profile-section justify-content-md-end">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="dropdown">
                            <button class="btn user-menu dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                                <span>{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down fa-sm"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">{{ Auth::user()->email }}</h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('asom.welcome') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>ASOM Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                    <i class="fas fa-user me-2"></i>Main Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('user.settings') }}">
                                    <i class="fas fa-cog me-2"></i>Account Settings
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.alerts')

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="container">
            {{ $slot }}
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
