@extends('admin.layouts.app')

@section('title', 'Forms')
@section('page_kicker', 'Forms And Intake')
@section('page_subtitle', 'Create, monitor, and export ministry forms from one shared operations workspace.')

@section('content')
<div class="admin-page-shell">
    <section class="admin-stat-grid">
        <article class="admin-stat-card">
            <span class="admin-stat-label">Forms</span>
            <strong class="admin-stat-value">{{ number_format($summary['forms']) }}</strong>
            <p class="admin-stat-note mb-0">Active form definitions currently available to operators.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Public forms</span>
            <strong class="admin-stat-value">{{ number_format($summary['public_forms']) }}</strong>
            <p class="admin-stat-note mb-0">Forms available to unauthenticated visitors.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Login-gated</span>
            <strong class="admin-stat-value">{{ number_format($summary['gated_forms']) }}</strong>
            <p class="admin-stat-note mb-0">Forms that require members to sign in first.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Submissions</span>
            <strong class="admin-stat-value">{{ number_format($summary['submissions']) }}</strong>
            <p class="admin-stat-note mb-0">Total responses collected across every form.</p>
        </article>
    </section>

    <x-ui.panel title="Forms Management" subtitle="Open, edit, or export the forms that currently power your ministry workflows.">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <a href="{{ route('admin.forms.create') }}" class="surface-button-primary">Create new form</a>
            <a href="{{ route('admin.submissions.index') }}" class="surface-button-secondary">View all submissions</a>
        </div>

        @if($forms->isNotEmpty())
            <div class="surface-table-shell">
                <table class="table admin-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Form</th>
                            <th>Access</th>
                            <th>Fields</th>
                            <th>Submissions</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($forms as $form)
                            <tr>
                                <td>
                                    <strong class="d-block">{{ $form->title ?: 'Untitled form' }}</strong>
                                    <small class="text-muted">{{ Str::limit($form->description ?: 'No description provided.', 90) }}</small>
                                </td>
                                <td>
                                    <span class="admin-status-chip {{ $form->require_login ? 'is-warning' : 'is-success' }}">
                                        {{ $form->require_login ? 'Login required' : 'Public' }}
                                    </span>
                                </td>
                                <td>{{ count($form->fields ?? []) }}</td>
                                <td>{{ number_format($form->submissions_count) }}</td>
                                <td>
                                    <strong class="d-block">{{ $form->created_at->format('M j, Y') }}</strong>
                                    <small class="text-muted">{{ $form->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="admin-action-row justify-content-end">
                                        <a href="{{ route('forms.show', $form) }}" class="btn btn-sm btn-outline-secondary" target="_blank">Preview</a>
                                        <a href="{{ route('admin.forms.edit', $form) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="{{ route('admin.forms.submissions', $form) }}" class="btn btn-sm btn-outline-success">Submissions</a>
                                        <form action="{{ route('admin.forms.destroy', $form) }}" method="POST" class="d-inline-flex" data-admin-confirm="Delete this form and every submission attached to it?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $forms->links() }}
            </div>
        @else
            <x-ui.empty-state
                title="No forms have been created yet"
                message="Start with a new form to begin collecting ministry responses."
                icon="bi bi-ui-checks-grid"
            >
                <x-slot:actions>
                    <a href="{{ route('admin.forms.create') }}" class="surface-button-primary">Create first form</a>
                </x-slot:actions>
            </x-ui.empty-state>
        @endif
    </x-ui.panel>
</div>
@endsection
