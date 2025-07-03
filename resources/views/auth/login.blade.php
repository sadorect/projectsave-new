<x-layouts.app>
    <!-- Page Header Start -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>Login</h2>
                </div>
                <div class="col-12">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="">Login</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="login-form">
                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="alert alert-success mb-4" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- General Error Messages -->
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="remember_me" name="remember">
                                <label class="custom-control-label" for="remember_me">Remember me</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-custom">Login</button>

                        <div class="d-flex justify-content-between mt-3">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="forgot-password">
                                    Forgot your password?
                                </a>
                            @endif
                            
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="register-link">
                                    Don't have an account? Register here
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
