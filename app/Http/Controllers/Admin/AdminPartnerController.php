<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Notifications\PartnerStatusUpdate;
use Illuminate\Http\Request;

class AdminPartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::latest()->get();
        return view('admin.partners.index', compact('partners'));
    }

    public function show(Partner $partner)
    {
        return view('admin.partners.show', compact('partner'));
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
