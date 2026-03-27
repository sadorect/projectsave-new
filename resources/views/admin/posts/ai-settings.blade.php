@extends('admin.layouts.app')

@section('title', 'AI Image Settings')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">AI Image Settings</h1>
            <p class="text-muted mb-0">Manage review behavior, defaults, and provider credentials for generated blog images.</p>
        </div>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Posts
        </a>
    </div>

    @include('components.alerts')

    <form method="POST" action="{{ route('admin.ai-images.settings.update') }}" id="ai-image-settings-form">
        @csrf

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Global Defaults</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="requireApproval" name="require_approval" {{ old('require_approval', $requireApproval) ? 'checked' : '' }}>
                            <label class="form-check-label" for="requireApproval">Require admin review before AI-generated images go live</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Default Provider</label>
                        <select name="default_provider" class="form-select @error('default_provider') is-invalid @enderror">
                            @foreach($providers as $provider)
                                <option value="{{ $provider['key'] }}" {{ old('default_provider', $defaultProvider) === $provider['key'] ? 'selected' : '' }}>
                                    {{ $provider['label'] }}{{ $provider['tier'] ? ' · ' . $provider['tier'] : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('default_provider')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Default Preset</label>
                        <select name="default_preset" class="form-select @error('default_preset') is-invalid @enderror">
                            @foreach($presets as $presetKey => $preset)
                                <option value="{{ $presetKey }}" {{ old('default_preset', $defaultPreset) === $presetKey ? 'selected' : '' }}>
                                    {{ $preset['label'] ?? $presetKey }}
                                </option>
                            @endforeach
                        </select>
                        @error('default_preset')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @foreach($providers as $provider)
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $provider['label'] }}</strong>
                                @if($provider['tier'])
                                    <span class="badge bg-light text-dark border ms-2">{{ $provider['tier'] }}</span>
                                @endif
                                @if($provider['driver'])
                                    <span class="badge bg-secondary ms-2">{{ $provider['driver'] }}</span>
                                @endif
                            </div>
                            <span class="badge {{ $provider['state']['configured'] ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $provider['state']['configured'] ? 'Configured' : 'Missing API Key' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-3">
                                <div class="small text-muted">
                                    @if($provider['audit']['updated_at'])
                                        Last updated {{ \Illuminate\Support\Carbon::parse($provider['audit']['updated_at'])->diffForHumans() }}
                                        @if($provider['audit']['updated_by_name'])
                                            by {{ $provider['audit']['updated_by_name'] }}
                                        @endif
                                        · {{ $provider['audit']['configured'] ? 'stored override present' : 'using env fallback only' }}
                                    @else
                                        No stored override audit yet.
                                    @endif
                                </div>
                                <button
                                    type="submit"
                                    class="btn btn-sm btn-outline-primary"
                                    formaction="{{ route('admin.ai-images.settings.test', $provider['key']) }}"
                                    formmethod="POST"
                                >
                                    Test Connection
                                </button>
                            </div>

                            <div class="row g-3">
                                @foreach($provider['state']['fields'] as $fieldKey => $field)
                                    <div class="col-md-6">
                                        <label class="form-label">{{ $field['label'] }}</label>
                                        @if($field['secret'])
                                            <input
                                                type="password"
                                                name="providers[{{ $provider['key'] }}][{{ $fieldKey }}]"
                                                class="form-control @error('providers.' . $provider['key'] . '.' . $fieldKey) is-invalid @enderror"
                                                placeholder="Leave blank to keep existing value"
                                            >
                                            <div class="form-text">
                                                Source env: <code>{{ $field['env'] }}</code>
                                                @if($field['has_stored_value'])
                                                    · stored override present
                                                @endif
                                            </div>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" value="1" id="clear-{{ $provider['key'] }}-{{ $fieldKey }}" name="providers[{{ $provider['key'] }}][clear_{{ $fieldKey }}]">
                                                <label class="form-check-label" for="clear-{{ $provider['key'] }}-{{ $fieldKey }}">Clear stored override and fall back to env</label>
                                            </div>
                                        @else
                                            <input
                                                type="text"
                                                name="providers[{{ $provider['key'] }}][{{ $fieldKey }}]"
                                                class="form-control @error('providers.' . $provider['key'] . '.' . $fieldKey) is-invalid @enderror"
                                                value="{{ old('providers.' . $provider['key'] . '.' . $fieldKey, $field['value']) }}"
                                                placeholder="Optional override"
                                            >
                                            <div class="form-text">Env fallback: <code>{{ $field['env'] }}</code></div>
                                        @endif
                                        @error('providers.' . $provider['key'] . '.' . $fieldKey)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save AI Settings</button>
        </div>
    </form>
</div>
@endsection