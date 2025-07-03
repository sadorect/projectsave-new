<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Welcome to ASOM - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Figtree', sans-serif;
        }
        
        .welcome-container {
            padding: 2rem 0;
        }
        
        .welcome-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .welcome-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .welcome-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 600;
        }
        
        .welcome-header p {
            margin: 0.5rem 0 0 0;
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .groups-container {
            padding: 2rem;
        }
        
        .group-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .group-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
        }
        
        .group-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .group-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        
        .group-description {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }
        
        .whatsapp-btn {
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .instructions {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 0 5px 5px 0;
        }
        
        .verification-notice {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 0 5px 5px 0;
        }
        
        .dashboard-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }
        
        .btn-dashboard {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-dashboard:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
            text-decoration: none;
        }
        
        .btn-verify {
            background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-verify:hover {
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="welcome-card">
                        <div class="welcome-header">
                            <h1><i class="fas fa-graduation-cap me-3"></i>Welcome to ASOM!</h1>
                            <p>Archippus School of Ministry - Your Journey in Ministry Begins Here</p>
                        </div>
                        
                        <div class="groups-container">
                            @if(session('verified'))
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Email Verified!</strong> Your account has been successfully verified. You can now join all WhatsApp groups below.
                                </div>
                            @endif

                            @if(!Auth::user()->hasVerifiedEmail())
                                <div class="verification-notice">
                                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Email Verification Required</h5>
                                    <p class="mb-2">
                                        Welcome, <strong>{{ Auth::user()->name }}</strong>! You can see your course groups below, but to join them and access all ASOM features, 
                                        please verify your email address first.
                                    </p>
                                    <a href="{{ route('verification.notice') }}" class="btn-verify">
                                        <i class="fas fa-envelope-circle-check me-2"></i>Verify Email
                                    </a>
                                </div>
                            @else
                                <div class="instructions">
                                    <h5><i class="fas fa-info-circle me-2"></i>Getting Started</h5>
                                    <p class="mb-0">
                                        Welcome, <strong>{{ Auth::user()->name }}</strong>! To begin your ASOM journey, please join the WhatsApp groups for your courses below. 
                                        These groups are where you'll receive course materials, interact with faculty, and connect with fellow students.
                                    </p>
                                </div>
                            @endif
                            
                            <h4 class="mb-4"><i class="fab fa-whatsapp me-2 text-success"></i>Your Course Groups</h4>
                            
                            <div class="row">
                                @foreach($whatsappGroups as $group)
                                <div class="col-md-6 col-lg-4">
                                    @if(Auth::user()->hasVerifiedEmail())
                                        <a href="{{ $group['url'] }}" target="_blank" class="group-card">
                                    @else
                                        <div class="group-card" style="opacity: 0.7; cursor: not-allowed;">
                                    @endif
                                        <div class="group-icon">
                                            <i class="{{ $group['icon'] }}"></i>
                                        </div>
                                        <div class="group-name">{{ $group['name'] }}</div>
                                        <div class="group-description">{{ $group['description'] }}</div>
                                        <span class="whatsapp-btn">
                                            <i class="fab fa-whatsapp"></i>
                                            @if(Auth::user()->hasVerifiedEmail())
                                                Join Group
                                            @else
                                                Verify Email First
                                            @endif
                                        </span>
                                    @if(Auth::user()->hasVerifiedEmail())
                                        </a>
                                    @else
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            
                            <div class="alert alert-info mt-4">
                                <h6><i class="fas fa-lightbulb me-2"></i>Important Notes:</h6>
                                <ul class="mb-0">
                                    <li>Join all relevant course groups for your program</li>
                                    <li>Make sure to introduce yourself when you join</li>
                                    <li>Keep group discussions respectful and on-topic</li>
                                    <li>Check the Info Desk group for general announcements</li>
                                    @if(!Auth::user()->hasVerifiedEmail())
                                        <li><strong>Remember to verify your email to access all features!</strong></li>
                                    @endif
                                </ul>
                            </div>
                            
                            <div class="dashboard-link">
                                <p class="text-muted mb-3">Ready to explore more features?</p>
                                <a href="{{ route('user.dashboard') }}" class="btn-dashboard">
                                    <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
