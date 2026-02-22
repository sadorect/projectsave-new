{{--
    Math CAPTCHA component.

    Usage:
        <x-math-captcha />                          (Bootstrap form-control styling)
        <x-math-captcha input-class="form-control-lg" />

    The submitted field name is "math_captcha".
    Validate server-side with: 'math_captcha' => ['required', new \App\Rules\MathCaptchaRule]
--}}
<div class="math-captcha-wrapper mb-3">
    <label class="form-label fw-semibold" for="math_captcha">
        <i class="fas fa-shield-alt me-1 text-secondary" aria-hidden="true"></i>
        Security Check
    </label>
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <span class="math-captcha-question badge bg-light text-dark border fs-6 px-3 py-2"
              aria-label="Math question: {{ $question }}"
              style="font-family: monospace; letter-spacing: 0.05em;">
            {{ $question }}
        </span>
        <input
            type="number"
            name="math_captcha"
            id="math_captcha"
            class="form-control {{ $inputClass }} @error('math_captcha') is-invalid @enderror"
            placeholder="Your answer"
            value="{{ old('math_captcha') }}"
            autocomplete="off"
            inputmode="numeric"
            style="max-width: 120px;"
            aria-describedby="math_captcha_hint"
            required
        >
    </div>
    <div id="math_captcha_hint" class="form-text text-muted">
        Solve the equation above to verify you are human.
    </div>
    @error('math_captcha')
        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
    @enderror
</div>
