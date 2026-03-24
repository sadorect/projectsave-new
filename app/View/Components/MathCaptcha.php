<?php

namespace App\View\Components;

use App\Services\MathCaptcha as MathCaptchaService;
use Illuminate\View\Component;

/**
 * Blade component: <x-math-captcha />
 *
 * Generates a new math question on every render and stores the answer
 * in the PHP session for server-side validation.
 */
class MathCaptcha extends Component
{
    public string $question;
    public string $inputClass;
    public string $formId;
    public string $errorBag;
    public string $captchaKey;

    /**
     * @param  string  $inputClass  Optional extra CSS classes for the input element.
     */
    public function __construct(string $inputClass = '', string $formId = '', string $errorBag = 'default')
    {
        $captcha = MathCaptchaService::generate();

        $this->question = $captcha['question'];
        $this->captchaKey = $captcha['key'];
        $this->inputClass = $inputClass;
        $this->formId = $formId;
        $this->errorBag = $errorBag;
    }

    public function render()
    {
        return view('components.math-captcha');
    }
}
