<x-layouts.app
    :title="ucfirst($partnerType) . ' Application Status'"
    :meta-description="'Review the current status of your ' . ucfirst($partnerType) . ' application with Projectsave International.'"
>
    <x-ui.public-page-hero
        eyebrow="Application status"
        :title="ucfirst($partnerType) . ' Force application'"
        :subtitle="'Current status: ' . ucfirst($partner->status)"
    >
        <x-slot:actions>
            <a href="{{ route('partners.create', ['type' => $partnerType]) }}" class="surface-button-secondary">Submit another application</a>
            <a href="{{ route('contact.show') }}" class="surface-button-primary">Contact the team</a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section pt-2">
        <div class="surface-frame">
            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <div class="public-card p-4 p-lg-5">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                            <div>
                                <div class="public-kicker mb-2">Application summary</div>
                                <h2 class="mb-0">Thank you for responding to the mission.</h2>
                            </div>
                            <span class="badge bg-{{ $partner->status === 'pending' ? 'warning' : ($partner->status === 'approved' ? 'success' : 'danger') }}">
                                {{ ucfirst($partner->status) }}
                            </span>
                        </div>

                        @include('partners.partials.show.personal-info')
                        @include('partners.partials.show.spiritual-info')
                        @include('partners.partials.show.leadership-info')
                        @include('partners.partials.show.commitment-info')
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">
                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="What happens next"
                                title="Review and follow-up"
                            />

                            <ul class="mb-0 mt-4 text-muted">
                                <li class="mb-2">Pending applications are reviewed by the ministry team.</li>
                                <li class="mb-2">Approved applicants receive next-step communication.</li>
                                <li class="mb-0">If you need help, contact us directly and mention your application type.</li>
                            </ul>
                        </div>

                        <div class="public-sidebar-card">
                            <div class="public-kicker mb-3">Need support?</div>
                            <p class="mb-3 text-muted">If anything in your application needs clarification, the team can help.</p>
                            <a href="{{ route('contact.show') }}" class="surface-button-primary">Contact support</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
