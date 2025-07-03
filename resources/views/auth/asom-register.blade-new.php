<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ASOM Registration - {{ config('app.name', 'Laravel') }}</title>

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
        
        .register-container {
            padding: 2rem 0;
        }
        
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .register-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .register-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
        }
        
        .register-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }
        
        .register-form {
            padding: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        .btn-register {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 500;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-register:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="register-card">
                        <div class="register-header">
                            <h1><i class="fas fa-graduation-cap me-3"></i>Join ASOM</h1>
                            <p>African School of Ministry Registration</p>
                        </div>
                        
                        <div class="register-form">
                            <form method="POST" action="{{ route('asom.register') }}">
                                @csrf

                                <!-- Name -->
                                <div class="form-group">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user me-2"></i>Full Name
                                    </label>
                                    <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
                                    @error('name')
                                        <div class="alert-error mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email Address -->
                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>Email Address
                                    </label>
                                    <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
                                    @error('email')
                                        <div class="alert-error mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="form-group">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Password
                                    </label>
                                    <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" />
                                    @error('password')
                                        <div class="alert-error mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Confirm Password
                                    </label>
                                    <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" />
                                    @error('password_confirmation')
                                        <div class="alert-error mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn-register">
                                    <i class="fas fa-user-plus me-2"></i>Register for ASOM
                                </button>
                            </form>

                            <div class="login-link">
                                <p class="text-muted">Already have an account?</p>
                                <a href="{{ route('login') }}" class="text-decoration-none">
                                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
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
