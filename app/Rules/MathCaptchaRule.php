<?php

namespace App\Rules;

use Closure;
use App\Services\MathCaptcha;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validation rule for the math CAPTCHA input.
 *
 * Usage in controllers:
 *   'math_captcha' => ['required', new MathCaptchaRule],
 */
class MathCaptchaRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $captchaKey = request()->input('math_captcha_key');

        if (! MathCaptcha::validate($value, is_string($captchaKey) ? $captchaKey : null)) {
            $fail('The answer to the math question is incorrect. Please try again.');
        }
    }
}
