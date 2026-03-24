@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')
@section('page_kicker', 'Phase 5 Admin Console')
@section('page_subtitle', 'Coordinate content, people, learning, and support workflows from one responsive back-office workspace.')

@section('content')
<div class="d-grid gap-4">
    <section class="admin-dashboard-hero">
        <div class="row g-4 align-items-end">
            <div class="col-xl-7">
                <span class="surface-eyebrow bg-white/10 text-white border-0">Operations overview</span>
                <h2 class="mt-3 mb-3">Run the ministry workspace with clearer priorities, faster handoffs, and fewer hidden queues.</h2>
                <p class="mb-4 text-white-50">
                    The admin workspace now starts from the jobs that need attention first: approvals, publishing, learner operations,
                    and support follow-through. Use the workflow hubs below to move straight into the surfaces that match your role.
                </p>

                @if($quickActions->isNotEmpty())
                    <div class="admin-dashboard-actions">
                        @foreach($quickActions->take(3) as $action)
                            <a href="{{ $action['url'] }}" class="{{ $loop->first ? 'btn btn-light rounded-pill px-4' : 'surface-button-ghost text-white' }}">
                                @if(! empty($action['icon']))
                                    <i class="{{ $action['icon'] }}"></i>
                                @endif
                                <span>{{ $action['label'] }}</span>
                                @if(! empty($action['badge']))
                                    <span class="badge rounded-pill bg-dark-subtle text-dark">{{ $action['badge'] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="mb-0 text-white-50 small">Your current role has dashboard access, but no additional workflow links have been assigned yet.</p>
                @endif
            </div>

            <div class="col-xl-5">
                <div class="admin-dashboard-pulse">
                    @foreach($operatorPulse as $pulse)
                        <article class="admin-dashboard-pulse-card">
                            <span class="label">{{ $pulse['label'] }}</span>
                            <span class="value">{{ $pulse['value'] }}</span>
                            <p class="mb-0 text-white-50 small">{{ $pulse['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="admin-metric-grid">
        <article class="admin-metric-card">
            <span class="label">Total users</span>
            <span class="value">{{ number_format($stats['total_users']) }}</span>
            <p class="mb-0 text-muted">Accounts currently stored across the ministry platform.</p>
        </article>
        <article class="admin-metric-card">
            <span class="label">Active users</span>
            <span class="value">{{ number_format($stats['active_users']) }}</span>
            <p class="mb-0 text-muted">Verified users available for ongoing ministry engagement.</p>
        </article>
        <article class="admin-metric-card">
            <span class="label">New users today</span>
            <span class="value">{{ number_format($stats['new_users_today']) }}</span>
            <p class="mb-0 text-muted">Fresh registrations that landed in the last 24 hours.</p>
        </article>
        <article class="admin-metric-card">
            <span class="label">ASOM students</span>
            <span class="value">{{ number_format($stats['asom_students']) }}</span>
            <p class="mb-0 text-muted">Learners attached to the school of ministry journey.</p>
        </article>
        <article class="admin-metric-card">
            <span class="label">Certificates</span>
            <span class="value">{{ number_format($stats['total_certificates']) }}</span>
            <p class="mb-0 text-muted">Issued and pending learner certificates on file.</p>
        </article>
    </section>

    <section class="row g-4">
        <div class="col-xl-8">
            <div class="admin-dashboard-panel" id="dashboard-workflows">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h3 class="mb-1">Workflow hubs</h3>
                        <p class="text-muted mb-0">Open the surfaces available to your role without hunting through the full navigation tree.</p>
                    </div>
                </div>

                @if($workflowSections->isNotEmpty())
                    <div class="admin-workflow-grid">
                        @foreach($workflowSections as $section)
                            <article class="admin-workflow-card admin-workflow-card-{{ $section['accent'] }}">
                                <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="admin-workflow-icon">
                                            <i class="{{ $section['icon'] }}"></i>
                                        </span>
                                        <div>
                                            <h4 class="h5 mb-1">{{ $section['label'] }}</h4>
                                            <p class="text-muted mb-0 small">{{ $section['description'] }}</p>
                                        </div>
                                    </div>
                                    <span class="admin-workflow-count">{{ $section['action_count'] }}</span>
                                </div>

                                <div class="d-grid gap-2">
                                    @foreach($section['actions'] as $action)
                                        <a href="{{ $action['url'] }}" class="admin-workflow-link" data-admin-nav-link>
                                            <span class="d-flex align-items-center gap-2">
                                                @if(! empty($action['icon']))
                                                    <i class="{{ $action['icon'] }}"></i>
                                                @endif
                                                <span>{{ $action['label'] }}</span>
                                            </span>
                                            @if(! empty($action['badge']))
                                                <span class="badge rounded-pill bg-warning text-dark">{{ $action['badge'] }}</span>
                                            @else
                                                <i class="bi bi-arrow-up-right-circle text-muted"></i>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state
                        title="No workflow hubs assigned yet"
                        message="Additional operational areas will appear here when this role is granted more module access."
                    />
                @endif
            </div>
        </div>

        <div class="col-xl-4">
            <div class="d-grid gap-4">
                <div class="admin-dashboard-panel">
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                        <div>
                            <h3 class="mb-1">Attention queue</h3>
                            <p class="text-muted mb-0">Items that currently need a person to act.</p>
                        </div>
                    </div>

                    @if($attentionItems->isNotEmpty())
                        <div class="d-grid gap-3">
                            @foreach($attentionItems as $item)
                                <a href="{{ $item['route'] }}" class="admin-attention-item admin-attention-item-{{ $item['tone'] }}">
                                    <span class="admin-attention-icon">
                                        <i class="{{ $item['icon'] }}"></i>
                                    </span>
                                    <span class="flex-grow-1">
                                        <strong class="d-block">{{ $item['label'] }}</strong>
                                        <small>{{ $item['description'] }}</small>
                                    </span>
                                    <span class="admin-attention-count">{{ $item['count'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <x-ui.empty-state
                            title="No urgent queue right now"
                            message="The items tied to your role are currently cleared."
                        />
                    @endif
                </div>

                <div class="admin-dashboard-panel">
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                        <div>
                            <h3 class="mb-1">Quick actions</h3>
                            <p class="text-muted mb-0">Jump directly into the next admin task.</p>
                        </div>
                    </div>

                    @if($quickActions->isNotEmpty())
                        <div class="d-grid gap-2">
                            @foreach($quickActions as $action)
                                <a href="{{ $action['url'] }}" class="admin-quick-action" data-admin-nav-link>
                                    <span class="d-flex align-items-center gap-2">
                                        @if(! empty($action['icon']))
                                            <i class="{{ $action['icon'] }}"></i>
                                        @endif
                                        <span>{{ $action['label'] }}</span>
                                    </span>
                                    @if(! empty($action['badge']))
                                        <span class="badge rounded-pill bg-warning text-dark">{{ $action['badge'] }}</span>
                                    @else
                                        <i class="bi bi-arrow-up-right-circle text-muted"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @else
                        <x-ui.empty-state
                            title="No quick actions yet"
                            message="As more tools are assigned to this role, the most useful jump points will appear here."
                        />
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="row g-4">
        <div class="col-xl-6">
            <div class="admin-dashboard-panel h-100">
                <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h3 class="mb-1">Recent content activity</h3>
                        <p class="text-muted mb-0">Latest publishing movement across the ministry surface.</p>
                    </div>
                    @if($canViewContentWorkflow)
                        <a href="{{ route('admin.posts.index') }}" class="surface-button-secondary">Open posts</a>
                    @endif
                </div>

                @if(! $canViewContentWorkflow)
                    <x-ui.empty-state
                        title="Content tools are not assigned to this role"
                        message="Recent publishing activity will appear here when content access is granted."
                    />
                @elseif($recentActivity->isNotEmpty())
                    <div class="admin-activity-list">
                        @foreach($recentActivity as $activity)
                            <article class="admin-activity-item">
                                <div class="admin-activity-dot"></div>
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                                        <div>
                                            <h4 class="h6 mb-1">{{ $activity->title }}</h4>
                                            <p class="text-muted mb-0 small">Created by {{ $activity->author?->name ?? 'System' }}</p>
                                        </div>
                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state
                        title="No recent post activity"
                        message="As new content is created or updated, it will appear here."
                    />
                @endif
            </div>
        </div>

        <div class="col-xl-6">
            <div class="admin-dashboard-panel h-100">
                <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h3 class="mb-1">Reminders and events</h3>
                        <p class="text-muted mb-0">Upcoming calendar items and reminder status at a glance.</p>
                    </div>
                    @if($canManageReminders)
                        <a href="{{ route('admin.notification-settings.event-reminders') }}" class="surface-button-secondary">Reminder settings</a>
                    @endif
                </div>

                @if(! $canManageReminders)
                    <x-ui.empty-state
                        title="Reminder management is not assigned"
                        message="Upcoming event reminder controls appear here for operators with notification access."
                    />
                @elseif($upcomingEvents->isNotEmpty())
                    <div class="d-grid gap-3">
                        @foreach($upcomingEvents as $event)
                            <article class="admin-event-card">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                    <div>
                                        <h4 class="h6 mb-1">{{ $event->title }}</h4>
                                        <p class="text-muted mb-0 small">
                                            {{ $event->date ? \Carbon\Carbon::parse($event->date)->format('M j, Y') : 'Date not set' }}
                                        </p>
                                    </div>
                                    <span class="admin-status-pill {{ $event->reminderLogs->count() > 0 ? 'is-success' : 'is-warning' }}">
                                        {{ $event->reminderLogs->count() > 0 ? 'Sent' : 'Pending' }}
                                    </span>
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <small class="text-muted">{{ $event->reminderLogs->sum('recipients_count') }} recipients reached</small>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button
                                            type="button"
                                            class="surface-button-ghost"
                                            data-dashboard-preview-reminder
                                            data-preview-url="{{ route('admin.notification-settings.event-reminders.preview', $event) }}"
                                        >
                                            Preview
                                        </button>
                                        <button
                                            type="button"
                                            class="surface-button-secondary"
                                            data-dashboard-send-reminder
                                            data-send-url="{{ route('admin.notification-settings.event-reminders.send', $event) }}"
                                        >
                                            Send now
                                        </button>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state
                        title="No upcoming events"
                        message="When events are scheduled, reminder actions will appear here."
                    />
                @endif
            </div>
        </div>
    </section>

    <section class="admin-dashboard-panel" id="dashboard-celebrations">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="mb-1">Today's celebrations</h3>
                <p class="text-muted mb-0">Track birthdays and anniversaries that may need a ministry response.</p>
            </div>
            @if($canViewCelebrations)
                <div class="btn-group" role="group" aria-label="Celebration filters">
                    <button type="button" class="btn btn-outline-secondary active" data-dashboard-celebration-filter="all">All</button>
                    <button type="button" class="btn btn-outline-secondary" data-dashboard-celebration-filter="birthday">Birthdays</button>
                    <button type="button" class="btn btn-outline-secondary" data-dashboard-celebration-filter="wedding">Anniversaries</button>
                </div>
            @endif
        </div>

        @if(! $canViewCelebrations)
            <x-ui.empty-state
                title="Celebration reporting is not assigned"
                message="Birthday and anniversary tracking appears here for operators with reporting access."
            />
        @elseif($todayCelebrants->isNotEmpty())
            <div class="table-responsive" data-dashboard-celebrations>
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Celebration</th>
                            <th>Years</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todayCelebrants as $celebrant)
                            <tr data-celebration-row="{{ $celebrant->celebration_type }}">
                                <td>
                                    <strong>{{ $celebrant->name }}</strong>
                                </td>
                                <td>{{ $celebrant->celebration_type === 'birthday' ? 'Birthday' : 'Wedding anniversary' }}</td>
                                <td>{{ $celebrant->years }} years</td>
                                <td class="text-end">
                                    <button
                                        type="button"
                                        class="surface-button-secondary"
                                        data-dashboard-send-wishes
                                        data-send-wishes-url="{{ route('admin.dashboard.send-wishes', $celebrant->id) }}"
                                    >
                                        Send wishes
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <x-ui.empty-state
                title="No celebrations today"
                message="When birthdays or anniversaries land on today's date, they will appear here."
            />
        @endif
    </section>
</div>

<div class="modal fade" id="reminderPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reminder Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="preview-tabs">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#emailPreviewPane" type="button" role="tab">Email</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#databasePreviewPane" type="button" role="tab">In-App</button>
                        </li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="emailPreviewPane" role="tabpanel">
                            <div class="email-preview-content"></div>
                        </div>
                        <div class="tab-pane fade" id="databasePreviewPane" role="tabpanel">
                            <div class="notification-preview-content"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
