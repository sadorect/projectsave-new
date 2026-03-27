<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use App\Rules\MathCaptchaRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterSubscriptionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('newsletterSubscription', [
            'email' => ['required', 'email', 'max:255'],
            'math_captcha' => ['required', new MathCaptchaRule],
        ]);

        NewsletterSubscriber::query()->updateOrCreate(
            ['email' => Str::lower($validated['email'])],
            [
                'is_active' => true,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
                'unsubscribe_token' => (string) Str::uuid(),
                'source' => 'footer',
            ]
        );

        return back()->with('success', 'You are now subscribed to Newsletters. Be on the lookout for our ministry updates.');
    }

    public function destroy(string $token): RedirectResponse
    {
        $subscriber = NewsletterSubscriber::query()
            ->where('unsubscribe_token', $token)
            ->firstOrFail();

        $subscriber->forceFill([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ])->save();

        return redirect()->route('home')->with('success', 'You have been unsubscribed from Newsletters. You will no longer receive our ministry updates.');
    }
}
