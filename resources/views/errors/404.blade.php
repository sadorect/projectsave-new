<x-layouts.app
    title="Page Not Found – Projectsave International"
    meta-description="The page you were looking for could not be found."
>
    <section class="surface-section py-5">
        <div class="surface-frame">
            <div class="row justify-content-center text-center">
                <div class="col-lg-6">
                    <p class="surface-kicker mb-3">Error 404</p>
                    <h1 class="h2 mb-3">Page Not Found</h1>
                    <p class="text-muted mb-4">The page you're looking for might have been moved, deleted, or never existed.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('home') }}" class="surface-button-primary">
                            <i class="bi bi-house"></i> Return Home
                        </a>
                        <a href="{{ route('contact.show') }}" class="surface-button-secondary">
                            <i class="bi bi-envelope"></i> Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>

