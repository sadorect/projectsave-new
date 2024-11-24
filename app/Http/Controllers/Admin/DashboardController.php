<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Event;
use App\Models\Partner;

class DashboardController extends Controller
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
}