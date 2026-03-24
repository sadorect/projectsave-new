<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Notifications\PartnerStatusUpdate;
use Illuminate\Http\Request;

class AdminPartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:viewAny,' . Partner::class)->only('index');
        $this->middleware('can:view,partner')->only('show');
        $this->middleware('can:moderate,partner')->only(['approve', 'reject']);
    }

    public function index(Request $request)
    {
        $partners = Partner::query()
            ->when($request->filled('partner_type'), fn ($query) => $query->where('partner_type', $request->string('partner_type')->toString()))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total' => Partner::count(),
            'pending' => Partner::where('status', 'pending')->count(),
            'approved' => Partner::where('status', 'approved')->count(),
            'rejected' => Partner::where('status', 'rejected')->count(),
            'prayer' => Partner::where('partner_type', 'prayer')->count(),
            'ground' => Partner::where('partner_type', 'ground')->count(),
            'skilled' => Partner::where('partner_type', 'skilled')->count(),
        ];

        return view('admin.partners.index', compact('partners', 'summary'));
    }

    public function show(Partner $partner)
    {
        $relatedApplications = Partner::query()
            ->where('email', $partner->email)
            ->whereKeyNot($partner->getKey())
            ->latest()
            ->take(3)
            ->get();

        return view('admin.partners.show', compact('partner', 'relatedApplications'));
    }

    public function approve(Request $request, Partner $partner)
    {
        $partner->update(['status' => 'approved']);
        $channels = $request->input('notify_via', ['mail', 'database']);
        $partner->notify(new PartnerStatusUpdate($partner, 'approved', $channels));

        return redirect()->route('admin.partners.index')
            ->with('success', 'Application approved and notification sent');
    }

    public function reject(Request $request, Partner $partner)
    {
        $partner->update(['status' => 'rejected']);
        $channels = $request->input('notify_via', ['mail', 'database']);
        $partner->notify(new PartnerStatusUpdate($partner, 'rejected', $channels));

        return redirect()->route('admin.partners.index')
            ->with('success', 'Application rejected and notification sent');
    }
}
