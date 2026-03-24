@extends('admin.layouts.app')

@section('title', 'Form Responses')
@section('page_kicker', 'Forms And Intake')
@section('page_subtitle', 'Review the responses, copy the public form link, and export the dataset for this specific form.')

@section('content')
@php($fieldLabels = collect($form->fields ?? [])->mapWithKeys(fn ($field) => [($field['name'] ?? '') => ($field['label'] ?? $field['name'] ?? 'Field')]))

<div class="admin-page-shell">
    <section class="admin-stat-grid">
        <article class="admin-stat-card">
            <span class="admin-stat-label">Responses</span>
            <strong class="admin-stat-value">{{ number_format($summary['total']) }}</strong>
            <p class="admin-stat-note mb-0">Total responses recorded for this form.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Received today</span>
            <strong class="admin-stat-value">{{ number_format($summary['today']) }}</strong>
            <p class="admin-stat-note mb-0">Fresh responses received since midnight.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Member responses</span>
            <strong class="admin-stat-value">{{ number_format($summary['members']) }}</strong>
            <p class="admin-stat-note mb-0">Responses from signed-in users.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Guest responses</span>
            <strong class="admin-stat-value">{{ number_format($summary['guests']) }}</strong>
            <p class="admin-stat-note mb-0">Responses received from non-members.</p>
        </article>
    </section>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="d-grid gap-4">
                <x-ui.panel title="Form Profile" subtitle="The current setup and response access level for this form.">
                    <div class="admin-definition-grid admin-definition-grid-single">
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Title</span>
                            <strong>{{ $form->title ?: 'Untitled form' }}</strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Description</span>
                            <strong>{{ $form->description ?: 'No description provided' }}</strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Access</span>
                            <strong>
                                <span class="admin-status-chip {{ $form->require_login ? 'is-warning' : 'is-success' }}">
                                    {{ $form->require_login ? 'Login required' : 'Public access' }}
                                </span>
                            </strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Field count</span>
                            <strong>{{ count($form->fields ?? []) }}</strong>
                        </div>
                    </div>
                </x-ui.panel>

                <x-ui.panel title="Actions" subtitle="Jump to the form editor, export responses, or copy the public intake link.">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.forms.edit', $form) }}" class="surface-button-primary">Edit form</a>
                        <a href="{{ route('admin.forms.download', $form) }}" class="surface-button-secondary">Export CSV</a>
                        <a href="{{ route('forms.show', $form) }}" target="_blank" class="surface-button-secondary">Preview public form</a>
                        <button
                            type="button"
                            class="surface-button-ghost"
                            data-admin-copy-text="{{ route('forms.show', $form) }}"
                            data-copy-success="Form link copied."
                        >
                            Copy public link
                        </button>
                    </div>
                </x-ui.panel>
            </div>
        </div>

        <div class="col-xl-8">
            <x-ui.panel title="Response Timeline" subtitle="Open individual responses to inspect the submitted payload and user context.">
                @if($submissions->isNotEmpty())
                    <div class="admin-record-list">
                        @foreach($submissions as $submission)
                            <details class="admin-record-card">
                                <summary class="admin-record-summary">
                                    <span>
                                        <strong class="d-block">Submission #{{ $submission->id }}</strong>
                                        <small class="text-muted">
                                            {{ $submission->user?->name ?? 'Guest user' }}
                                            @if($submission->user?->email)
                                                - {{ $submission->user->email }}
                                            @endif
                                        </small>
                                    </span>
                                    <span class="text-end">
                                        <strong class="d-block">{{ $submission->created_at->format('M j, Y H:i') }}</strong>
                                        <small class="text-muted">{{ $submission->created_at->diffForHumans() }}</small>
                                    </span>
                                </summary>

                                <div class="admin-record-body">
                                    <div class="admin-data-preview">
                                        @forelse($submission->data ?? [] as $key => $value)
                                            <div class="admin-data-preview-item">
                                                <strong>{{ ucfirst(str_replace('_', ' ', $fieldLabels[$key] ?? $key)) }}</strong>
                                                <span>
                                                    @if(is_array($value))
                                                        {{ implode(', ', $value) }}
                                                    @elseif(blank($value))
                                                        No response
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </span>
                                            </div>
                                        @empty
                                            <span class="text-muted">No submission data is available for this record.</span>
                                        @endforelse
                                    </div>
                                </div>
                            </details>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $submissions->links() }}
                    </div>
                @else
                    <x-ui.empty-state
                        title="No responses yet"
                        message="Share the form and new responses will appear here as they arrive."
                        icon="bi bi-inbox"
                    >
                        <x-slot:actions>
                            <a href="{{ route('forms.show', $form) }}" target="_blank" class="surface-button-secondary">Open public form</a>
                        </x-slot:actions>
                    </x-ui.empty-state>
                @endif
            </x-ui.panel>
        </div>
    </div>
</div>
@endsection
