<?php

namespace App\Services;

/**
 * Simple session-based math CAPTCHA.
 *
 * Generates random arithmetic questions (addition / subtraction / multiplication)
 * and stores the expected answer in the PHP session so no external service
 * or API key is required.
 *
 * Usage flow:
 *  1. In a controller or Blade component, call MathCaptcha::generate() to
 *     produce a question and write the answer to the session.
 *  2. Render the question text next to a plain text input named "math_captcha".
 *  3. Validate the submitted value with the MathCaptchaRule.
 */
class MathCaptcha
{
    /** Session key that holds the expected answer. */
    public const SESSION_KEY = 'math_captcha_answer';

    /**
     * Generate a new math question, store its answer in the session, and
     * return the display string (e.g. "7 + 4 = ?").
     */
    public static function generate(): string
    {
        $operations = ['+', '-', '×'];
        $op = $operations[array_rand($operations)];

        switch ($op) {
            case '+':
                $a = rand(1, 20);
                $b = rand(1, 20);
                $answer = $a + $b;
                break;

            case '-':
                // Ensure non-negative result
                $a = rand(5, 20);
                $b = rand(1, $a);
                $answer = $a - $b;
                break;

            case '×':
                $a = rand(2, 10);
                $b = rand(2, 10);
                $answer = $a * $b;
                break;

            default:
                $a = rand(1, 10);
                $b = rand(1, 10);
                $answer = $a + $b;
        }

        session([self::SESSION_KEY => $answer]);

        return "{$a} {$op} {$b} = ?";
    }

    /**
     * Validate whether the given value matches the session answer.
     * Clears the session answer after checking to prevent replay attacks.
     */
    public static function validate(mixed $value): bool
    {
        $expected = session(self::SESSION_KEY);

        // Clear immediately to make it one-time-use
        session()->forget(self::SESSION_KEY);

        if ($expected === null) {
            return false;
        }

        return (int) $value === (int) $expected;
    }
}
