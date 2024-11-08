<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            'message' => 'required|min:10'
        ]);

        // Handle form submission here
        // You can add email notification, database storage, etc.

        return redirect()->back()->with('success', 'Thank you for your message. We will get back to you soon!');
    }
}
