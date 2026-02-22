<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;
use App\Models\Event;
use App\Models\Partner;
use Illuminate\Http\Request;
use App\Models\AnniversaryWishLog;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AnniversaryReminderNotification;

class AdminController extends Controller
{
    public function loginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (!$user->is_admin) {
                Auth::logout();
                return back()->withErrors(['email' => 'You do not have admin privileges.']);
            }

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been deactivated. Please contact support.']);
            }

            $request->session()->regenerate();
            return redirect()->intended('admin/dashboard');
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

    public function dashboard()
        {$stats = [
            'total_users' => User::count(),
            'active_users' => User::where('email_verified_at', '!=', null)->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'pending_partners' => Partner::where('status', 'pending')->count(),
            'asom_students' => User::where('user_type', 'asom_student')->count(),
            'pending_certificates' => \App\Models\Certificate::where('is_approved', false)->whereNull('approved_at')->count(),
            'total_certificates' => \App\Models\Certificate::count(),
        ];

        $recent_activity = Post::with('author')
            ->latest()
            ->take(5)
            ->get();

            $upcomingEvents = Event::where('date', '>', now())
        ->with(['reminderLogs'])
        ->orderBy('date')
        ->take(5)
        ->get();

       
            $todayCelebrants = $this->getTodayCelebrants();
            
       
        return view('admin.dashboard', compact('stats', 'recent_activity','upcomingEvents','todayCelebrants'));
        }

        public function showCelebrants()
        {
            $todayCelebrants = $this->getTodayCelebrants();
            
            return view('admin.dashboard', compact('todayCelebrants'));
        }
        
        private function getTodayCelebrants()
        {
            $today = now();
            
            $birthdays = User::whereMonth('birthday', $today->month)
                ->whereDay('birthday', $today->day)
                ->get()
                ->map(function($user) {
                    $user->celebration_type = 'birthday';
                    $user->years = now()->diffInYears($user->birthday);
                    return $user;
                });
                
            $weddings = User::whereMonth('wedding_anniversary', $today->month)
                ->whereDay('wedding_anniversary', $today->day)
                ->get()
                ->map(function($user) {
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
                
                // Log the wishes sent
                AnniversaryWishLog::create([
                    'user_id' => $user->id,
                    'type' => $type,
                    'sent_by' => auth()->id(),
                    'years' => $years
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

    return view('admin.celebrations.statistics', compact(
        'monthlyStats',
        'upcomingCelebrations',
        'topWellwishers',
        'responseMetrics'
    ));
}

private function getMonthlyStats()
{
    return User::selectRaw("MONTH(birthday) as month, COUNT(*) as count")        ->whereNotNull('birthday')
        ->groupBy('month')
        ->get()
        ->concat(
            User::selectRaw("MONTH(wedding_anniversary) as month, COUNT(*) as count")
                ->whereNotNull('wedding_anniversary')
                ->groupBy('month')
                ->get()
        );
}

private function getUpcomingCelebrations()
{
    $thirtyDaysFromNow = now()->addDays(30);
    
    return User::where(function($query) use ($thirtyDaysFromNow) {
        $query->whereRaw("DATE_FORMAT(birthday, '%m-%d') BETWEEN ? AND ?", [
            now()->format('m-d'),
            $thirtyDaysFromNow->format('m-d')
        ]);
    })
    ->orWhere(function($query) use ($thirtyDaysFromNow) {
        $query->whereRaw("DATE_FORMAT(wedding_anniversary, '%m-%d') BETWEEN ? AND ?", [
            now()->format('m-d'),
            $thirtyDaysFromNow->format('m-d')
        ]);
    })
    ->get();
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
        'rate' => $total > 0 ? ($responded / $total) * 100 : 0
    ];
}

public function celebrationCalendar()
{
    $celebrations = collect();
    
    // Get birthdays
    User::whereNotNull('birthday')->get()->each(function($user) use ($celebrations) {
        $birthdate = Carbon::parse($user->birthday)->setYear(now()->year);
        if ($birthdate->isPast()) {
            $birthdate->addYear();
        }
        
        $celebrations->push([
            'id' => 'birthday_' . $user->id,
            'title' => '🎂 ' . $user->name . "'s Birthday",
            'start' => $birthdate->format('Y-m-d'),
            'backgroundColor' => '#FF6B6B',
            'type' => 'birthday'
        ]);
    });
    
    // Get wedding anniversaries
    User::whereNotNull('wedding_anniversary')->get()->each(function($user) use ($celebrations) {
        $anniversary = Carbon::parse($user->wedding_anniversary)->setYear(now()->year);
        if ($anniversary->isPast()) {
            $anniversary->addYear();
        }
        
        $celebrations->push([
            'id' => 'anniversary_' . $user->id,
            'title' => '💑 ' . $user->name . "'s Wedding Anniversary",
            'start' => $anniversary->format('Y-m-d'),
            'backgroundColor' => '#4ECDC4',
            'type' => 'wedding'
        ]);
    });

    return view('admin.celebrations.calendar', [
        'celebrations' => $celebrations
    ]);
}



}

