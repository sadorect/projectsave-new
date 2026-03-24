<?php

namespace App\Http\Controllers;

use App\Models\AnniversaryWishLog;
use App\Models\Certificate;
use App\Models\DeletionRequest;
use App\Models\Event;
use App\Models\FormSubmission;
use App\Models\Partner;
use App\Models\Post;
use App\Models\PrayerForcePartner;
use App\Models\User;
use App\Notifications\AnniversaryReminderNotification;
use App\Rules\MathCaptchaRule;
use App\Support\Navigation\NavigationBuilder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function loginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'math_captcha' => ['required', new MathCaptchaRule],
        ]);

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (! $user->hasBackofficeAccess()) {
                Auth::logout();

                return back()->withErrors(['email' => 'You do not have back-office access.']);
            }

            $request->session()->regenerate();

            return redirect()->intended(route($user->dashboardRoute()));
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function dashboard(NavigationBuilder $navigationBuilder)
    {
        $user = Auth::user();
        $dashboardUrl = route('admin.dashboard');

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::whereNotNull('email_verified_at')->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'pending_partners' => Partner::where('status', 'pending')->count(),
            'pending_prayer_force' => PrayerForcePartner::where('status', 'pending')->count(),
            'form_submissions_today' => FormSubmission::whereDate('created_at', today())->count(),
            'pending_deletion_requests' => DeletionRequest::where('status', 'pending')->count(),
            'asom_students' => User::where('user_type', 'asom_student')->count(),
            'pending_certificates' => Certificate::where('is_approved', false)->whereNull('approved_at')->count(),
            'total_certificates' => Certificate::count(),
        ];

        $adminNavigation = $navigationBuilder->build('admin', $user);
        $recentActivity = $this->canViewContentWorkflow($user)
            ? Post::with('author')->latest()->take(5)->get()
            : collect();
        $upcomingEvents = $user->can('manage-notification-settings')
            ? Event::where('date', '>', now())
                ->with('reminderLogs')
                ->orderBy('date')
                ->take(5)
                ->get()
            : collect();
        $todayCelebrants = $user->can('view-reports')
            ? $this->getTodayCelebrants()
            : collect();

        $workflowSections = $this->buildWorkflowSections($adminNavigation, $dashboardUrl);
        $quickActions = $this->buildQuickActions($workflowSections);
        $attentionItems = $this->buildAttentionItems($user, $stats);
        $operatorPulse = $this->buildOperatorPulse($stats, $upcomingEvents);

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'upcomingEvents' => $upcomingEvents,
            'todayCelebrants' => $todayCelebrants,
            'workflowSections' => $workflowSections,
            'quickActions' => $quickActions,
            'attentionItems' => $attentionItems,
            'operatorPulse' => $operatorPulse,
            'canViewContentWorkflow' => $this->canViewContentWorkflow($user),
            'canManageReminders' => $user->can('manage-notification-settings'),
            'canViewCelebrations' => $user->can('view-reports'),
        ]);
    }

    public function showCelebrants()
    {
        return redirect()->to(route('admin.dashboard') . '#dashboard-celebrations');
    }

    private function getTodayCelebrants()
    {
        $today = now();

        $birthdays = User::whereMonth('birthday', $today->month)
            ->whereDay('birthday', $today->day)
            ->get()
            ->map(function (User $user) {
                $user->celebration_type = 'birthday';
                $user->years = now()->diffInYears($user->birthday);

                return $user;
            });

        $weddings = User::whereMonth('wedding_anniversary', $today->month)
            ->whereDay('wedding_anniversary', $today->day)
            ->get()
            ->map(function (User $user) {
                $user->celebration_type = 'wedding';
                $user->years = now()->diffInYears($user->wedding_anniversary);

                return $user;
            });

        return $birthdays->concat($weddings);
    }

    public function sendWishes($userId)
    {
        $user = User::findOrFail($userId);
        $today = now();
        $type = null;

        if ($user->birthday && $user->birthday->month === $today->month && $user->birthday->day === $today->day) {
            $type = 'birthday';
            $years = $today->diffInYears($user->birthday);
        } elseif ($user->wedding_anniversary && $user->wedding_anniversary->month === $today->month && $user->wedding_anniversary->day === $today->day) {
            $type = 'wedding';
            $years = $today->diffInYears($user->wedding_anniversary);
        }

        if ($type) {
            $user->notify(new AnniversaryReminderNotification($type, $years));

            AnniversaryWishLog::create([
                'user_id' => $user->id,
                'type' => $type,
                'sent_by' => auth()->id(),
                'years' => $years,
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 422);
    }

    public function viewWishLogs()
    {
        $wishLogs = AnniversaryWishLog::with(['user', 'sender'])
            ->latest()
            ->paginate(15);

        return view('admin.celebrations.wish-logs', compact('wishLogs'));
    }

    public function celebrationStats()
    {
        $monthlyStats = $this->getMonthlyStats();
        $upcomingCelebrations = $this->getUpcomingCelebrations();
        $topWellwishers = $this->getTopWellwishers();
        $responseMetrics = $this->getResponseMetrics();
        $currentMonthCelebrationTotal = (int) data_get($monthlyStats->firstWhere('month', now()->month), 'count', 0);
        $upcomingCelebrationCount = $upcomingCelebrations->count();
        $activeWellwishersCount = $topWellwishers->count();
        $monthlyChartLabels = collect(range(1, 12))
            ->map(fn (int $month) => Carbon::create()->month($month)->format('M'))
            ->all();
        $monthlyChartValues = $monthlyStats->pluck('count')->all();

        return view('admin.celebrations.statistics', compact(
            'monthlyStats',
            'upcomingCelebrations',
            'topWellwishers',
            'responseMetrics',
            'currentMonthCelebrationTotal',
            'upcomingCelebrationCount',
            'activeWellwishersCount',
            'monthlyChartLabels',
            'monthlyChartValues'
        ));
    }

    private function getMonthlyStats()
    {
        $driver = User::query()->getConnection()->getDriverName();
        $monthExpression = match ($driver) {
            'sqlite' => "CAST(strftime('%%m', %s) AS INTEGER)",
            'pgsql' => 'EXTRACT(MONTH FROM %s)',
            default => 'MONTH(%s)',
        };

        $birthdayCounts = User::selectRaw(sprintf($monthExpression, 'birthday') . ' as month, COUNT(*) as count')
            ->whereNotNull('birthday')
            ->groupBy('month')
            ->pluck('count', 'month');
        $anniversaryCounts = User::selectRaw(sprintf($monthExpression, 'wedding_anniversary') . ' as month, COUNT(*) as count')
            ->whereNotNull('wedding_anniversary')
            ->groupBy('month')
            ->pluck('count', 'month');

        return collect(range(1, 12))->map(function (int $month) use ($birthdayCounts, $anniversaryCounts): array {
            return [
                'month' => $month,
                'count' => (int) $birthdayCounts->get($month, 0) + (int) $anniversaryCounts->get($month, 0),
            ];
        });
    }

    private function getUpcomingCelebrations()
    {
        $today = now()->startOfDay();
        $windowEnd = $today->copy()->addDays(30);
        $celebrations = collect();

        User::query()
            ->whereNotNull('birthday')
            ->get(['id', 'name', 'birthday'])
            ->each(function (User $user) use ($celebrations, $today): void {
                $nextDate = Carbon::parse($user->birthday)->setYear($today->year)->startOfDay();

                if ($nextDate->isPast()) {
                    $nextDate->addYear();
                }

                $celebrations->push((object) [
                    'id' => $user->id,
                    'name' => $user->name,
                    'celebration_type' => 'birthday',
                    'celebration_type_label' => 'Birthday',
                    'next_celebration_date' => $nextDate,
                    'days_until' => $today->diffInDays($nextDate, false),
                ]);
            });

        User::query()
            ->whereNotNull('wedding_anniversary')
            ->get(['id', 'name', 'wedding_anniversary'])
            ->each(function (User $user) use ($celebrations, $today): void {
                $nextDate = Carbon::parse($user->wedding_anniversary)->setYear($today->year)->startOfDay();

                if ($nextDate->isPast()) {
                    $nextDate->addYear();
                }

                $celebrations->push((object) [
                    'id' => $user->id,
                    'name' => $user->name,
                    'celebration_type' => 'wedding',
                    'celebration_type_label' => 'Wedding Anniversary',
                    'next_celebration_date' => $nextDate,
                    'days_until' => $today->diffInDays($nextDate, false),
                ]);
            });

        return $celebrations
            ->filter(fn (object $celebration) => $celebration->next_celebration_date->between($today, $windowEnd))
            ->sortBy('next_celebration_date')
            ->values();
    }

    private function getTopWellwishers()
    {
        return AnniversaryWishLog::select('sent_by')
            ->selectRaw('COUNT(*) as wishes_count')
            ->with('sender:id,name')
            ->groupBy('sent_by')
            ->orderByDesc('wishes_count')
            ->limit(10)
            ->get();
    }

    private function getResponseMetrics()
    {
        $total = AnniversaryWishLog::count();
        $responded = AnniversaryWishLog::whereNotNull('response')->count();

        return [
            'total' => $total,
            'responded' => $responded,
            'rate' => $total > 0 ? ($responded / $total) * 100 : 0,
        ];
    }

    public function celebrationCalendar()
    {
        $celebrations = collect();

        User::whereNotNull('birthday')->get()->each(function (User $user) use ($celebrations) {
            $birthdate = Carbon::parse($user->birthday)->setYear(now()->year);

            if ($birthdate->isPast()) {
                $birthdate->addYear();
            }

            $celebrations->push([
                'id' => 'birthday_' . $user->id,
                'title' => 'Birthday: ' . $user->name,
                'start' => $birthdate->format('Y-m-d'),
                'backgroundColor' => '#FF6B6B',
                'type' => 'birthday',
                'userId' => $user->id,
                'displayDate' => $birthdate->format('M d, Y'),
            ]);
        });

        User::whereNotNull('wedding_anniversary')->get()->each(function (User $user) use ($celebrations) {
            $anniversary = Carbon::parse($user->wedding_anniversary)->setYear(now()->year);

            if ($anniversary->isPast()) {
                $anniversary->addYear();
            }

            $celebrations->push([
                'id' => 'anniversary_' . $user->id,
                'title' => 'Wedding anniversary: ' . $user->name,
                'start' => $anniversary->format('Y-m-d'),
                'backgroundColor' => '#4ECDC4',
                'type' => 'wedding',
                'userId' => $user->id,
                'displayDate' => $anniversary->format('M d, Y'),
            ]);
        });

        return view('admin.celebrations.calendar', [
            'celebrations' => $celebrations,
        ]);
    }

    private function canViewContentWorkflow(User $user): bool
    {
        return collect(['view-posts', 'create-posts', 'edit-posts', 'publish-posts', 'access-content-admin', 'edit-content'])
            ->contains(fn (string $ability) => $user->can($ability));
    }

    private function buildWorkflowSections(array $adminNavigation, string $dashboardUrl): Collection
    {
        return collect($adminNavigation)
            ->map(function (array $section) use ($dashboardUrl) {
                $actions = $this->flattenNavigationItems($section['items'] ?? [])
                    ->filter(fn (array $item) => $item['url'] !== $dashboardUrl)
                    ->take(5)
                    ->values();

                if ($actions->isEmpty()) {
                    return null;
                }

                $meta = $this->workflowSectionMeta($section['label'] ?? 'Workspace');

                return [
                    'label' => $section['label'] ?? 'Workspace',
                    'description' => $meta['description'],
                    'icon' => $meta['icon'],
                    'accent' => $meta['accent'],
                    'action_count' => $actions->count(),
                    'actions' => $actions->all(),
                ];
            })
            ->filter()
            ->values();
    }

    private function flattenNavigationItems(array $items): Collection
    {
        return collect($items)->flatMap(function (array $item) {
            if (! empty($item['children'])) {
                return $this->flattenNavigationItems($item['children'])->all();
            }

            if (! empty($item['url'])) {
                return [[
                    'label' => $item['label'],
                    'url' => $item['url'],
                    'icon' => $item['icon'] ?? 'bi bi-arrow-up-right-circle',
                    'badge' => $item['badge'] ?? null,
                ]];
            }

            return [];
        });
    }

    private function buildQuickActions(Collection $workflowSections): Collection
    {
        return $workflowSections
            ->flatMap(fn (array $section) => $section['actions'])
            ->unique('url')
            ->take(6)
            ->values();
    }

    private function buildAttentionItems(User $user, array $stats): Collection
    {
        $items = collect();

        if ($user->can('manage-partners')) {
            $items->push([
                'label' => 'Partner applications',
                'count' => $stats['pending_partners'],
                'icon' => 'bi bi-people-fill',
                'route' => route('admin.partners.index'),
                'tone' => 'warning',
                'description' => 'Pending partnership records waiting for review.',
            ]);
        }

        if ($user->can('manage-prayer-force')) {
            $items->push([
                'label' => 'Prayer force approvals',
                'count' => $stats['pending_prayer_force'],
                'icon' => 'bi bi-heart-pulse',
                'route' => route('admin.prayer-force.index'),
                'tone' => 'danger',
                'description' => 'Volunteer records still awaiting approval.',
            ]);
        }

        if ($user->can('manage-certificates')) {
            $items->push([
                'label' => 'Certificate approvals',
                'count' => $stats['pending_certificates'],
                'icon' => 'bi bi-award',
                'route' => route('admin.certificates.pending'),
                'tone' => 'info',
                'description' => 'Learner certificates pending review and approval.',
            ]);
        }

        if ($user->can('manage-forms')) {
            $items->push([
                'label' => 'Form submissions today',
                'count' => $stats['form_submissions_today'],
                'icon' => 'bi bi-inbox',
                'route' => route('admin.submissions.index'),
                'tone' => 'success',
                'description' => 'New submissions that arrived today.',
            ]);
        }

        if ($user->can('manage-users')) {
            $items->push([
                'label' => 'Deletion requests',
                'count' => $stats['pending_deletion_requests'],
                'icon' => 'bi bi-trash',
                'route' => route('admin.deletion-requests.index'),
                'tone' => 'danger',
                'description' => 'Account deletion requests pending action.',
            ]);
        }

        return $items->filter(fn (array $item) => $item['count'] > 0)->values();
    }

    private function buildOperatorPulse(array $stats, Collection $upcomingEvents): array
    {
        return [
            [
                'label' => 'Active users',
                'value' => number_format($stats['active_users']),
                'description' => 'Verified accounts currently in the system.',
            ],
            [
                'label' => 'New users today',
                'value' => number_format($stats['new_users_today']),
                'description' => 'Fresh registrations that landed today.',
            ],
            [
                'label' => 'ASOM students',
                'value' => number_format($stats['asom_students']),
                'description' => 'Learners currently attached to the school.',
            ],
            [
                'label' => 'Upcoming events',
                'value' => number_format($upcomingEvents->count()),
                'description' => 'Scheduled items still ahead on the calendar.',
            ],
        ];
    }

    private function workflowSectionMeta(string $label): array
    {
        return match ($label) {
            'Content' => [
                'icon' => 'bi bi-journal-richtext',
                'accent' => 'brand',
                'description' => 'Publish, review, and curate the public ministry surface.',
            ],
            'Operations' => [
                'icon' => 'bi bi-kanban',
                'accent' => 'ink',
                'description' => 'Manage people, approvals, support queues, and ministry operations.',
            ],
            'Learning' => [
                'icon' => 'bi bi-mortarboard',
                'accent' => 'accent',
                'description' => 'Oversee courses, enrollments, exams, and certificate workflows.',
            ],
            default => [
                'icon' => 'bi bi-grid-1x2',
                'accent' => 'brand',
                'description' => 'Open the tools currently available in your back-office workspace.',
            ],
        };
    }
}
