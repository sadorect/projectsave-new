<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeletionRequest;
use App\Notifications\AccountDeletionProcessedNotification;

class DeletionRequestController extends Controller
{
    public function index()
    {
        $deletionRequests = DeletionRequest::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.deletion-requests.index', compact('deletionRequests'));
    }

    public function process(DeletionRequest $request)
    {
        $user = $request->user;

        // Process deletion
        $user->partners()->delete();
        $user->activities()->delete();
        
        // Update request status
        $request->update([
            'status' => 'processed',
            'processed_at' => now()
        ]);

        // Notify user
        $user->notify(new AccountDeletionProcessedNotification());

        // Delete user
        $user->delete();

        return back()->with('success', 'Account deletion processed successfully');
    }


public function show(DeletionRequest $request)
{
    return view('admin.deletion-requests.show', compact('request'));
}
}