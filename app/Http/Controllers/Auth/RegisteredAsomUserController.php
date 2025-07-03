<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Rules\Recaptcha;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;

class RegisteredAsomUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.asom-register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'g-recaptcha-response' => ['required', new Recaptcha],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'asom_student', // Mark as ASOM student
        ]);

        $user->sendEmailVerificationNotification();
        event(new Registered($user));

        Auth::login($user);

       // Store intended redirect in session for after email verification
       session(['asom_redirect_after_verification' => true]);

       // Redirect to email verification notice with ASOM context
       return redirect()->route('verification.notice')->with('asom_student', true);
    }

    /**
     * Display the ASOM welcome page with WhatsApp groups.
     */
    public function welcome(): View
    {
        $whatsappGroups = [
            [
                'name' => 'Bible Introduction',
                'url' => 'https://chat.whatsapp.com/BVLhGpQ6PSKJQB1Mj76MfY',
                'icon' => 'fas fa-book-open',
                'description' => 'Introduction to Biblical studies and foundational concepts'
            ],
            [
                'name' => 'Hermeneutics',
                'url' => 'https://chat.whatsapp.com/ChWdu5pFXnZK78CjxOG4ec',
                'icon' => 'fas fa-search',
                'description' => 'Biblical interpretation principles and methods'
            ],
            [
                'name' => 'Ministry Vitals',
                'url' => 'https://chat.whatsapp.com/JgEKk0Ae4b73zDptc4jTow',
                'icon' => 'fas fa-heart',
                'description' => 'Essential principles for effective ministry'
            ],
            [
                'name' => 'Spiritual Gifts & Ministry',
                'url' => 'https://chat.whatsapp.com/FgWiscG4Xh7A9ueuuOzACG',
                'icon' => 'fas fa-gifts',
                'description' => 'Discovering and using your spiritual gifts'
            ],
            [
                'name' => 'Biblical Counseling',
                'url' => 'https://chat.whatsapp.com/HBthpjWrv9q9nCGN6qi18V',
                'icon' => 'fas fa-hands-helping',
                'description' => 'Biblical approaches to counseling and care'
            ],
            [
                'name' => 'Homiletics',
                'url' => 'https://chat.whatsapp.com/JHhtdqlSTSd5uF3oUAOBly',
                'icon' => 'fas fa-microphone',
                'description' => 'Art and science of preaching and sermon preparation'
            ],
            [
                'name' => 'ASOM Recharge',
                'url' => 'https://chat.whatsapp.com/CnQijuSNwLe50yNald4aob',
                'icon' => 'fas fa-battery-full',
                'description' => 'Spiritual refreshment and encouragement'
            ],
            [
                'name' => 'Info Desk',
                'url' => 'https://chat.whatsapp.com/CD1sL6mRamMKErBimzz52f',
                'icon' => 'fas fa-info-circle',
                'description' => 'General information and administrative support'
            ]
        ];

        return view('asom-welcome', compact('whatsappGroups'));
    }
}
