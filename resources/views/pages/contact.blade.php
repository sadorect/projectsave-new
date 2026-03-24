<x-layouts.app
    title="Contact Projectsave International"
    meta-description="Reach the Projectsave team for ministry questions, event information, partnership interest, and support."
>
    <x-ui.public-page-hero
        eyebrow="Contact"
        title="Get in touch with the ministry team"
        subtitle="Reach out for partnership questions, event information, ministry support, or general enquiries."
    />

    <section class="surface-section pt-2">
        <div class="surface-frame">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="public-form-shell">
                        <x-ui.public-section-heading
                            eyebrow="Send a message"
                            title="We would love to hear from you"
                            description="Use the form below and a team member will follow up with you."
                        />

                        <form action="{{ route('contact.submit') }}" method="POST" class="mt-4">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="contact-name" class="form-label fw-semibold">Your Name</label>
                                    <input id="contact-name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="contact-email" class="form-label fw-semibold">Your Email</label>
                                    <input id="contact-email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="contact-message" class="form-label fw-semibold">Message</label>
                                    <textarea id="contact-message" name="message" class="form-control @error('message') is-invalid @enderror" rows="6" required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <x-math-captcha />
                                </div>

                                <div class="col-12">
                                    <button class="surface-button-primary" type="submit">Send message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="d-flex flex-column gap-4">
                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="Contact details"
                                title="Reach us directly"
                                description="For ministry questions, partnership follow-up, and event enquiries."
                            />

                            <div class="public-contact-list mt-4 text-muted">
                                <div class="d-flex gap-3">
                                    <i class="bi bi-geo-alt-fill text-brand-700 mt-1"></i>
                                    <span>P.O.Box 358, Ota, Ogun State, Nigeria.</span>
                                </div>
                                <div class="d-flex gap-3">
                                    <i class="bi bi-telephone-fill text-brand-700 mt-1"></i>
                                    <span>(+234) 07080100893</span>
                                </div>
                                <div class="d-flex gap-3">
                                    <i class="bi bi-envelope-fill text-brand-700 mt-1"></i>
                                    <span>info@projectsaveng.org</span>
                                </div>
                            </div>
                        </div>

                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="Popular next steps"
                                title="You may also want to"
                            />

                            <div class="d-grid gap-2 mt-4">
                                <a href="{{ route('events.index') }}" class="surface-button-secondary justify-content-center">See upcoming events</a>
                                <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="surface-button-secondary justify-content-center">Volunteer with us</a>
                                <a href="{{ route('lms.courses.index') }}" class="surface-button-secondary justify-content-center">Explore ASOM</a>
                            </div>
                        </div>

                        <div class="public-sidebar-card">
                            <div class="public-kicker mb-3">Response expectation</div>
                            <p class="mb-0 text-muted">We review contact requests as quickly as possible and use email as the main follow-up channel for ministry and partnership enquiries.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
