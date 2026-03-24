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
    @endphp

    <x-ui.public-page-hero
        eyebrow="Partner with the mission"
        :title="$typeTitle . ' application'"
        :subtitle="$typeDescriptions[$partnerType] ?? 'Take your next step with the ministry by completing the application below.'"
    />

    <section class="surface-section pt-2">
        <div class="surface-frame">
            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <div class="public-form-shell">
                        <x-ui.public-section-heading
                            eyebrow="Application form"
                            title="Tell us about yourself"
                            description="Complete the application carefully. We use this information to review fit, follow up, and help you take the right next step."
                        />

                        <form id="partnerForm" method="POST" action="{{ route('partners.store', $partnerType) }}" class="mt-4">
                            @csrf

                            @include('partners.partials.personal-info')
                            @include('partners.partials.spiritual-background')
                            @include('partners.partials.leadership')
                            @include('partners.partials.commitment')

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
                                eyebrow="Prefer another route?"
                                title="Other partnership paths"
                            />

                            <div class="d-grid gap-2 mt-4">
                                <a href="{{ route('partners.create', ['type' => 'prayer']) }}" class="surface-button-secondary justify-content-center">Prayer Force</a>
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
