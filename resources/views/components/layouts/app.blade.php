@include('components.layouts.header')

{{$slot}}

@include('components.layouts.footer')



<div class="cookie-notice"
     role="alertdialog"
     aria-live="polite"
     aria-label="Cookie consent"
     aria-describedby="cookie-notice-message"
     style="display: none;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <p id="cookie-notice-message" class="mb-0">We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.</p>
            <button class="btn btn-primary accept-cookies" aria-label="Accept cookies and close notice">Accept</button>
            <a href="{{ route('privacy') }}" class="btn btn-outline-light btn-sm" aria-label="Learn more about our cookie policy">Learn More</a>
        </div>
    </div>
</div>

<style>
.cookie-notice {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 1rem 0;
    z-index: 1000;
}
</style>
