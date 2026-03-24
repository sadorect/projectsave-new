<x-layouts.asom-auth
    page-title="My Certificates"
    subtitle="Review issued certificates, pending approvals, and the records attached to your completed ASOM journey."
>
    <div class="d-grid gap-4">
        <section class="lms-summary-grid">
            <article class="lms-summary-card">
                <span class="label">Total certificates</span>
                <span class="value">{{ $certificateStats['total'] }}</span>
            </article>
            <article class="lms-summary-card">
                <span class="label">Approved</span>
                <span class="value">{{ $certificateStats['approved'] }}</span>
            </article>
            <article class="lms-summary-card">
                <span class="label">Pending</span>
                <span class="value">{{ $certificateStats['pending'] }}</span>
            </article>
            <article class="lms-summary-card">
                <span class="label">Program</span>
                <span class="value">{{ $certificateStats['program'] }}</span>
            </article>
        </section>

        @if($certificates->isEmpty())
            <x-ui.empty-state
                title="No certificates yet"
                message="Your Diploma in Ministry certificate and any admin-issued course recognitions will appear here after approval."
            >
                <x-slot:actions>
                    <a href="{{ route('lms.dashboard') }}" class="surface-button-primary">Back to workspace</a>
                    <a href="{{ route('lms.courses.index') }}" class="surface-button-secondary">Browse courses</a>
                </x-slot:actions>
            </x-ui.empty-state>
        @else
            <section class="lms-course-grid">
                @foreach($certificates as $certificate)
                    <article class="lms-certificate-card">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <h3 class="h5 mb-1">{{ $certificate->certificate_type }}</h3>
                                <p class="text-muted small mb-0">{{ $certificate->certificate_id }}</p>
                            </div>
                            <span class="lms-pill">
                                <i class="fas {{ $certificate->is_approved ? 'fa-check-circle text-success' : 'fa-clock text-warning' }}"></i>
                                {{ $certificate->is_approved ? 'Approved' : 'Pending' }}
                            </span>
                        </div>

                        <div class="d-grid gap-2 small text-muted mb-4">
                            <div><strong>Completed:</strong> {{ optional($certificate->completed_at)->format('M j, Y') ?? 'In review' }}</div>
                            <div><strong>Issued:</strong> {{ optional($certificate->issued_at)->format('M j, Y') ?? 'Awaiting approval' }}</div>
                            @if($certificate->final_grade)
                                <div><strong>Final grade:</strong> {{ number_format($certificate->final_grade, 1) }}%</div>
                            @endif
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('lms.certificates.show', $certificate) }}" class="surface-button-secondary">View</a>
                            @if($certificate->is_approved)
                                <a href="{{ route('lms.certificates.download', $certificate) }}" class="surface-button-primary">Download</a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </section>
        @endif
    </div>
</x-layouts.asom-auth>
