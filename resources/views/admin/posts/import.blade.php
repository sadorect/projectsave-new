@extends('admin.layouts.app')

@section('title', 'Import Devotionals')
@section('page_subtitle', 'Queue Projectsave devotional archive imports, monitor progress, and resume safely from the last saved checkpoint if a worker stops mid-run.')

@section('content')
<div class="container-fluid">
    @php
        $statusClasses = [
            'queued' => 'bg-secondary-subtle text-secondary',
            'processing' => 'bg-primary-subtle text-primary',
            'completed' => 'bg-success-subtle text-success',
            'failed' => 'bg-danger-subtle text-danger',
        ];
    @endphp

    <form action="{{ route('admin.posts.import.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1">Import Projectsave Devotionals</h1>
                <p class="text-muted mb-0">Imports now run in the background, preserve original publish dates, and save progress after every devotional.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">Back to posts</a>
                <button type="submit" class="btn btn-primary">Queue import</button>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <x-ui.panel title="Source File" subtitle="Upload either the extracted devotional file or the original WhatsApp export. Only message blocks that start with Projectsave Devotional are imported.">
                    <div class="mb-4">
                        <label for="source_file" class="form-label">Devotional source file</label>
                        <input
                            id="source_file"
                            type="file"
                            name="source_file"
                            accept=".txt"
                            class="form-control @error('source_file') is-invalid @enderror"
                            required
                        >
                        @error('source_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Accepted format: plain-text `.txt` files up to 20MB. Server upload limit: <strong>{{ ini_get('upload_max_filesize') }}</strong>.</div>
                    </div>

                    <div class="mb-0">
                        <label for="duplicate_strategy" class="form-label">If a devotional already exists</label>
                        <select id="duplicate_strategy" name="duplicate_strategy" class="form-select @error('duplicate_strategy') is-invalid @enderror">
                            <option value="update" @selected(old('duplicate_strategy', 'update') === 'update')>Update existing post (Recommended)</option>
                            <option value="skip" @selected(old('duplicate_strategy') === 'skip')>Skip existing post</option>
                        </select>
                        @error('duplicate_strategy')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </x-ui.panel>
            </div>

            <div class="col-lg-4">
                <div class="d-flex flex-column gap-4">
                    <x-ui.panel title="Queue Workflow" subtitle="This importer no longer depends on a single browser request finishing the full archive.">
                        <ul class="mb-0 ps-3 text-muted small">
                            <li>The uploaded source file is stored locally and processed in queued chunks.</li>
                            <li>Each devotional updates the checkpoint, so a resumed run continues from the last saved index.</li>
                            <li>A queue worker must be running for queued imports to advance beyond the initial pending state.</li>
                            <li>Current queue connection: <strong>{{ $queueConnection }}</strong>.</li>
                        </ul>

                        @if($queueConnection === 'sync')
                            <div class="alert alert-warning mt-3 mb-0">
                                This environment is using the <code>sync</code> queue driver, so imports will still run inline until the queue connection is changed.
                            </div>
                        @endif
                    </x-ui.panel>

                    <x-ui.panel title="Import Rules" subtitle="How entries are mapped into the current devotional schema.">
                        <ul class="mb-0 ps-3 text-muted small">
                            <li>The original WhatsApp timestamp becomes the post <code>published_at</code> value.</li>
                            <li>The sender name becomes the post author.</li>
                            <li>The first scripture block becomes <code>bible_text</code> and its reference populates <code>scripture</code> when detectable.</li>
                            <li>If an entry has no action point, the trailing prayer block is used as <code>action_point</code>.</li>
                            <li>Categories are suggested from the devotional title and body, then reused or created automatically.</li>
                            <li>Imported historical posts are marked as already sent so subscribers are not emailed old archives.</li>
                        </ul>
                    </x-ui.panel>

                    <x-ui.panel title="Category Suggestions" subtitle="Current keyword-based categories the importer can assign.">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['Prayer', 'Marriage & Relationships', 'Purpose & Calling', 'Missions & Evangelism', 'Holiness & Purity', 'Family & Parenting', 'Wisdom & Discernment', 'Faith & Trust', 'Ministry & Leadership', 'Stewardship & Work', 'Eternity & Hope', 'Gratitude & Praise', 'Christian Living'] as $categoryLabel)
                                <span class="badge bg-light text-dark border">{{ $categoryLabel }}</span>
                            @endforeach
                        </div>
                    </x-ui.panel>
                </div>
            </div>
        </div>
    </form>

    @if($selectedSession)
        @php
            $progressWidth = $selectedSession->progressPercentage();
            $showProgressBar = ($selectedSession->total_entries ?? 0) > 0;
        @endphp

        <div class="mt-4">
            <x-ui.panel title="Selected Import Session" subtitle="Open any recent session below to inspect progress, failures, categories created, and retry actions.">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge {{ $statusClasses[$selectedSession->status] ?? 'bg-light text-dark' }}">{{ ucfirst($selectedSession->status) }}</span>
                            @if($selectedSession->isStale())
                                <span class="badge bg-warning-subtle text-warning">Stale</span>
                            @endif
                            <span class="text-muted small">Queued {{ optional($selectedSession->queued_at)->diffForHumans() ?? 'just now' }}</span>
                        </div>
                        <div class="fw-semibold">{{ $selectedSession->source_filename }}</div>
                        <div class="text-muted small">
                            Duplicate strategy: <strong>{{ $selectedSession->duplicate_strategy }}</strong>
                            @if($selectedSession->user)
                                · queued by {{ $selectedSession->user->name }}
                            @endif
                            @if($selectedSession->last_activity_at)
                                · last activity {{ $selectedSession->last_activity_at->diffForHumans() }}
                            @endif
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.posts.import.create', ['session' => $selectedSession->getKey()]) }}" class="btn btn-outline-secondary">Refresh status</a>

                        @if($selectedSession->status === 'failed' || $selectedSession->isStale())
                            <form action="{{ route('admin.posts.import.resume', $selectedSession) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    {{ $selectedSession->status === 'failed' ? 'Resume from checkpoint' : 'Re-queue session' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if($showProgressBar)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between small text-muted mb-2">
                            <span>Progress</span>
                            <span>{{ number_format($selectedSession->processed_entries) }} / {{ number_format($selectedSession->total_entries) }} devotionals</span>
                        </div>
                        <div class="progress" role="progressbar" aria-valuenow="{{ $progressWidth }}" aria-valuemin="0" aria-valuemax="100" style="height: 0.75rem;">
                            <div
                                class="progress-bar {{ $selectedSession->status === 'completed' ? 'bg-success' : ($selectedSession->status === 'failed' ? 'bg-danger' : 'bg-primary') }}"
                                style="width: {{ max($progressWidth, $progressWidth > 0 ? 6 : 0) }}%;"
                            ></div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mb-4">
                        The worker has not counted the matching devotional entries yet. Refresh after the job starts to see the detected total.
                    </div>
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-md-2 col-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted small">Detected</div>
                            <div class="fs-4 fw-semibold">{{ $selectedSession->total_entries ? number_format($selectedSession->total_entries) : '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted small">Created</div>
                            <div class="fs-4 fw-semibold text-success">{{ number_format($selectedSession->created_count) }}</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted small">Updated</div>
                            <div class="fs-4 fw-semibold text-primary">{{ number_format($selectedSession->updated_count) }}</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted small">Skipped</div>
                            <div class="fs-4 fw-semibold text-secondary">{{ number_format($selectedSession->skipped_count) }}</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted small">Failed</div>
                            <div class="fs-4 fw-semibold text-danger">{{ number_format($selectedSession->failed_count) }}</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted small">Checkpoint</div>
                            <div class="fs-4 fw-semibold">{{ $selectedSession->last_processed_index >= 0 ? number_format($selectedSession->last_processed_index + 1) : '0' }}</div>
                        </div>
                    </div>
                </div>

                @if($selectedSession->last_error)
                    <div class="alert alert-danger mb-4">
                        <div class="fw-semibold mb-1">Last session error</div>
                        <div class="small">{{ $selectedSession->last_error }}</div>
                    </div>
                @endif

                @if(!empty($selectedSession->created_categories))
                    <div class="mb-4">
                        <div class="small text-muted mb-2">Categories created during this import</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($selectedSession->created_categories as $createdCategory)
                                <span class="badge bg-light text-dark border">{{ $createdCategory }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty($selectedSession->category_counts))
                    <div class="mb-4">
                        <div class="small text-muted mb-2">Assigned categories so far</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(collect($selectedSession->category_counts)->sortKeys() as $categoryName => $count)
                                <span class="badge bg-primary-subtle text-primary">{{ $categoryName }} ({{ $count }})</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty($selectedSession->failures))
                    <div>
                        <div class="small text-muted mb-2">Recorded failures</div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Published</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(collect($selectedSession->failures)->take(15) as $failure)
                                        <tr>
                                            <td>{{ ($failure['index'] ?? 0) + 1 }}</td>
                                            <td>{{ $failure['title'] ?? 'Untitled devotional' }}</td>
                                            <td>{{ $failure['published_at'] ?? 'Unknown' }}</td>
                                            <td class="text-danger">{{ $failure['reason'] ?? 'Unknown error' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(count($selectedSession->failures) > 15)
                            <div class="small text-muted mt-2">Showing the first 15 recorded failures.</div>
                        @endif
                    </div>
                @endif
            </x-ui.panel>
        </div>
    @endif

    <div class="mt-4">
        <x-ui.panel title="Recent Import Sessions" subtitle="Use this queue to reopen a recent run, monitor its progress, or resume a stalled archive import.">
            @if($recentSessions->isEmpty())
                <div class="text-muted">No devotional imports have been queued yet.</div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Started</th>
                                <th>Source File</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Results</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSessions as $session)
                                @php
                                    $rowStatusClass = $statusClasses[$session->status] ?? 'bg-light text-dark';
                                @endphp
                                <tr @class(['table-primary' => $selectedSession && $session->is($selectedSession)])>
                                    <td class="small text-muted">
                                        <div>{{ optional($session->created_at)->format('Y-m-d H:i') }}</div>
                                        <div>{{ optional($session->created_at)->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $session->source_filename }}</div>
                                        <div class="small text-muted">Strategy: {{ $session->duplicate_strategy }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                            <span class="badge {{ $rowStatusClass }}">{{ ucfirst($session->status) }}</span>
                                            @if($session->isStale())
                                                <span class="badge bg-warning-subtle text-warning">Stale</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="small">
                                        @if(($session->total_entries ?? 0) > 0)
                                            {{ number_format($session->processed_entries) }} / {{ number_format($session->total_entries) }}
                                            <div class="text-muted">{{ $session->progressPercentage() }}%</div>
                                        @else
                                            <span class="text-muted">Waiting for worker</span>
                                        @endif
                                    </td>
                                    <td class="small">
                                        <div class="text-success">Created: {{ number_format($session->created_count) }}</div>
                                        <div class="text-primary">Updated: {{ number_format($session->updated_count) }}</div>
                                        <div class="text-danger">Failed: {{ number_format($session->failed_count) }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.posts.import.create', ['session' => $session->getKey()]) }}" class="btn btn-sm btn-outline-secondary">Open</a>

                                            @if($session->status === 'failed' || $session->isStale())
                                                <form action="{{ route('admin.posts.import.resume', $session) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary">Resume</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.panel>
    </div>
</div>
@endsection

@if($hasActiveSessions)
    @push('scripts')
    <script>
        window.setTimeout(function () {
            const url = new URL(window.location.href);
            @if($selectedSession)
                url.searchParams.set('session', '{{ $selectedSession->getKey() }}');
            @endif
            window.location.replace(url.toString());
        }, 15000);
    </script>
    @endpush
@endif
