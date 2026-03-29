@php
    $siteName = $siteSettings['site_name'] ?? 'Projectsave International';
    $siteShortName = $siteSettings['site_short_name'] ?? 'Projectsave';
    $siteTagline = $siteSettings['site_tagline'] ?? 'Winning the lost. Building the saints.';
    $siteDescription = $siteSettings['site_description'] ?? null;
    $logoUrl = $siteSettings['logo_url'] ?? null;
    $contactPhone = $siteSettings['contact_phone'] ?? null;
    $contactPhoneHref = $siteSettings['contact_phone_href'] ?? null;
    $contactEmail = $siteSettings['contact_email'] ?? null;
    $contactEmailHref = $siteSettings['contact_email_href'] ?? null;
    $contactAddress = $siteSettings['contact_address'] ?? null;
    $socialLinks = collect($siteSettings['social_links'] ?? []);
@endphp

<footer class="site-footer">

    {{-- Scripture band --}}
    <div class="site-footer-scripture-band">
        <div class="surface-frame site-footer-scripture-inner">
            <i class="bi bi-quote"></i>
            <span>"Go into all the world and preach the Gospel to every creature."</span>
            <cite>&mdash; Mark 16:15</cite>
        </div>
    </div>

    {{-- Main body --}}
    <div class="surface-frame site-footer-body">
        <div class="site-footer-grid">

            {{-- Brand panel --}}
            <section class="site-footer-brand-panel" aria-label="About Projectsave">
                <div class="site-footer-brand-mark">
                    @if(filled($logoUrl))
                        <img src="{{ $logoUrl }}" alt="{{ $siteName }}" width="42" height="42">
                    @endif
                    <div>
                        <strong class="site-footer-brand-name">{{ $siteShortName }}</strong>
                        <span class="site-footer-brand-sub">{{ str($siteName)->replaceFirst($siteShortName, '')->trim() ?: 'International' }}</span>
                    </div>
                </div>

                <p class="site-footer-tagline">{{ str_replace('. ', ' · ', $siteTagline) }}</p>

                @if(filled($siteDescription))
                    <p class="site-footer-copy">{{ $siteDescription }}</p>
                @endif

                <div class="site-footer-cta-row">
                    <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="home-btn-primary">
                        <i class="bi bi-arrow-right-circle-fill"></i> Volunteer with us
                    </a>
                    <a href="{{ route('lms.courses.index') }}" class="home-btn-glass">
                        Explore ASOM
                    </a>
                </div>

                @if($socialLinks->isNotEmpty())
                    <div class="site-footer-socials mt-4">
                        @foreach($socialLinks as $socialLink)
                            <a href="{{ $socialLink['url'] }}" class="site-footer-social-icon" aria-label="{{ $socialLink['label'] }}" rel="noopener noreferrer" target="_blank">
                                <i class="{{ $socialLink['icon'] }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <span class="public-chip">{{ number_format($totalSiteVisits ?? 0) }} site visits</span>
                    <span class="public-chip">{{ number_format($totalPostViews ?? 0) }} article views</span>
                </div>
            </section>

            {{-- Explore --}}
            <section class="site-footer-nav-panel" aria-label="Explore">
                <h3 class="site-footer-heading">Explore</h3>
                <nav class="site-footer-link-list">
                    <a href="{{ route('about') }}" class="site-footer-link">About the ministry</a>
                    <a href="{{ route('events.index') }}" class="site-footer-link">Events &amp; gatherings</a>
                    <a href="{{ route('reports.index') }}" class="site-footer-link">Ministry reports</a>
                    <a href="{{ route('blog.index') }}" class="site-footer-link">Devotionals</a>
                    <a href="{{ route('lms.courses.index') }}" class="site-footer-link">ASOM courses</a>
                    <a href="{{ route('faqs.list') }}" class="site-footer-link">FAQs</a>
                    <a href="{{ route('contact.show') }}" class="site-footer-link">Contact</a>
                </nav>
            </section>

            {{-- Participate --}}
            <section class="site-footer-nav-panel" aria-label="Participate">
                <h3 class="site-footer-heading">Get involved</h3>
                <nav class="site-footer-link-list">
                    <a href="{{ route('partners.create', ['type' => 'prayer']) }}" class="site-footer-link">Join Prayer Force</a>
                    <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="site-footer-link">Serve on the ground</a>
                    <a href="{{ route('partners.create', ['type' => 'skilled']) }}" class="site-footer-link">Partner with skills</a>
                    <a href="{{ route('volunteer.prayer-force') }}" class="site-footer-link">Volunteer</a>
                    @guest
                        <a href="{{ route('login') }}" class="site-footer-link">Login</a>
                    @endguest
                </nav>
            </section>

            {{-- Contact + Newsletter --}}
            <section class="site-footer-nav-panel" aria-label="Contact and newsletter">
                <h3 class="site-footer-heading">Reach us</h3>

                <div class="site-footer-contact-list">
                    @if(filled($contactAddress))
                        <div class="site-footer-contact-item">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>{{ $contactAddress }}</span>
                        </div>
                    @endif
                    @if(filled($contactPhone) && filled($contactPhoneHref))
                        <div class="site-footer-contact-item">
                            <i class="bi bi-telephone-fill"></i>
                            <a href="{{ $contactPhoneHref }}" class="site-footer-link">{{ $contactPhone }}</a>
                        </div>
                    @endif
                    @if(filled($contactEmail) && filled($contactEmailHref))
                        <div class="site-footer-contact-item">
                            <i class="bi bi-envelope-fill"></i>
                            <a href="{{ $contactEmailHref }}" class="site-footer-link">{{ $contactEmail }}</a>
                        </div>
                    @endif
                </div>

                <div class="site-footer-newsletter mt-4">
                    <p class="site-footer-newsletter-label">
                        <i class="bi bi-send-fill me-2"></i>Stay connected
                    </p>
                    <form class="site-footer-newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <div class="site-footer-newsletter-row">
                            <input type="email" name="email" placeholder="Your email address"
                                   class="site-footer-newsletter-input" value="{{ old('email') }}" required autocomplete="email">
                            <button type="submit" class="site-footer-newsletter-btn" aria-label="Subscribe">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                        @if($errors->getBag('newsletterSubscription')->has('email'))
                            <p class="site-footer-newsletter-note text-danger mb-0 mt-2">
                                {{ $errors->getBag('newsletterSubscription')->first('email') }}
                            </p>
                        @endif
                        <div class="mt-3">
                            <x-math-captcha input-class="site-footer-newsletter-input" error-bag="newsletterSubscription" />
                        </div>
                        <p class="site-footer-newsletter-note">Solve the quick check to subscribe. New devotionals are mailed when they are published.</p>
                    </form>
                </div>
            </section>

        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="site-footer-bottom">
        <div class="surface-frame site-footer-bottom-inner">
            <p class="mb-0">&copy; {{ now()->year }} {{ $siteName }}. All rights reserved.</p>
            <nav class="site-footer-bottom-links">
                <a href="{{ route('privacy') }}" class="site-footer-link">Privacy policy</a>
                <a href="{{ route('faqs.list') }}" class="site-footer-link">FAQs</a>
                <a href="{{ route('contact.show') }}" class="site-footer-link">Contact</a>
                <span class="site-footer-bottom-credit">Designed by <a href="https://sadorect.com" class="site-footer-link" rel="noopener noreferrer" target="_blank">Sadorect</a></span>
            </nav>
        </div>
    </div>
</footer>

<button type="button" class="site-back-to-top" data-back-to-top aria-label="Back to top" hidden>
    <i class="bi bi-arrow-up"></i>
</button>
