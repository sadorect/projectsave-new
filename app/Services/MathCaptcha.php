<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Simple session-based math CAPTCHA.
 *
 * Generates random arithmetic questions and stores the expected answer in the
 * session so no external service or API key is required.
 */
class MathCaptcha
{
    public const SESSION_KEY = 'math_captcha_answer';
    public const SESSION_COLLECTION_KEY = 'math_captcha_answers';

    /**
     * Generate a new math question and keep the answer in session storage.
     *
     * @return array{key:string,question:string}
     */
    public static function generate(?string $key = null): array
    {
        $operations = ['+', '-'];
        $operation = $operations[array_rand($operations)];

        switch ($operation) {
            case '+':
                $first = rand(0, 9);
                $second = rand(0, 9 - $first);
                $answer = $first + $second;
                break;

            case '-':
                $first = rand(0, 9);
                $second = rand(0, $first);
                $answer = $first - $second;
                break;

            default:
                $first = rand(0, 9);
                $second = rand(0, 9 - $first);
                $answer = $first + $second;
                $operation = '+';
        }

        $captchaKey = $key ?: (string) Str::uuid();
        $answers = session(self::SESSION_COLLECTION_KEY, []);
        $answers[$captchaKey] = $answer;

        session([
            self::SESSION_KEY => $answer,
            self::SESSION_COLLECTION_KEY => $answers,
        ]);

        return [
            'key' => $captchaKey,
            'question' => "{$first} {$operation} {$second} = ?",
        ];
    }

    /**
     * Validate whether the given value matches the stored answer.
     */
    public static function validate(mixed $value, ?string $key = null): bool
    {
        if ($key) {
            $answers = session(self::SESSION_COLLECTION_KEY, []);
            $expected = $answers[$key] ?? null;

            unset($answers[$key]);
            session([self::SESSION_COLLECTION_KEY => $answers]);

            if ($expected === null) {
                return false;
            }

            return (int) $value === (int) $expected;
        }

        $expected = session(self::SESSION_KEY);
        session()->forget(self::SESSION_KEY);

        if ($expected === null) {
            return false;
        }

        return (int) $value === (int) $expected;
    }
}
