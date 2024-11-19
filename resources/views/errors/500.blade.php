<x-layouts.app>
  <!-- Page Header Start -->
  <div class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2>Server Error</h2>
            </div>
            <div class="col-12">
                <a href="{{ route('home') }}">Home</a>
                <a href="">Error 500</a>
            </div>
        </div>
    </div>
</div>
<!-- Page Header End -->
    <div class="error-page">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-6">
                    <div class="error-content">
                        <h1 class="display-1 text-danger">500</h1>
                        <h2>Server Error</h2>
                        <p>We're experiencing some technical difficulties. Our team has been notified and is working on it.</p>
                        <div class="error-actions mt-4">
                            <a href="{{ route('home') }}" class="btn btn-custom">
                                <i class="fa fa-home mr-2"></i>Return Home
                            </a>
                            <a href="{{ route('contact.show') }}" class="btn btn-custom">
                                <i class="fa fa-envelope mr-2"></i>Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
