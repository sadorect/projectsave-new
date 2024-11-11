<x-layouts.app>
  
        <!-- Page Header Start -->
        <div class="page-header">
          <div class="container">
              <div class="row">
                  <div class="col-12">
                      <h2>Contact Us</h2>
                  </div>
                  <div class="col-12">
                      <a href="">Home</a>
                      <a href="">Contact</a>
                  </div>
              </div>
          </div>
      </div>
      <!-- Page Header End -->
      
    <div class="container">
        <div class="section-header text-center">
            <p>Get In Touch</p>
            <h2>Contact Us</h2>
        </div>

        <div class="contact-form">
            @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif


            <form action="{{ route('contact.submit') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Your Name" value="{{ old('name') }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Your Email" value="{{ old('email') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="5" placeholder="Message">{{ old('message') }}</textarea>
                    @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    @error('g-recaptcha-response')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <button class="btn btn-custom" type="submit">Send Message</button>
            </form>
        </div>
    </div>
</x-layouts.app>
