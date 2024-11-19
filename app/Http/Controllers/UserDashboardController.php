<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\DeletionRequest;
use App\Notifications\DataDeletionRequestNotification;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $activities = $user->activities()->latest()->take(5)->get();
        $partnerships = $user->partnerships()->get();
        $notifications = $user->notifications()->latest()->take(3)->get();

        return view('user.dashboard', compact('activities', 'partnerships', 'notifications'));
    }

    public function profile()
    {
        return view('user.profile');
    }

    public function partnerships()
    {
        $partnerships = auth()->user()->partnerships()->get();
        return view('user.partnerships', compact('partnerships'));
    }

    public function notifications()
    {
        $notifications = auth()->user()->notifications()->paginate(10);
        return view('user.notifications', compact('notifications'));
    }

    public function settings()
    {
        return view('user.settings');
    }


    public function updateProfile(Request $request)
    {
      
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'wedding_anniversary' => 'nullable|date',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
        ]);
        
        $user = auth()->user();

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);
        
        return back()->with('status', 'Profile updated successfully');
    }



    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'preferences' => 'array',
            'preferences.*' => 'boolean',
            'language' => 'string|in:en,fr',
            'timezone' => 'string|timezone',
        ]);

        $user = auth()->user();
        $user->update([
            'preferences' => $validated['preferences'] ?? [],
            'language' => $validated['language'] ?? 'en',
            'timezone' => $validated['timezone'] ?? 'UTC',
        ]);

        return back()->with('status', 'Preferences updated successfully');
    }

    public function showDeletionForm()
{
    return view('user.account-deletion');
}

public function requestDeletion(Request $request)
{
    $deletionRequest = DeletionRequest::create([
        'user_id' => auth()->id(),
        'reason' => $request->reason,
        'status' => 'pending'
    ]);

    // Notify admin
    $admin = User::where('is_admin', true)->first();
    $admin->notify(new DataDeletionRequestNotification($deletionRequest));

    auth()->logout();

    return redirect()->route('login')
        ->with('success', 'Your account deletion request has been submitted. You will receive confirmation once processed.');
}


}