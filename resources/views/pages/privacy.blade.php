<x-layouts.app
    title="Privacy Policy | Projectsave International"
    meta-description="Review how Projectsave International collects, uses, protects, and deletes personal information."
>
    @php
        $privacyEmail = $siteSettings['privacy_email'] ?: ($siteSettings['contact_email'] ?? null);
        $contactPhone = $siteSettings['contact_phone'] ?? null;
        $contactAddress = $siteSettings['contact_address'] ?? null;
    @endphp
    <x-ui.public-page-hero
        eyebrow="Privacy policy"
        title="How we handle your information"
        subtitle="This page explains what we collect, how we use it, and how to request deletion or follow-up."
    >
        <x-slot:actions>
            <a href="{{ route('contact.show') }}" class="surface-button-primary">Contact the team</a>
            <a href="{{ route('home') }}" class="surface-button-secondary">Return home</a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section pt-2">
        <div class="surface-frame">
            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <div class="public-card p-4 p-lg-5">
                        <x-ui.public-section-heading
                            eyebrow="Overview"
                            title="Our commitment to privacy"
                            description="We collect only the information needed to operate ministry services, respond to requests, manage accounts, and improve the experience across the public site and LMS."
                        />

                        <div class="public-richtext mt-4">
                            <h3>Cookie policy</h3>
                            <p>We use cookies and similar technologies to improve site performance, remember preferences, and understand how visitors use the platform. Accepting cookies helps us provide a smoother experience across public pages and the LMS.</p>

                            <h3>Information we collect</h3>
                            <ul>
                                <li>Basic visitor data such as IP address, browser type, and device information.</li>
                                <li>Information you submit through forms, contact requests, account registration, and partnership applications.</li>
                                <li>Donation or transaction-related details where applicable.</li>
                                <li>Newsletter or ministry update subscription details.</li>
                            </ul>

                            <h3>How we use your information</h3>
                            <ul>
                                <li>To provide, maintain, and improve ministry services.</li>
                                <li>To respond to enquiries, support requests, and partnership applications.</li>
                                <li>To communicate important service or ministry updates.</li>
                                <li>To monitor service usage and improve the site experience.</li>
                            </ul>

                            <h3>Data protection</h3>
                            <p>We apply reasonable administrative and technical safeguards to protect personal information. No online transmission method is completely risk-free, but we work to handle submitted data responsibly.</p>

                            <h3>Third-party services</h3>
                            <p>We may use third-party providers to support payment processing, analytics, email delivery, and social platform integration where necessary for ministry operations.</p>

                            <h3>Your data deletion rights</h3>
                            <p>You may request deletion of your personal data from our systems.</p>
                            <ol>
                                <li>Log into your account and review any self-service deletion options available in account settings.</li>
                                <li>Or send a deletion request to {{ $privacyEmail ?: 'our privacy contact' }} with enough detail for us to identify your record.</li>
                                <li>We aim to process confirmed requests within 30 days, subject to legal or legitimate operational retention needs.</li>
                            </ol>

                            <blockquote>
                                Some information may be retained where required by law or where limited retention is necessary for legitimate administrative purposes.
                            </blockquote>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">
                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="Contact"
                                title="Privacy enquiries"
                                description="Reach us directly if you have questions about data handling, deletion, or account privacy."
                            />

                            <div class="public-contact-list mt-4 text-muted">
                                @if(filled($privacyEmail))
                                    <div class="d-flex gap-3">
                                        <i class="bi bi-envelope-fill text-brand-700 mt-1"></i>
                                        <span>{{ $privacyEmail }}</span>
                                    </div>
                                @endif
                                @if(filled($contactPhone))
                                    <div class="d-flex gap-3">
                                        <i class="bi bi-telephone-fill text-brand-700 mt-1"></i>
                                        <span>{{ $contactPhone }}</span>
                                    </div>
                                @endif
                                @if(filled($contactAddress))
                                    <div class="d-flex gap-3">
                                        <i class="bi bi-geo-alt-fill text-brand-700 mt-1"></i>
                                        <span>{{ $contactAddress }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="public-sidebar-card">
                            <div class="public-kicker mb-3">Helpful next steps</div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('contact.show') }}" class="surface-button-primary justify-content-center">Contact the team</a>
                                <a href="{{ route('partners.create', ['type' => 'prayer']) }}" class="surface-button-secondary justify-content-center">Join Prayer Force</a>
                                <a href="{{ route('lms.courses.index') }}" class="surface-button-secondary justify-content-center">Explore ASOM</a>
                            </div>
                        </div>

                        <div class="public-sidebar-card">
                            <div class="public-kicker mb-3">Policy note</div>
                            <p class="mb-0 text-muted">This policy should be reviewed whenever the site adds new data collection, new third-party services, or new account and learning workflows.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
