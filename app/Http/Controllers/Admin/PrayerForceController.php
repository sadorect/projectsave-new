<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrayerForcePartner;

class PrayerForceController extends Controller
{
    public function index()
    {
        $partners = PrayerForcePartner::latest()->get();
        return view('admin.prayer-force.index', compact('partners'));
    }
}
