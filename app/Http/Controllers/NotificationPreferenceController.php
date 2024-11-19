<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    public function update(Request $request)
    {
        $preferences = $request->input('preferences', []);
        
        auth()->user()->update([
            'notification_preferences' => $preferences
        ]);

        return back()->with('status', 'Notification preferences updated successfully.');
    }
}
