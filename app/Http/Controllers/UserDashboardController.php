<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use App\Rules\MathCaptchaRule;
use App\Services\FileUploadService;
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
        $asomSummary = null;

        if ($user->user_type === 'asom_student') {
            $programCourseTitles = [
                'Bible Introduction',
                'Hermeneutics',
                'Ministry Vitals',
                'Spiritual Gifts & Ministry',
                'Biblical Counseling',
                'Homiletics',
            ];
            $enrolledCourses = $user->courses()
                ->whereIn('title', $programCourseTitles)
                ->get();
            $overallProgress = $enrolledCourses->isEmpty()
                ? 0
                : round($enrolledCourses->sum(fn ($course) => $user->getCourseProgress($course)) / $enrolledCourses->count(), 1);

            $asomSummary = [
                'overall_progress' => $overallProgress,
                'enrolled_courses_count' => $enrolledCourses->count(),
                'program_courses_count' => Course::query()
                    ->whereIn('title', $programCourseTitles)
                    ->count(),
            ];
        }

        if ($user->canAccessContentAdmin()) {
            return view('user.dashboard', [
                'showContentManagement' => true,
                'activities' => $activities,
                'partnerships' => $partnerships,
                'notifications' => $notifications,
                'asomSummary' => $asomSummary,
            ]);
        }

        return view('user.dashboard', compact('activities', 'partnerships', 'notifications', 'asomSummary'));
    }

    public function files(Request $request)
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->hasPermission('manage-files'), 403);

        $files = auth()->user()->files()
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->when($request->search, fn($q) => $q->where('original_name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20);

        $totalSize = auth()->user()->files()->sum('size');

        return view('user.files.index', compact('files', 'totalSize'));
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
        $user = auth()->user();

        return view('user.notifications', [
            'notifications' => $user->notifications()->paginate(10),
            'accountPreferences' => $this->accountPreferences($user),
        ]);
    }

    public function settings()
    {
        return view('user.settings', [
            'accountPreferences' => $this->accountPreferences(auth()->user()),
        ]);
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
            $path = FileUploadService::uploadImage(
                $request->file('avatar'),
                'avatars',
                'public',
                ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'avatar'
            );
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
            'language' => 'nullable|string|in:en,fr',
            'timezone' => 'nullable|string|timezone',
        ]);

        $user = auth()->user();
        $mergedPreferences = array_merge(
            $this->accountPreferences($user),
            $validated['preferences'] ?? []
        );

        $user->forceFill([
            'preferences' => $mergedPreferences,
            'notification_preferences' => $mergedPreferences,
            'language' => $validated['language'] ?? $user->language ?? 'en',
            'timezone' => $validated['timezone'] ?? $user->timezone ?? 'UTC',
        ])->save();

        return back()->with('status', 'Preferences updated successfully');
    }

    public function showDeletionForm()
    {
        $pendingDeletionRequest = DeletionRequest::query()
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('user.account-deletion', compact('pendingDeletionRequest'));
    }

    public function requestDeletion(Request $request)
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:5000'],
            'confirm_deletion' => ['accepted'],
            'password' => ['required', 'current_password'],
            'math_captcha' => ['required', new MathCaptchaRule],
        ]);

        $pendingDeletionRequest = DeletionRequest::query()
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($pendingDeletionRequest) {
            return back()->withErrors([
                'reason' => 'You already have a pending deletion request under review.',
            ]);
        }

        $deletionRequest = DeletionRequest::create([
            'user_id' => auth()->id(),
            'requester_name' => auth()->user()->name,
            'requester_email' => auth()->user()->email,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        User::query()
            ->where('is_admin', true)
            ->get()
            ->each
            ->notify(new DataDeletionRequestNotification($deletionRequest));

        return back()->with('success', 'Your account deletion request has been submitted and is now awaiting admin review.');
    }

    private function accountPreferences(User $user): array
    {
        return array_merge(
            [
                'email' => true,
                'prayer_updates' => false,
                'event_reminders' => false,
                'newsletter' => false,
                'blog_posts' => false,
                'events' => false,
                'prayer_requests' => false,
            ],
            (array) $user->preferences,
            (array) $user->notification_preferences,
        );
    }
}
