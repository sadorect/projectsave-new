<?php

namespace App\Http\Controllers;

use App\Models\PrayerForcePartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\HtmlString;

class PrayerForceController extends Controller
{
    public function __construct()
    {
        $this->middleware('web');
       
    }

    public function index()
    {
        return view('partners.prayer-force');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-\'\.]+$/u'],
            'dob' => ['required', 'date', 'before:today'],
            'profession' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:prayer_force_partners',
            'born_again' => 'required|in:yes,no',
            'salvation_date' => 'required_if:born_again,yes|nullable|date',
            'salvation_place' => 'required_if:born_again,yes|nullable|string',
            'water_baptized' => 'required|in:yes,no',
            'baptism_type' => 'required_if:water_baptized,yes|nullable|in:immersion,sprinkling',
            'holy_ghost_baptism' => 'required|in:yes,no',
            'holy_ghost_baptism_reason' => 'required_if:holy_ghost_baptism,no|nullable|string',
            'leadership_experience' => 'required|in:yes,no',
            'church_name.*' => 'required_if:leadership_experience,yes|nullable|string',
            'post_held.*' => 'required_if:leadership_experience,yes|nullable|string',
            'leadership_year.*' => 'required_if:leadership_experience,yes|nullable|string',
            'referee_name.*' => 'required_if:leadership_experience,yes|nullable|string',
            'referee_phone.*' => 'required_if:leadership_experience,yes|nullable|string',
            'calling' => 'required|string',
            'prayer_commitment' => 'required|in:yes,no'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        $validated = $validator->validated();

        // Clean and sanitize all input data
        $validated = $this->sanitizeInput($validated);

        // Process leadership details
        if ($request->leadership_experience === 'yes' && $request->has('church_name')) {
            $validated['leadership_details'] = $this->processLeadershipDetails($request);
        } else {
            $validated['leadership_details'] = null;
        }

        $validated['status'] = 'pending';

        $partner = PrayerForcePartner::create($validated);

        return redirect()->route('volunteer.prayer-force')
            ->with('success', 'Thank you for joining our Prayer Force! Your application has been submitted successfully.');
    }

    public function show(PrayerForcePartner $partner)
    {
        return view('partners.prayer-force.show', compact('partner'));
    }

    public function update(Request $request, PrayerForcePartner $partner)
    {
        // Add update logic if needed
    }

    public function destroy(PrayerForcePartner $partner)
    {
        // Add delete logic if needed
    }

    private function sanitizeInput($data)
    {
        return collect($data)->map(function ($value) {
            if (is_string($value)) {
                return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
            return $value;
        })->all();
    }

    private function processLeadershipDetails($request): array
    {
        if (!$request->has('church_name')) {
            return [];
        }
        
        return array_map(function($index) use ($request) {
            return [
                'church_name' => $request->church_name[$index],
                'post_held' => $request->post_held[$index],
                'year' => $request->leadership_year[$index],
                'referee_name' => $request->referee_name[$index],
                'referee_phone' => $request->referee_phone[$index]
            ];
        }, array_keys($request->church_name));
    }

}
