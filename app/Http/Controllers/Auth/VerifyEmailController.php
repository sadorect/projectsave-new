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
        if ($request->user()->hasVerifiedEmail()) {
            // Check if user is ASOM student and should be redirected to welcome page
            if ($this->shouldRedirectToAsomWelcome($request->user())) {
                return redirect()->route('asom.welcome')->with('verified', true);
            }
            
            return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // After successful verification, check if user should go to ASOM welcome
        if ($this->shouldRedirectToAsomWelcome($request->user())) {
            // Clear the session flag
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
