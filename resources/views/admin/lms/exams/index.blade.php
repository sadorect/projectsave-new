@extends('admin.layouts.app')

@section('title', 'Exam Operations')
@section('page_kicker', 'Learning Operations')
@section('page_subtitle', 'Manage assessment readiness, question volume, activation, and manual-pass workflows from one LMS operations queue.')

@section('content')
<div class="admin-page-shell">
    <section class="admin-stat-grid">
        <article class="admin-stat-card">
            <span class="admin-stat-label">Exams</span>
            <strong class="admin-stat-value">{{ number_format($summary['total']) }}</strong>
            <p class="admin-stat-note mb-0">Assessment definitions currently available in the LMS.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Active</span>
            <strong class="admin-stat-value">{{ number_format($summary['active']) }}</strong>
            <p class="admin-stat-note mb-0">Exams available to students right now.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Inactive</span>
            <strong class="admin-stat-value">{{ number_format($summary['inactive']) }}</strong>
            <p class="admin-stat-note mb-0">Exams staged but not yet available to students.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Question bank</span>
            <strong class="admin-stat-value">{{ number_format($summary['questions']) }}</strong>
            <p class="admin-stat-note mb-0">Questions currently attached across all exams.</p>
        </article>
    </section>

    <x-ui.panel title="Exam Queue" subtitle="Open, activate, refine, or route into attempts and manual-pass workflows.">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <a href="{{ route('admin.exams.create') }}" class="surface-button-primary">Create new exam</a>
            <a href="{{ route('admin.exam-attempts.index') }}" class="surface-button-secondary">View all attempts</a>
        </div>

        @if($exams->isNotEmpty())
            <div class="surface-table-shell">
                <table class="table admin-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Exam</th>
                            <th>Course</th>
                            <th>Duration</th>
                            <th>Questions</th>
                            <th>Attempts</th>
                            <th>Passing score</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exams as $exam)
                            <tr>
                                <td>
                                    <strong class="d-block">{{ $exam->title }}</strong>
                                    <small class="text-muted">{{ $exam->description ?: 'No description provided' }}</small>
                                </td>
                                <td>{{ $exam->course->title ?? 'No course assigned' }}</td>
                                <td>{{ $exam->duration_minutes }} mins</td>
                                <td>{{ number_format($exam->questions_count) }}</td>
                                <td>{{ number_format($exam->attempts_count) }}</td>
                                <td>{{ $exam->passing_score }}%</td>
                                <td>
                                    <span class="admin-status-chip {{ $exam->is_active ? 'is-success' : 'is-muted' }}">
                                        {{ $exam->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="admin-action-row justify-content-end">
                                        <a href="{{ route('admin.exams.preview', $exam) }}" class="btn btn-sm btn-outline-secondary">Preview</a>
                                        <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="{{ route('admin.questions.create', $exam) }}" class="btn btn-sm btn-outline-success">Questions</a>
                                        <a href="{{ route('admin.exams.manual-pass', $exam) }}" class="btn btn-sm btn-outline-warning">Manual pass</a>
                                        <form action="{{ route('admin.exams.toggle-activation', $exam) }}" method="POST" class="d-inline-flex">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $exam->is_active ? 'btn-success' : 'btn-outline-secondary' }}">
                                                {{ $exam->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="d-inline-flex" data-admin-confirm="Delete this exam and every question attached to it?">
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
                {{ $exams->links() }}
            </div>
        @else
            <x-ui.empty-state
                title="No exams created yet"
                message="Create the first exam to begin building the assessment path for your courses."
                icon="bi bi-journal-check"
            >
                <x-slot:actions>
                    <a href="{{ route('admin.exams.create') }}" class="surface-button-primary">Create first exam</a>
                </x-slot:actions>
            </x-ui.empty-state>
        @endif
    </x-ui.panel>
</div>
@endsection
