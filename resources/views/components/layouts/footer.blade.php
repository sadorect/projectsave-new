<footer class="site-footer mt-5">
    <div class="surface-frame py-5">
        <div class="site-footer-grid">
            <section class="site-footer-panel site-footer-brand-panel">
                <div class="surface-kicker mb-3">Projectsave International</div>
                <h2 class="mb-3">One ministry platform for outreach, discipleship, prayer, and ASOM formation.</h2>
                <p class="site-footer-copy mb-4">
                    Move across ministry stories, events, devotionals, partnership pathways, and ASOM learning from one
                    connected public experience.
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="surface-button-primary">Volunteer with us</a>
                    <a href="{{ route('lms.courses.index') }}" class="surface-button-secondary">Explore ASOM</a>
                </div>

                <div class="site-footer-meta mt-4">
                    <div>
                        <strong>Mission</strong>
                        <span>Winning the lost and building the saints through Christ-centered ministry service.</span>
                    </div>
                    <div>
                        <strong>Reach</strong>
                        <span>Public teaching, live gatherings, partnerships, and structured ministry training.</span>
                    </div>
                </div>
            </section>

            <section class="site-footer-panel">
                <h3 class="site-footer-heading">Explore</h3>
                <div class="site-footer-link-list">
                    <a href="{{ route('about') }}" class="site-footer-link">About the ministry</a>
                    <a href="{{ route('events.index') }}" class="site-footer-link">Events and gatherings</a>
                    <a href="{{ route('blog.index') }}" class="site-footer-link">Devotionals</a>
                    <a href="{{ route('faqs.list') }}" class="site-footer-link">FAQs</a>
                    <a href="{{ route('contact.show') }}" class="site-footer-link">Contact</a>
                </div>
            </section>

            <section class="site-footer-panel">
                <h3 class="site-footer-heading">Participate</h3>
                <div class="site-footer-link-list">
                    <a href="{{ route('partners.create', ['type' => 'prayer']) }}" class="site-footer-link">Join Prayer Force</a>
                    <a href="{{ route('partners.create', ['type' => 'skilled']) }}" class="site-footer-link">Partner with skills</a>
                    <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="site-footer-link">Serve on the ground</a>
                    <a href="{{ route('privacy') }}" class="site-footer-link">Privacy policy</a>
                </div>
            </section>

            <section class="site-footer-panel">
                <h3 class="site-footer-heading">Reach us</h3>
                <div class="site-footer-contact-list">
                    <div class="site-footer-contact-item">
                        <i class="fas fa-map-marker-alt mt-1"></i>
                        <span>P.O.Box 358, Ota, Ogun State, Nigeria.</span>
                    </div>
                    <div class="site-footer-contact-item">
                        <i class="fas fa-phone-alt mt-1"></i>
                        <span>(+234) 07080100893</span>
                    </div>
                    <div class="site-footer-contact-item">
                        <i class="fas fa-envelope mt-1"></i>
                        <span>info@projectsaveng.org</span>
                    </div>
                </div>

                <div class="site-footer-socials mt-4">
                    <a href="https://facebook.com/projectsave02" class="site-footer-link" aria-label="Projectsave on Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://instagram.com/projectsave_ministries" class="site-footer-link" aria-label="Projectsave on Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </section>
        </div>
    </div>

    <div class="site-footer-bottom">
        <div class="surface-frame site-footer-bottom-copy d-flex flex-column gap-2 flex-md-row align-items-md-center justify-content-md-between py-3">
            <p class="mb-0">&copy; {{ now()->year }} Projectsave International. All rights reserved.</p>
            <div class="d-flex flex-wrap gap-3">
                
                <a href="{{ route('privacy') }}" class="site-footer-link">Privacy policy</a>
        </div>
    </div>
</footer>

<button type="button" class="site-back-to-top" data-back-to-top aria-label="Back to top" hidden>
    <i class="bi bi-arrow-up"></i>
</button>
