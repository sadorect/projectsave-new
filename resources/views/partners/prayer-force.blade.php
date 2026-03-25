<x-layouts.app
    title="Prayer Force — Join the Intercession Team"
    meta-description="Stand with Projectsave International in focused intercession and spiritual covering. Apply to join the Prayer Force."
>
    <x-ui.public-page-hero
        eyebrow="Partner with the mission"
        title="Prayer Force application"
        subtitle="Stand with the ministry in focused intercession and spiritual covering."
    />

    <section class="surface-section pt-2">
        <div class="surface-frame">
            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <div class="public-form-shell">
                        <x-ui.public-section-heading
                            eyebrow="Application form"
                            title="Tell us about yourself"
                            description="Complete the application carefully. We use this information to review your application and help you take the right next step in ministry partnership."
                        />

                        <form id="prayerForceForm" method="POST" action="{{ route('volunteer.prayer-force.store') }}" class="mt-4">
                            @csrf

                            @include('partners.partials.personal-info')
                            @include('partners.partials.spiritual-background')
                            @include('partners.partials.leadership')

                            <div class="public-divider mt-5 pt-5"></div>

                            <div class="public-section-heading mb-4">
                                <div class="public-kicker">Step 4</div>
                                <h3 class="mb-0 text-2xl font-semibold">Prayer Commitment</h3>
                                <p class="public-section-description mb-0">Help us understand your calling and commitment to intercession.</p>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">What is your calling?</label>
                                    <input
                                        type="text"
                                        class="form-control @error('calling') is-invalid @enderror"
                                        name="calling"
                                        value="{{ old('calling') }}"
                                        placeholder="Describe your sense of calling to intercessory ministry"
                                        required
                                    >
                                    @error('calling')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Are you committed to praying regularly for the ministry?</label>
                                    <select class="form-select @error('prayer_commitment') is-invalid @enderror" name="prayer_commitment" required>
                                        <option value="">Select...</option>
                                        <option value="yes" @selected(old('prayer_commitment') === 'yes')>Yes, I am committed</option>
                                        <option value="no" @selected(old('prayer_commitment') === 'no')>No, not at this time</option>
                                    </select>
                                    @error('prayer_commitment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="public-divider mt-5 pt-5"></div>

                            <x-math-captcha />

                            <div class="d-flex flex-column gap-3 flex-md-row align-items-md-center justify-content-between mt-4">
                                <p class="mb-0 text-muted">By submitting this form, you confirm that the information provided is accurate to the best of your knowledge.</p>
                                <button type="submit" class="surface-button-primary">Submit application</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">
                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="What to expect"
                                title="Before you submit"
                            />

                            <ul class="mb-0 mt-4 text-muted">
                                <li class="mb-2">Complete all sections honestly and clearly.</li>
                                <li class="mb-2">Leadership details are only required if you answer yes.</li>
                                <li class="mb-2">We may follow up by email or phone for next steps.</li>
                                <li class="mb-0">Approved applications will receive orientation guidance.</li>
                            </ul>
                        </div>

                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="About Prayer Force"
                                title="What is the Prayer Force?"
                            />

                            <p class="text-muted mt-4 mb-3">The Prayer Force is a dedicated team of intercessors who cover the ministry, its leaders, and its outreach activities in consistent prayer.</p>

                            <p class="text-muted mb-0">Members receive ministry prayer items, join scheduled intercession calls, and serve as a spiritual backbone for all of Projectsave's work.</p>
                        </div>

                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="Prefer another route?"
                                title="Other partnership paths"
                            />

                            <div class="d-grid gap-2 mt-4">
                                <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="surface-button-secondary justify-content-center">Ground Force</a>
                                <a href="{{ route('partners.create', ['type' => 'skilled']) }}" class="surface-button-secondary justify-content-center">Skilled Partner</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
