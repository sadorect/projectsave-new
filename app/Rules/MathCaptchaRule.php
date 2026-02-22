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
        if (!MathCaptcha::validate($value)) {
            $fail('The answer to the math question is incorrect. Please try again.');
        }
    }
}
