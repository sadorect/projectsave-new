<x-layouts.app
    title="Server Error – Projectsave International"
    meta-description="We encountered a server error. Please try again shortly."
>
    <section class="surface-section py-5">
        <div class="surface-frame">
            <div class="row justify-content-center text-center">
                <div class="col-lg-6">
                    <p class="surface-kicker mb-3">Error 500</p>
                    <h1 class="h2 mb-3">Server Error</h1>
                    <p class="text-muted mb-4">We're experiencing some technical difficulties. Our team has been notified and is working on it.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('home') }}" class="surface-button-primary">
                            <i class="bi bi-house"></i> Return Home
                        </a>
                        <a href="{{ route('contact.show') }}" class="surface-button-secondary">
                            <i class="bi bi-envelope"></i> Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>

