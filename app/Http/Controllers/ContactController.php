<?php

namespace App\Http\Controllers;

use Mail;
use App\Rules\MathCaptchaRule;
use Illuminate\Http\Request;
use App\Mail\ContactFormSubmission;

class ContactController extends Controller
{
    public function show()
    {
        return view('pages.contact');
    }
    
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:2',
            'email' => 'required|email',
            'message' => 'required|min:10',
            'math_captcha' => ['required', new MathCaptchaRule]
        ]);

        try {
            $contact = new \App\Models\Contact();
            $contact->name = $validated['name'];
            $contact->email = $validated['email'];
            $contact->message = $validated['message'];
            $contact->save();

            Mail::to(config('mail.admin_email', 'admin@example.com'))->send(
                new ContactFormSubmission($contact)
            );

            \Log::info('New contact form submission', ['contact_id' => $contact->id]);

            return redirect()->back()->with('success', 'Thank you for your message. We will get back to you soon!');
            
        } catch (\Exception $e) {
            \Log::error('Contact form submission failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    
    }
}