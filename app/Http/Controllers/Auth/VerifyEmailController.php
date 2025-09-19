<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // The EmailVerificationRequest is signed and throttled by middleware.
        // However, the user may not be authenticated when they click the email link.
        // Handle both cases: authenticated (request->user()) and unauthenticated.

        $user = $request->user();

        // If no authenticated user, attempt to resolve user by route id
        if (!$user) {
            $id = $request->route('id');
            $user = \App\Models\User::find($id);
        }

        // If user not found, redirect with error
        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid verification link or user not found.');
        }

        // Verify the incoming hash matches the user's email verification hash
        $hash = $request->route('hash');
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            if ($this->shouldRedirectToAsomWelcome($user)) {
                return redirect()->route('asom.welcome')->with('verified', true);
            }

            return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        if ($this->shouldRedirectToAsomWelcome($user)) {
            session()->forget('asom_redirect_after_verification');
            return redirect()->route('asom.welcome')->with('verified', true);
        }

        return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
    }

    /**
     * Determine if user should be redirected to ASOM welcome page
     */
    private function shouldRedirectToAsomWelcome($user): bool
    {
        return $user->user_type === 'asom_student' && 
               (session('asom_redirect_after_verification') || request()->has('asom'));
    }
}
