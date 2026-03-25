@php
    $fieldLabel = \Illuminate\Support\Str::headline((string) $fieldKey);
@endphp

@if(is_array($fieldValue))
    <div class="row g-3">
        @foreach($fieldValue as $childKey => $childValue)
            <div class="{{ is_array($childValue) ? 'col-12' : 'col-lg-6' }}">
                <div class="{{ is_array($childValue) ? 'border rounded bg-white p-3 h-100' : '' }}">
                    @if(is_array($childValue))
                        <div class="fw-semibold mb-2">{{ \Illuminate\Support\Str::headline((string) $childKey) }}</div>
                    @endif
                    @include('admin.asom-page.partials.fields', [
                        'fieldKey' => $childKey,
                        'fieldValue' => $childValue,
                        'fieldPath' => $fieldPath . '[' . $childKey . ']',
                    ])
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="mb-3">
        <label class="form-label" for="{{ md5($fieldPath) }}">{{ $fieldLabel }}</label>
        @if(is_string($fieldValue) && strlen($fieldValue) > 120)
            <textarea
                id="{{ md5($fieldPath) }}"
                name="{{ $fieldPath }}"
                rows="4"
                class="form-control"
            >{{ old($fieldPath, $fieldValue) }}</textarea>
        @else
            <input
                id="{{ md5($fieldPath) }}"
                type="text"
                name="{{ $fieldPath }}"
                value="{{ old($fieldPath, $fieldValue) }}"
                class="form-control"
            >
        @endif
    </div>
@endif