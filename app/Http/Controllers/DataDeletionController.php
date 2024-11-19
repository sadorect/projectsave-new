<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeletionRequest;
use App\Notifications\DataDeletionRequestNotification;

class DataDeletionController extends Controller
{
    public function request(Request $request)
    {
        $deletionRequest = DeletionRequest::create([
            'user_id' => auth()->id(),
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        // Notify admin
        $admin = User::where('is_admin', true)->first();
        $admin->notify(new DataDeletionRequestNotification($deletionRequest));

        return back()->with('success', 'Your data deletion request has been submitted.');
    }

    public function confirm($token)
    {
        $request = DeletionRequest::where('token', $token)->firstOrFail();
        $user = $request->user;

        // Delete user data
        $user->partners()->delete();
        $user->activities()->delete();
        $user->delete();

        return redirect('/')->with('success', 'Your account has been successfully deleted.');
    }
}
