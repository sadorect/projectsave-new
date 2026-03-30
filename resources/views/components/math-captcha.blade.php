{{--
    Math CAPTCHA component.

    Usage:
        <x-math-captcha />                          (Bootstrap form-control styling)
        <x-math-captcha input-class="form-control-lg" />

    The submitted field name is "math_captcha".
    Validate server-side with: 'math_captcha' => ['required', new \App\Rules\MathCaptchaRule]
--}}
@php
    $captchaErrors = isset($errors) ? $errors->getBag($errorBag ?: 'default') : new \Illuminate\Support\MessageBag;
    $inputId = 'math_captcha_' . str_replace('-', '_', $captchaKey);
    $hintId = $inputId . '_hint';
@endphp

<div class="math-captcha-wrapper mb-3" data-math-captcha data-refresh-url="{{ route('math-captcha.refresh') }}">
    <label class="form-label fw-semibold" for="{{ $inputId }}">
        <i class="fas fa-shield-alt me-1 text-secondary" aria-hidden="true"></i>
        Security Check
    </label>
    <input type="hidden" name="math_captcha_key" value="{{ $captchaKey }}" data-math-captcha-key @if($formId !== '') form="{{ $formId }}" @endif>
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <span class="math-captcha-question badge bg-light text-dark border fs-6 px-3 py-2" data-math-captcha-question aria-live="polite" aria-label="Math question: {{ $question }}">
            {{ $question }}
        </span>
        <button type="button" class="btn btn-outline-secondary btn-sm" data-math-captcha-refresh>
            New equation
        </button>
        <input
            type="number"
            name="math_captcha"
            id="{{ $inputId }}"
            data-math-captcha-input
            class="form-control math-captcha-input {{ $inputClass }} {{ $captchaErrors->has('math_captcha') ? 'is-invalid' : '' }}"
            placeholder="Your answer"
            value="{{ old('math_captcha') }}"
            autocomplete="off"
            inputmode="numeric"
            aria-describedby="{{ $hintId }}"
            @if($formId !== '') form="{{ $formId }}" @endif
            required
        >
    </div>
    <div id="{{ $hintId }}" class="form-text text-muted">
        Solve the equation above to verify you are human.
    </div>
    @if($captchaErrors->has('math_captcha'))
        <div class="invalid-feedback d-block mt-1">{{ $captchaErrors->first('math_captcha') }}</div>
    @endif
</div>

<script>
    if (! window.__mathCaptchaRefreshBound) {
        window.__mathCaptchaRefreshBound = true;

        document.addEventListener('click', async (event) => {
            const refreshButton = event.target.closest('[data-math-captcha-refresh]');

            if (! refreshButton) {
                return;
            }

            const wrapper = refreshButton.closest('[data-math-captcha]');

            if (! wrapper) {
                return;
            }

            const refreshUrl = wrapper.dataset.refreshUrl;
            const questionNode = wrapper.querySelector('[data-math-captcha-question]');
            const keyNode = wrapper.querySelector('[data-math-captcha-key]');
            const inputNode = wrapper.querySelector('[data-math-captcha-input]');

            if (! refreshUrl || ! questionNode || ! keyNode || ! inputNode) {
                return;
            }

            const originalLabel = refreshButton.textContent;
            refreshButton.disabled = true;
            refreshButton.textContent = 'Refreshing...';

            try {
                const response = await fetch(refreshUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                if (! response.ok) {
                    throw new Error('Unable to refresh captcha.');
                }

                const payload = await response.json();

                questionNode.textContent = payload.question;
                questionNode.setAttribute('aria-label', `Math question: ${payload.question}`);
                keyNode.value = payload.key;
                inputNode.value = '';
                inputNode.focus();
            } catch (error) {
                refreshButton.textContent = 'Try again';
            } finally {
                refreshButton.disabled = false;

                if (refreshButton.textContent !== 'Try again') {
                    refreshButton.textContent = originalLabel;
                }
            }
        });
    }
</script>
