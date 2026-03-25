<x-layouts.app
    title="Contact Projectsave International"
    meta-description="Reach the Projectsave team for ministry questions, event information, partnership interest, and support."
>
    <x-ui.public-page-hero
        eyebrow="Contact"
        title="Get in touch with the ministry team"
        subtitle="Reach out for partnership questions, event information, ministry support, or general enquiries. We review every message."
    >
        <x-slot:actions>
            <a href="mailto:info@projectsaveng.org" class="surface-button-primary">
                <i class="fas fa-envelope me-2"></i>Email us directly
            </a>
            <a href="{{ route('events.index') }}" class="surface-button-secondary">See upcoming events</a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section">
        <div class="surface-frame">
            <div class="row g-5">

                {{-- Contact form --}}
                <div class="col-lg-7">
                    <div class="contact-form-shell">
                        <span class="page-section-eyebrow mb-2 d-block">Send a message</span>
                        <h2 class="page-section-title" style="font-size:1.65rem;">We would love to hear from you</h2>
                        <p class="page-section-desc mb-4" style="font-size:0.92rem;">Use the form below and a team member will follow up with you by email.</p>

                        @if(session('success'))
                            <div class="alert alert-success rounded-4 mb-4" role="alert">
                                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('contact.submit') }}" method="POST">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="contact-name" class="form-label fw-semibold" style="font-size:0.85rem;">Your Name <span class="text-danger">*</span></label>
                                    <input id="contact-name" type="text" name="name" class="form-control rounded-3 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="John Adeyemi" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="contact-email" class="form-label fw-semibold" style="font-size:0.85rem;">Email Address <span class="text-danger">*</span></label>
                                    <input id="contact-email" type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@example.com" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label for="contact-message" class="form-label fw-semibold" style="font-size:0.85rem;">Message <span class="text-danger">*</span></label>
                                    <textarea id="contact-message" name="message" class="form-control rounded-3 @error('message') is-invalid @enderror" rows="6" placeholder="Tell us how we can help or how you'd like to get involved..." required>{{ old('message') }}</textarea>
                                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <x-math-captcha />
                                </div>

                                <div class="col-12">
                                    <button class="surface-button-primary" type="submit">
                                        <i class="bi bi-send me-2"></i>Send message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Contact details sidebar --}}
                <div class="col-lg-5">
                    <div class="d-flex flex-column gap-4">

                        {{-- Direct contact info --}}
                        <div class="contact-info-card">
                            <span class="page-section-eyebrow mb-3 d-block">Contact details</span>
                            <h3 style="font-size:1.1rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem;">Reach us directly</h3>

                            <div class="contact-info-item">
                                <div class="contact-info-icon"><i class="bi bi-geo-alt-fill"></i></div>
                                <div>
                                    <div class="contact-info-label">Mailing address</div>
                                    <div class="contact-info-value">P.O.Box 358, Ota, Ogun State, Nigeria</div>
                                </div>
                            </div>

                            <div class="contact-info-item">
                                <div class="contact-info-icon"><i class="bi bi-telephone-fill"></i></div>
                                <div>
                                    <div class="contact-info-label">Phone</div>
                                    <div class="contact-info-value">
                                        <a href="tel:+2347080100893" style="color:#0f172a;text-decoration:none;font-weight:500;">(+234) 07080100893</a>
                                    </div>
                                </div>
                            </div>

                            <div class="contact-info-item" style="border-bottom:none;">
                                <div class="contact-info-icon"><i class="bi bi-envelope-fill"></i></div>
                                <div>
                                    <div class="contact-info-label">Email</div>
                                    <div class="contact-info-value">
                                        <a href="mailto:info@projectsaveng.org" style="color:#c1121f;text-decoration:none;font-weight:500;">info@projectsaveng.org</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Next steps --}}
                        <div class="contact-info-card">
                            <span class="page-section-eyebrow mb-2 d-block">Next steps</span>
                            <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:1rem;">You may also want to</h3>
                            <div class="d-grid gap-2">
                                <a href="{{ route('events.index') }}" class="surface-button-secondary justify-content-center">
                                    <i class="bi bi-calendar-event me-2"></i>See upcoming events
                                </a>
                                <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="surface-button-secondary justify-content-center">
                                    <i class="bi bi-person-check me-2"></i>Volunteer with us
                                </a>
                                <a href="{{ route('lms.courses.index') }}" class="surface-button-secondary justify-content-center">
                                    <i class="bi bi-mortarboard me-2"></i>Explore ASOM
                                </a>
                            </div>
                        </div>

                        {{-- Response expectation --}}
                        <div style="background:linear-gradient(135deg,rgba(193,18,31,0.04),rgba(188,111,44,0.04));border:1px solid rgba(193,18,31,0.1);border-radius:14px;padding:1.25rem 1.5rem;">
                            <div class="public-kicker mb-2">Response time</div>
                            <p style="font-size:0.88rem;color:#6b7280;line-height:1.7;margin:0;">We review messages as quickly as possible and use email as the primary follow-up channel for ministry and partnership enquiries.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
