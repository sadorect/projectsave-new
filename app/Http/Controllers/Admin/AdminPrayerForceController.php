<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrayerForcePartner;
use App\Notifications\PrayerForceStatusUpdate;
use Illuminate\Http\Request;

class AdminPrayerForceController extends Controller
{
    public function index()
    {
        $partners = PrayerForcePartner::latest()->get();
        return view('admin.prayer-force.index', compact('partners'));
    }
    public function approve(Request $request, PrayerForcePartner $partner)
    {
        $partner->update(['status' => 'approved']);
        $channels = $request->input('notify_via', ['mail', 'database']);
        $partner->notify(new PrayerForceStatusUpdate($partner, 'approved', $channels));

        return redirect()->route('admin.prayer-force.index')
            ->with('success', 'Application approved and notification sent');
    }

    public function reject(Request $request, PrayerForcePartner $partner)
    {
        $partner->update(['status' => 'rejected']);
        $channels = $request->input('notify_via', ['mail', 'database']);
        $partner->notify(new PrayerForceStatusUpdate($partner, 'rejected', $channels));

        return redirect()->route('admin.prayer-force.index')
            ->with('success', 'Application rejected and notification sent');
    }


    public function show(PrayerForcePartner $partner)
      {
          return view('admin.prayer-force.show', compact('partner'));
      }
}



