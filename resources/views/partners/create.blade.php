<x-layouts.app
    :title="ucfirst($partnerType) . ' Partnership Application'"
    :meta-description="'Apply to join the ' . ucfirst($partnerType) . ' partnership pathway with Projectsave International.'"
>
    @php
        $typeTitle = ucfirst($partnerType) . ' Force';
        $typeDescriptions = [
            'prayer' => 'Stand with the ministry in focused intercession and spiritual covering.',
            'ground' => 'Serve directly in meetings, trips, events, and ministry operations.',
            'skilled' => 'Offer your professional skill to strengthen the work practically.',
            'financial' => 'Support outreach, discipleship, and ministry activity through giving.',
        ];
        $typeIcons = [
            'prayer' => 'fas fa-pray',
            'ground' => 'fas fa-hands-helping',
            'skilled' => 'fas fa-tools',
            'financial' => 'fas fa-hand-holding-usd',
        ];
    @endphp

    <x-ui.public-page-hero
        eyebrow="Partner with the mission"
        :title="$typeTitle . ' application'"
        :subtitle="$typeDescriptions[$partnerType] ?? 'Take your next step with the ministry by completing the application below.'"
    >
        <x-slot:actions>
            <a href="{{ route('about') }}" class="surface-button-secondary">
                <i class="bi bi-arrow-left me-2"></i>Learn about Projectsave
            </a>
            <a href="{{ route('contact.show') }}" class="surface-button-primary">
                <i class="bi bi-chat-dots me-2"></i>Speak to the team first
            </a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section">
        <div class="surface-frame">
            <div class="row g-5 align-items-start">

                {{-- Application form --}}
                <div class="col-lg-8">
                    <div class="contact-form-shell">
                        <div class="partner-type-badge">
                            <i class="{{ $typeIcons[$partnerType] ?? 'fas fa-user' }}"></i>
                            {{ $typeTitle }}
                        </div>

                        <span class="page-section-eyebrow mb-2 d-block">Application form</span>
                        <h2 class="page-section-title" style="font-size:1.65rem;">Tell us about yourself</h2>
                        <p class="page-section-desc mb-4" style="font-size:0.92rem;">Complete the application carefully. We use this information to review fit, follow up, and help you take the right next step with us.</p>

                        <form id="partnerForm" method="POST" action="{{ route('partners.store', $partnerType) }}">
                            @csrf

                            @include('partners.partials.personal-info')
                            @include('partners.partials.spiritual-background')
                            @include('partners.partials.leadership')
                            @include('partners.partials.commitment')

                            <div class="public-divider mt-5 pt-4"></div>

                            <x-math-captcha />

                            <div class="d-flex flex-column gap-3 flex-md-row align-items-md-center justify-content-between mt-4">
                                <p style="font-size:0.85rem;color:#6b7280;max-width:45ch;line-height:1.65;margin:0;">By submitting, you confirm that the information provided is accurate to the best of your knowledge.</p>
                                <button type="submit" class="surface-button-primary" style="white-space:nowrap;">
                                    <i class="bi bi-send me-2"></i>Submit application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">

                        <div class="public-sidebar-card">
                            <span class="page-section-eyebrow mb-2 d-block">What to expect</span>
                            <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:0.9rem;">Before you submit</h3>
                            <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.65rem;">
                                <li style="display:flex;align-items:flex-start;gap:0.6rem;font-size:0.87rem;color:#374151;">
                                    <i class="bi bi-check-circle-fill" style="color:#c1121f;margin-top:0.15rem;flex-shrink:0;"></i>
                                    Complete all sections honestly and clearly.
                                </li>
                                <li style="display:flex;align-items:flex-start;gap:0.6rem;font-size:0.87rem;color:#374151;">
                                    <i class="bi bi-check-circle-fill" style="color:#c1121f;margin-top:0.15rem;flex-shrink:0;"></i>
                                    Leadership details are only required if you answer yes.
                                </li>
                                <li style="display:flex;align-items:flex-start;gap:0.6rem;font-size:0.87rem;color:#374151;">
                                    <i class="bi bi-check-circle-fill" style="color:#c1121f;margin-top:0.15rem;flex-shrink:0;"></i>
                                    We may follow up by email or phone for next steps.
                                </li>
                                <li style="display:flex;align-items:flex-start;gap:0.6rem;font-size:0.87rem;color:#374151;">
                                    <i class="bi bi-check-circle-fill" style="color:#c1121f;margin-top:0.15rem;flex-shrink:0;"></i>
                                    Approved applications receive orientation guidance.
                                </li>
                            </ul>
                        </div>

                        <div class="public-sidebar-card">
                            <span class="page-section-eyebrow mb-2 d-block">Other partnership paths</span>
                            <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:1rem;">Choose a different path</h3>
                            <div class="d-flex flex-column gap-2">
                                @if($partnerType !== 'prayer')
                                    <a href="{{ route('partners.create', ['type' => 'prayer']) }}" class="partner-path-card">
                                        <i class="fas fa-pray"></i>Prayer Force
                                    </a>
                                @endif
                                @if($partnerType !== 'ground')
                                    <a href="{{ route('partners.create', ['type' => 'ground']) }}" class="partner-path-card">
                                        <i class="fas fa-hands-helping"></i>Ground Force
                                    </a>
                                @endif
                                @if($partnerType !== 'skilled')
                                    <a href="{{ route('partners.create', ['type' => 'skilled']) }}" class="partner-path-card">
                                        <i class="fas fa-tools"></i>Skilled Partner
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div style="background:linear-gradient(135deg,rgba(193,18,31,0.04),rgba(188,111,44,0.04));border:1px solid rgba(193,18,31,0.1);border-radius:14px;padding:1.25rem 1.5rem;">
                            <div class="public-kicker mb-2">Have questions first?</div>
                            <p style="font-size:0.87rem;color:#6b7280;line-height:1.7;margin-bottom:1rem;">Contact the ministry team before completing the form and we'll help you find the right fit.</p>
                            <a href="{{ route('contact.show') }}" class="surface-button-secondary" style="font-size:0.85rem;">
                                <i class="bi bi-envelope me-2"></i>Get in touch
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
