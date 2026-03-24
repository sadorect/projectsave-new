@extends('admin.layouts.app')

@section('title', 'Form Submissions')
@section('page_kicker', 'Forms And Intake')
@section('page_subtitle', 'Monitor every response arriving through ministry forms and jump into the related form context when needed.')

@section('content')
<div class="admin-page-shell">
    <section class="admin-stat-grid">
        <article class="admin-stat-card">
            <span class="admin-stat-label">All submissions</span>
            <strong class="admin-stat-value">{{ number_format($summary['total']) }}</strong>
            <p class="admin-stat-note mb-0">Responses currently stored across every admin-managed form.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Received today</span>
            <strong class="admin-stat-value">{{ number_format($summary['today']) }}</strong>
            <p class="admin-stat-note mb-0">Fresh responses that landed in the last 24 hours.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Member responses</span>
            <strong class="admin-stat-value">{{ number_format($summary['members']) }}</strong>
            <p class="admin-stat-note mb-0">Submissions tied to signed-in users.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Guest responses</span>
            <strong class="admin-stat-value">{{ number_format($summary['guests']) }}</strong>
            <p class="admin-stat-note mb-0">Submissions received without a member account.</p>
        </article>
    </section>

    <x-ui.panel title="Submission Feed" subtitle="Review incoming form traffic and open the owning form for deeper response management.">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <a href="{{ route('admin.forms.index') }}" class="surface-button-secondary">Back to forms</a>
        </div>

        @if($submissions->isNotEmpty())
            <div class="surface-table-shell">
                <table class="table admin-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Form</th>
                            <th>Submitted by</th>
                            <th>Received</th>
                            <th>Payload</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $submission)
                            <tr>
                                <td>
                                    <strong class="d-block">{{ $submission->form->title ?? 'Unknown form' }}</strong>
                                    <small class="text-muted">Form ID {{ $submission->form_id }}</small>
                                </td>
                                <td>
                                    @if($submission->user)
                                        <strong class="d-block">{{ $submission->user->name }}</strong>
                                        <small class="text-muted">{{ $submission->user->email }}</small>
                                    @else
                                        <span class="admin-status-chip is-muted">Guest user</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="d-block">{{ $submission->created_at->format('M j, Y H:i') }}</strong>
                                    <small class="text-muted">{{ $submission->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @if(is_array($submission->data) && count($submission->data) > 0)
                                        <details class="admin-inline-details">
                                            <summary>{{ count($submission->data) }} field(s)</summary>
                                            <div class="admin-data-preview mt-2">
                                                @foreach($submission->data as $key => $value)
                                                    <div class="admin-data-preview-item">
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong>
                                                        <span>{{ is_array($value) ? implode(', ', $value) : ($value ?: 'No response') }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </details>
                                    @else
                                        <span class="text-muted">No stored payload</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($submission->form)
                                        <a href="{{ route('admin.forms.submissions', $submission->form) }}" class="btn btn-sm btn-outline-primary">Open form responses</a>
                                    @else
                                        <span class="text-muted">No form link</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $submissions->links() }}
            </div>
        @else
            <x-ui.empty-state
                title="No submissions found"
                message="Responses will appear here as soon as visitors or members begin using your forms."
                icon="bi bi-inbox"
            >
                <x-slot:actions>
                    <a href="{{ route('admin.forms.index') }}" class="surface-button-secondary">Open forms</a>
                </x-slot:actions>
            </x-ui.empty-state>
        @endif
    </x-ui.panel>
</div>
@endsection
