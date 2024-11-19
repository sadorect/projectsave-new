
        <!-- Footer Start -->
        <div class="footer">
          <div class="container">
              <div class="row">
                  <div class="col-lg-3 col-md-6">
                      <div class="footer-contact">
                          <h2>Contact Us</h2>
                          <p><i class="fa fa-map-marker-alt"></i>P.O.Box 358,
                            Ota- Ogun State
                            Nigeria.</p>
                          <p><i class="fa fa-phone-alt"></i>(+234) 07080100893</p>
                          <p><i class="fa fa-envelope"></i>info@projectsaveng.org</p>
                          <div class="footer-social">
                              <a class="btn btn-custom" href=""><i class="fab fa-twitter"></i></a>
                              <a class="btn btn-custom" href=""><i class="fab fa-facebook-f"></i></a>
                              <a class="btn btn-custom" href=""><i class="fab fa-youtube"></i></a>
                              <a class="btn btn-custom" href=""><i class="fab fa-instagram"></i></a>
                              <a class="btn btn-custom" href=""><i class="fab fa-linkedin-in"></i></a>
                          </div>
                      </div>
                  </div>
                  <div class="col-lg-3 col-md-6">
                      <div class="footer-link">
                          <h2>Popular Links</h2>
                          <a href="">About Us</a>
                          <a href="">Contact Us</a>
                          <a href="">Popular Causes</a>
                          <a href="">Upcoming Events</a>
                          <a href="">Latest Blog</a>
                      </div>
                  </div>
                  <div class="col-lg-3 col-md-6">
                      <div class="footer-link">
                          <h2>Useful Links</h2>
                          <a href="">Terms of use</a>
                          <a href="{{route('privacy')}}">Privacy policy</a>
                         
                          <a href="">Help</a>
                          <a href="">FAQs</a>
                      </div>
                  </div>
                  <div class="col-lg-3 col-md-6">
                      <div class="footer-newsletter">
                          <h2>Newsletter</h2>
                          <form>
                              <input class="form-control" placeholder="Email goes here">
                              <button class="btn btn-custom">Submit</button>
                              <label>Don't worry, we don't spam!</label>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
          <div class="container copyright">
              <div class="row">
                  <div class="col-md-6">
                      <p>&copy; {{date('Y')}} <a href="#">Projectsave International</a>, All Right Reserved.</p>
                  </div>
                  <div class="col-md-6">
                      <p>Designed By <a href="https://sadorect.com">Sadorect</a></p>
                  </div>
              </div>
          </div>
      </div>
      <!-- Footer End -->
      
      <!-- Back to top button -->
      <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>
      
      <!-- Pre Loader -->
      <div id="loader" class="show">
          <div class="loader"></div>
      </div>

      <!-- JavaScript Libraries -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
      <script  src="{{ asset('frontend/lib/easing/easing.min.js') }}"></script>
      <script  src="{{ asset('frontend/lib/owlcarousel/owl.carousel.min.js') }}"></script>
      <script  src="{{ asset('frontend/lib/waypoints/waypoints.min.js') }}"></script>
      <script  src="{{ asset('frontend/lib/counterup/counterup.min.js') }}"></script>
      <script  src="{{ asset('frontend/lib/parallax/parallax.min.js') }}"></script>
      
      <!-- Contact Javascript File -->
     
      <!-- Template Javascript -->
      <script src="{{ asset('frontend/js/main.js') }}"></script>
      <script src="{{ asset('js/partner-form.js') }}"></script>
      <script src="{{ asset('frontend/js/cookie-consent.js') }}"></script>
      @stack('scripts')

      <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "ProjectSave International",
            "url": "{{ config('app.url') }}",
            "logo": "{{ asset('frontend/img/psave_logo.png') }}",
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+234-07080100893",
                "email": "info@projectsaveng.org",
                "contactType": "customer service"
            }
        }
        </script>
    </body>
</html>




