<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            // If user is already verified and is ASOM student, redirect to welcome
            if ($request->user()->user_type === 'asom_student' && session('asom_redirect_after_verification')) {
                session()->forget('asom_redirect_after_verification');
                return redirect()->route('asom.welcome');
            }
            
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return view('auth.verify-email');
    }
    
}
