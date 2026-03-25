<x-layouts.app
    title="Access Restricted - Projectsave International"
    meta-description="Your access to this page is currently restricted."
>
    <section class="surface-section py-5">
        <div class="surface-frame">
            <div class="row justify-content-center text-center">
                <div class="col-lg-7">
                    <p class="surface-kicker mb-3">Error 403</p>
                    <h1 class="h2 mb-3">This area is no longer available to your account</h1>
                    <p class="text-muted mb-3">
                        Your permissions may have changed while you were signed in, or this page requires access you do not currently have.
                    </p>
                    @if(! empty($requestPath))
                        <p class="small text-muted mb-4">Requested path: {{ $requestPath }}</p>
                    @endif
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        @if(! empty($backofficeRoute))
                            <a href="{{ $backofficeRoute }}" class="surface-button-primary">
                                <i class="bi bi-briefcase"></i> Open my current workspace
                            </a>
                        @endif
                        <a href="{{ $dashboardRoute }}" class="surface-button-secondary">
                            <i class="bi bi-grid"></i> Go to dashboard
                        </a>
                        <a href="{{ route('home') }}" class="surface-button-secondary">
                            <i class="bi bi-house"></i> Return home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>