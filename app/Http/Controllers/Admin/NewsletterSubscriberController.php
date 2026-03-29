<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;

class NewsletterSubscriberController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage-mail,admin');
    }

    public function index()
    {
        $subscribers = NewsletterSubscriber::query()
            ->orderByDesc('is_active')
            ->orderByDesc('subscribed_at')
            ->paginate(25);

        $stats = [
            'total' => NewsletterSubscriber::query()->count(),
            'active' => NewsletterSubscriber::query()->where('is_active', true)->count(),
            'inactive' => NewsletterSubscriber::query()->where('is_active', false)->count(),
            'recent' => NewsletterSubscriber::query()->where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return view('admin.newsletter-subscribers.index', compact('subscribers', 'stats'));
    }

    public function show(NewsletterSubscriber $newsletterSubscriber)
    {
        return view('admin.newsletter-subscribers.show', [
            'subscriber' => $newsletterSubscriber,
        ]);
    }
}
