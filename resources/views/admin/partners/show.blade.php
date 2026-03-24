@extends('admin.layouts.app')

@section('title', 'Partner Application')
@section('page_kicker', 'Partner Operations')
@section('page_subtitle', 'Review the full application record, then approve or reject with the right communication channels.')

@section('content')
<div class="admin-page-shell">
    <section class="admin-stat-grid">
        <article class="admin-stat-card">
            <span class="admin-stat-label">Applicant</span>
            <strong class="admin-stat-value admin-stat-value-sm">{{ $partner->name }}</strong>
            <p class="admin-stat-note mb-0">{{ ucfirst($partner->partner_type) }} Force candidate</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Current status</span>
            <strong class="admin-stat-value admin-stat-value-sm">{{ ucfirst($partner->status) }}</strong>
            <p class="admin-stat-note mb-0">Updated through the partner moderation queue.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Applied</span>
            <strong class="admin-stat-value admin-stat-value-sm">{{ $partner->created_at->format('M j, Y') }}</strong>
            <p class="admin-stat-note mb-0">{{ $partner->created_at->diffForHumans() }}</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Contact</span>
            <strong class="admin-stat-value admin-stat-value-sm">{{ $partner->email }}</strong>
            <p class="admin-stat-note mb-0">{{ $partner->phone }}</p>
        </article>
    </section>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <a href="{{ route('admin.partners.index') }}" class="surface-button-secondary">Back to queue</a>

        <div class="admin-action-row">
            @if($partner->status === 'pending')
                <button type="button" class="surface-button-primary" data-bs-toggle="modal" data-bs-target="#approvePartnerModal">
                    Approve application
                </button>

                <form action="{{ route('admin.partners.reject', $partner) }}" method="POST" class="d-inline-flex" data-admin-confirm="Reject this partner application?">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="notify_via[]" value="mail">
                    <button type="submit" class="surface-button-secondary">Reject application</button>
                </form>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="d-grid gap-4">
                <x-ui.panel title="Personal Profile" subtitle="Basic identity and application metadata for this record.">
                    <div class="admin-definition-grid">
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Name</span>
                            <strong>{{ $partner->name }}</strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Date of birth</span>
                            <strong>{{ $partner->dob?->format('M j, Y') ?? 'Not provided' }}</strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Profession</span>
                            <strong>{{ $partner->profession ?: 'Not provided' }}</strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Phone</span>
                            <strong>{{ $partner->phone }}</strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Email</span>
                            <strong>{{ $partner->email }}</strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Partner type</span>
                            <strong>{{ ucfirst($partner->partner_type) }} Force</strong>
                        </div>
                    </div>
                </x-ui.panel>

                <x-ui.panel title="Spiritual Background" subtitle="Review salvation, baptism, and Holy Spirit background before approving.">
                    <div class="admin-definition-grid">
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Born again</span>
                            <strong>{{ ucfirst($partner->born_again) }}</strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Salvation date</span>
                            <strong>{{ $partner->salvation_date?->format('M j, Y') ?? 'Not provided' }}</strong>
                        </div>
                        <div class="admin-definition-item admin-definition-item-span">
                            <span class="admin-definition-label">Salvation place</span>
                            <strong>{{ $partner->salvation_place ?: 'Not provided' }}</strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Water baptized</span>
                            <strong>{{ ucfirst($partner->water_baptized) }}</strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Baptism type</span>
                            <strong>{{ $partner->baptism_type ? ucfirst($partner->baptism_type) : 'Not provided' }}</strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Holy Ghost baptism</span>
                            <strong>{{ ucfirst($partner->holy_ghost_baptism) }}</strong>
                        </div>
                        <div class="admin-definition-item admin-definition-item-span">
                            <span class="admin-definition-label">Reason / note</span>
                            <strong>{{ $partner->holy_ghost_baptism_reason ?: 'No additional note provided' }}</strong>
                        </div>
                    </div>
                </x-ui.panel>

                <x-ui.panel title="Leadership Experience" subtitle="Reference checks and ministry leadership history provided by the applicant.">
                    @if($partner->leadership_experience === 'yes' && ! empty($partner->leadership_details))
                        <div class="surface-table-shell">
                            <table class="table admin-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Church / Fellowship</th>
                                        <th>Position</th>
                                        <th>Year</th>
                                        <th>Referee</th>
                                        <th>Contact</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($partner->leadership_details as $detail)
                                        <tr>
                                            <td>{{ $detail['church_name'] ?? 'Not provided' }}</td>
                                            <td>{{ $detail['post_held'] ?? 'Not provided' }}</td>
                                            <td>{{ $detail['year'] ?? 'n/a' }}</td>
                                            <td>{{ $detail['referee_name'] ?? 'Not provided' }}</td>
                                            <td>{{ $detail['referee_phone'] ?? 'Not provided' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <x-ui.empty-state
                            title="No leadership history supplied"
                            message="This applicant did not provide prior ministry leadership entries."
                            icon="bi bi-person-lines-fill"
                        />
                    @endif
                </x-ui.panel>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="d-grid gap-4">
                <x-ui.panel title="Commitment Snapshot" subtitle="Core motivation and service commitment captured on the application.">
                    <div class="admin-definition-grid admin-definition-grid-single">
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Calling</span>
                            <strong>{{ $partner->calling ?: 'Not provided' }}</strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">{{ $partner->commitment_question ?: 'Commitment question' }}</span>
                            <strong>{{ $partner->commitment_answer ?: 'Not provided' }}</strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Status</span>
                            <strong>
                                <span class="admin-status-chip {{ $partner->status === 'approved' ? 'is-success' : ($partner->status === 'rejected' ? 'is-danger' : 'is-warning') }}">
                                    {{ ucfirst($partner->status) }}
                                </span>
                            </strong>
                        </div>
                    </div>
                </x-ui.panel>

                <x-ui.panel title="Related Applications" subtitle="Other partner submissions that share this email address.">
                    @if($relatedApplications->isNotEmpty())
                        <div class="admin-stack-list">
                            @foreach($relatedApplications as $related)
                                <a href="{{ route('admin.partners.show', $related) }}" class="admin-stack-item text-decoration-none">
                                    <span>{{ ucfirst($related->partner_type) }} Force</span>
                                    <strong>{{ ucfirst($related->status) }}</strong>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No additional applications were found for this email address.</p>
                    @endif
                </x-ui.panel>

                <x-ui.panel title="Decision Guidance" subtitle="Use the summary at left to verify testimony, leadership history, and readiness before changing the record.">
                    <p class="text-muted mb-0">
                        Approvals should only move forward when the ministry team is comfortable with the applicant's testimony,
                        calling, and service readiness. If more clarity is needed, leave the record pending and follow up directly.
                    </p>
                </x-ui.panel>
            </div>
        </div>
    </div>
</div>

@if($partner->status === 'pending')
    <div class="modal fade" id="approvePartnerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.partners.approve', $partner) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="modal-header">
                        <h5 class="modal-title">Approve application</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-muted">Choose which channels should receive the approval notice for {{ $partner->name }}.</p>

                        <div class="d-grid gap-2">
                            <label class="form-check">
                                <input type="checkbox" name="notify_via[]" value="mail" class="form-check-input" checked>
                                <span class="form-check-label">Send email approval</span>
                            </label>
                            <label class="form-check">
                                <input type="checkbox" name="notify_via[]" value="twilio" class="form-check-input">
                                <span class="form-check-label">Send SMS approval</span>
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="surface-button-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="surface-button-primary">Approve partner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
