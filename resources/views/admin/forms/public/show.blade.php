{{-- filepath: resources/views/admin/forms/public/show.blade.php --}}
<x-layouts.app>
    <x-slot name="title" >
        {{ $form->title ?? 'Form Submission' }}
    </x-slot>

    <x-slot name="header">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clipboard-list me-2"></i>{{ $form->title ?? 'Form Submission' }}
        </h1>
    </x-slot>

  <div class="mt-4">
        <div class="row justify-content-center pt-5 mt-8">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>{{ $form->title ?? 'Form' }}</h3>
                        @if($form->description)
                            <p class="mb-0 mt-2 opacity-75">{{ $form->description }}</p>
                        @endif
                    </div>
                    <div class="card-body mt-10">
                        @include('components.alerts')

                        @if($form->require_login && !Auth::check())
                            <div class="alert alert-warning">
                                <i class="fas fa-lock me-2"></i>You must be logged in to submit this form.
                                <a href="{{ route('login') }}" class="btn btn-sm btn-warning ms-2">Login</a>
                            </div>
                        @else
                            <form method="POST" action="{{ route('forms.submit', $form) }}" id="form-submission">
                                @csrf

                                @foreach($form->fields ?? [] as $index => $field)
                                    @php
                                        $fieldName = $field['name'] ?? 'field_' . $index . '_' . time();
                                        $fieldType = $field['type'] ?? 'text';
                                        $fieldLabel = $field['label'] ?? 'Field ' . ($index + 1);
                                        $isRequired = isset($field['required']) && $field['required'];
                                        $fieldOptions = $field['options'] ?? [];
                                    @endphp

                                    <div class="mb-4">
                                        <label class="form-label fw-bold">
                                            {{ $fieldLabel }}
                                            @if($isRequired)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        @if($fieldType === 'text')
                                            <input type="text" name="{{ $fieldName }}"
                                                class="form-control @error($fieldName) is-invalid @enderror"
                                                {{ $isRequired ? 'required' : '' }}
                                                value="{{ old($fieldName) }}"
                                                placeholder="Enter {{ strtolower($fieldLabel) }}">
                                        @elseif($fieldType === 'textarea')
                                            <textarea name="{{ $fieldName }}"
                                                class="form-control @error($fieldName) is-invalid @enderror"
                                                rows="4" {{ $isRequired ? 'required' : '' }}
                                                placeholder="Enter {{ strtolower($fieldLabel) }}">{{ old($fieldName) }}</textarea>
                                        @elseif($fieldType === 'select')
                                            <select name="{{ $fieldName }}"
                                                class="form-control @error($fieldName) is-invalid @enderror"
                                                {{ $isRequired ? 'required' : '' }}>
                                                <option value="">Choose {{ strtolower($fieldLabel) }}</option>
                                                @foreach($fieldOptions as $option)
                                                    <option value="{{ $option }}" {{ old($fieldName) == $option ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @elseif($fieldType === 'checkbox')
                                            <div class="form-check">
                                                <input type="hidden" name="{{ $fieldName }}" value="0">
                                                <input type="checkbox" name="{{ $fieldName }}" value="1"
                                                    class="form-check-input @error($fieldName) is-invalid @enderror"
                                                    id="checkbox_{{ $index }}" {{ old($fieldName) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="checkbox_{{ $index }}">
                                                    Yes, I agree/confirm
                                                </label>
                                            </div>
                                        @elseif($fieldType === 'radio')
                                            @foreach($fieldOptions as $optionIndex => $option)
                                                <div class="form-check">
                                                    <input type="radio" name="{{ $fieldName }}" value="{{ $option }}"
                                                        class="form-check-input @error($fieldName) is-invalid @enderror"
                                                        id="radio_{{ $index }}_{{ $optionIndex }}"
                                                        {{ old($fieldName) == $option ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="radio_{{ $index }}_{{ $optionIndex }}">
                                                        {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @elseif($fieldType === 'email')
                                            <input type="email" name="{{ $fieldName }}"
                                                class="form-control @error($fieldName) is-invalid @enderror"
                                                {{ $isRequired ? 'required' : '' }}
                                                value="{{ old($fieldName) }}"
                                                placeholder="Enter email address">
                                        @elseif($fieldType === 'number')
                                            <input type="number" name="{{ $fieldName }}"
                                                class="form-control @error($fieldName) is-invalid @enderror"
                                                {{ $isRequired ? 'required' : '' }}
                                                value="{{ old($fieldName) }}"
                                                placeholder="Enter number">
                                        @elseif($fieldType === 'date')
                                            <input type="date" name="{{ $fieldName }}"
                                                class="form-control @error($fieldName) is-invalid @enderror"
                                                {{ $isRequired ? 'required' : '' }}
                                                value="{{ old($fieldName) }}">
                                        @endif

                                        @error($fieldName)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach

                                <x-math-captcha />

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Form
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="card-footer text-muted text-center">
                        <small>
                            <i class="fas fa-shield-alt me-1"></i>
                            Your information is secure and will be handled according to our privacy policy.
                        </small>
                    </div>
                </div>
            </div>
        </div>
  </div>
</x-layouts.app>