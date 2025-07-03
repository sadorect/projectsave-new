<x-layouts.app>
  <!-- Page Header Start -->
  <div class="page-header">
      <div class="container">
          <div class="row">
              <div class="col-12">
                  <h2>ASOM ENROLMENT</h2>
              </div>
              <div class="col-12">
                  <a href="{{ route('home') }}">Home</a>
                  <a href="">Register</a>
              </div>
          </div>
      </div>
  </div>
  <!-- Page Header End -->

  <div class="container my-5">
      <div class="row justify-content-center">
          <div class="col-lg-6">
              <div class="register-form">
                  <form method="POST" action="{{ route('asom.register') }}">
                      @csrf
                      
                      <div class="form-group">
                          <label for="name">Full Name</label>
                          <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus>
                      </div>

                      <div class="form-group">
                          <label for="email">Email Address</label>
                          <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                      </div>

                      <div class="form-group">
                          <label for="password">Password</label>
                          <input type="password" class="form-control" id="password" name="password" required>
                      </div>

                      <div class="form-group">
                          <label for="password_confirmation">Confirm Password</label>
                          <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                      </div>
                      <div class="form-group g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                      <button type="submit" class="btn btn-custom">Register</button>

                      <p class="mt-3">
                          Already have an account? <a href="{{ route('login') }}">Login here</a>
                      </p>
                  </form>
              </div>
          </div>
      </div>
  </div>
</x-layouts.app>
