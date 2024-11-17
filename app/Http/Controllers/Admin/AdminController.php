<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\User;
use App\Models\Event;
use App\Models\Partner;
use App\Http\Controllers\Controller;
use App\Notifications\AnniversaryReminderNotification;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('email_verified_at', '!=', null)->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'pending_partners' => Partner::where('status', 'pending')->count(),
        ];

        $recent_activity = Post::with('author')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_activity'));
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