@extends('admin.layouts.app')

@section('title', 'Mail Delivery')
@section('page_kicker', 'Communications')
@section('page_subtitle', 'Send focused ministry updates to students, partners, and prayer-force contacts through one guided mail workflow.')

@section('content')
<div class="admin-page-shell">
    <section class="admin-stat-grid">
        @foreach($groups as $group)
            <article class="admin-stat-card">
                <span class="admin-stat-label">{{ $group['label'] }}</span>
                <strong class="admin-stat-value">{{ number_format($group['count']) }}</strong>
                <p class="admin-stat-note mb-0">{{ $group['description'] }}</p>
            </article>
        @endforeach
    </section>

    <div class="row g-4">
        <div class="col-xl-8">
            <x-ui.panel title="Compose Delivery" subtitle="Choose a template, select recipients, and preview the message before queueing it for delivery.">
                <form action="{{ route('admin.mail.send') }}" method="POST" class="d-grid gap-4">
                    @csrf

                    <label class="admin-field">
                        <span class="admin-field-label">Recipients</span>
                        <select name="recipients[]" class="form-select" multiple size="12" required>
                            <optgroup label="Groups">
                                @foreach($groups as $group)
                                    <option value="{{ $group['value'] }}" @selected(collect(old('recipients', []))->contains($group['value']))>
                                        {{ $group['label'] }} ({{ number_format($group['count']) }})
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Individual members">
                                @foreach($users as $user)
                                    <option value="user:{{ $user->id }}" @selected(collect(old('recipients', []))->contains('user:' . $user->id))>
                                        {{ $user->name }} - {{ $user->email }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                        <small class="text-muted">Use Ctrl/Cmd + click to choose multiple entries.</small>
                    </label>

                    <label class="admin-field">
                        <span class="admin-field-label">Template</span>
                        <select name="template_id" class="form-select" required>
                            <option value="">Select a template</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" @selected(old('template_id') == $template->id)>
                                    {{ $template->name }} - {{ $template->subject }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="admin-field">
                        <span class="admin-field-label">Custom message</span>
                        <textarea name="custom_message" rows="8" class="form-control" placeholder="Optional note to append beneath the template body.">{{ old('custom_message') }}</textarea>
                    </label>

                    <div class="admin-action-row">
                        <button
                            type="submit"
                            class="surface-button-secondary"
                            formaction="{{ route('admin.mail.preview') }}"
                            formtarget="_blank"
                        >
                            Preview delivery
                        </button>
                        <button type="submit" class="surface-button-primary">Queue email</button>
                    </div>
                </form>
            </x-ui.panel>
        </div>

        <div class="col-xl-4">
            <div class="d-grid gap-4">
                <x-ui.panel title="Workflow Note" subtitle="Preview uses the first selected recipient context for merge values like name or course.">
                    <p class="text-muted mb-0">
                        If you select a full group, the preview will still help you validate the template structure before queuing the delivery
                        to the wider audience. Use custom text only when you need a one-off note appended beneath the template.
                    </p>
                </x-ui.panel>

                <x-ui.panel title="Available Templates" subtitle="These are the saved mail templates currently available to this role.">
                    @if($templates->isNotEmpty())
                        <div class="admin-stack-list">
                            @foreach($templates as $template)
                                <div class="admin-stack-item">
                                    <span>{{ $template->name }}</span>
                                    <strong>{{ $template->subject }}</strong>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No templates have been configured yet.</p>
                    @endif
                </x-ui.panel>
            </div>
        </div>
    </div>
</div>
@endsection
