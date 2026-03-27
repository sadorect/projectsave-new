<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeletionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Services\UserDeletionService;
use App\Notifications\AccountDeletionProcessedNotification;

class DeletionRequestController extends Controller
{
    public function __construct(private readonly UserDeletionService $userDeletionService)
    {
    }

    public function index()
    {
        $deletionRequests = DeletionRequest::with(['user', 'processedBy'])
            ->latest()
            ->paginate(10);

        return view('admin.deletion-requests.index', compact('deletionRequests'));
    }

    public function process(Request $request, DeletionRequest $deletionRequest)
    {
        $validated = $request->validate([
            'decision' => ['required', 'in:complete,reject'],
            'processed_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($deletionRequest->status !== 'pending') {
            return back()->with('error', 'This deletion request has already been processed.');
        }

        $recipientEmail = $deletionRequest->requester_email ?: $deletionRequest->user?->email;

        if ($validated['decision'] === 'complete') {
            $user = $deletionRequest->user;

            if (!$user) {
                $deletionRequest->forceFill([
                    'status' => 'completed',
                    'processed_by' => auth()->id(),
                    'processed_notes' => $validated['processed_notes'],
                    'processed_at' => now(),
                ])->save();
            } else {
                $this->userDeletionService->deleteUser(
                    $user,
                    $deletionRequest,
                    auth()->id(),
                    $validated['processed_notes']
                );
            }

            if ($recipientEmail) {
                Notification::route('mail', $recipientEmail)
                    ->notify(new AccountDeletionProcessedNotification('completed', $validated['processed_notes']));
            }

            return back()->with('success', 'Account deletion completed successfully.');
        }

        $deletionRequest->forceFill([
            'status' => 'rejected',
            'processed_by' => auth()->id(),
            'processed_notes' => $validated['processed_notes'],
            'processed_at' => now(),
        ])->save();

        if ($recipientEmail) {
            Notification::route('mail', $recipientEmail)
                ->notify(new AccountDeletionProcessedNotification('rejected', $validated['processed_notes']));
        }

        return back()->with('success', 'Deletion request marked as rejected.');
    }

    public function show(DeletionRequest $request)
    {
        $request->load(['user.files', 'user.partnerships', 'processedBy']);

        $impactSummary = [
            'files' => $request->user?->files()->count() ?? 0,
            'partnerships' => $request->user?->partnerships()->count() ?? 0,
            'activities' => $request->user?->activities()->count() ?? 0,
        ];

        return view('admin.deletion-requests.show', compact('request', 'impactSummary'));
    }
}
