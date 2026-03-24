@extends('admin.layouts.app')

@section('title', 'Exam Attempts')
@section('page_kicker', 'Learning Operations')
@section('page_subtitle', 'Track attempt history, review student performance, and manage exceptional records across the exam system.')

@section('content')
<div class="admin-page-shell">
    <section class="admin-stat-grid">
        <article class="admin-stat-card">
            <span class="admin-stat-label">Attempt records</span>
            <strong class="admin-stat-value">{{ number_format($summary['total']) }}</strong>
            <p class="admin-stat-note mb-0">All attempt records currently stored in the LMS.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Logged today</span>
            <strong class="admin-stat-value">{{ number_format($summary['today']) }}</strong>
            <p class="admin-stat-note mb-0">Attempts created since midnight.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Students</span>
            <strong class="admin-stat-value">{{ number_format($summary['students']) }}</strong>
            <p class="admin-stat-note mb-0">Distinct learners represented in attempt history.</p>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Exams touched</span>
            <strong class="admin-stat-value">{{ number_format($summary['exams']) }}</strong>
            <p class="admin-stat-note mb-0">Assessments that have recorded at least one attempt.</p>
        </article>
    </section>

    <x-ui.panel title="Attempt History" subtitle="Use this queue to inspect score outcomes, manual passes, and deletion actions.">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <a href="{{ route('admin.exams.index') }}" class="surface-button-secondary">Back to exams</a>
        </div>

        @if($attempts->isNotEmpty())
            <div class="surface-table-shell">
                <table class="table admin-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Exam</th>
                            <th>Score</th>
                            <th>Result</th>
                            <th>Date</th>
                            <th>Duration</th>
                            <th>Type</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attempts as $attempt)
                            @php
                                $passed = $attempt->score >= $attempt->exam->passing_score;
                                $isManual = data_get($attempt->answers, 'manual_pass') === true;
                            @endphp
                            <tr>
                                <td>
                                    <strong class="d-block">{{ $attempt->user->name }}</strong>
                                    <small class="text-muted">{{ $attempt->user->email }}</small>
                                </td>
                                <td>
                                    <strong class="d-block">{{ $attempt->exam->title }}</strong>
                                    <small class="text-muted">{{ $attempt->exam->course->title ?? 'No course assigned' }}</small>
                                </td>
                                <td>{{ number_format($attempt->score, 1) }}%</td>
                                <td>
                                    <span class="admin-status-chip {{ $passed ? 'is-success' : 'is-danger' }}">
                                        {{ $passed ? 'Passed' : 'Failed' }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="d-block">{{ $attempt->started_at?->format('M j, Y H:i') ?? 'n/a' }}</strong>
                                    <small class="text-muted">{{ $attempt->started_at?->diffForHumans() ?? 'No start time' }}</small>
                                </td>
                                <td>
                                    @if($attempt->completed_at && $attempt->started_at)
                                        {{ $attempt->started_at->diffInMinutes($attempt->completed_at) }}m
                                    @else
                                        <span class="text-muted">n/a</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="admin-status-chip {{ $isManual ? 'is-info' : 'is-muted' }}">
                                        {{ $isManual ? 'Manual pass' : 'Exam' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="admin-action-row justify-content-end">
                                        <a href="{{ route('admin.exam-attempts.show', $attempt) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        <form action="{{ route('admin.exam-attempts.destroy', $attempt) }}" method="POST" class="d-inline-flex" data-admin-confirm="Delete this exam attempt record?">
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
                {{ $attempts->links() }}
            </div>
        @else
            <x-ui.empty-state
                title="No exam attempts found"
                message="Attempt history will appear here once students start taking assessments."
                icon="bi bi-clipboard-data"
            >
                <x-slot:actions>
                    <a href="{{ route('admin.exams.index') }}" class="surface-button-secondary">Open exams</a>
                </x-slot:actions>
            </x-ui.empty-state>
        @endif
    </x-ui.panel>
</div>
@endsection
