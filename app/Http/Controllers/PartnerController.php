<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Rules\MathCaptchaRule;
use Illuminate\Http\Request;
use App\Notifications\PartnerStatusUpdate;

class PartnerController extends Controller
{
    protected $commitmentQuestions = [
        'prayer' => 'Are you ready to pray without ceasing?',
        'ground' => 'Are you ready to serve voluntarily?',
        'skilled' => 'In what capacity do you want to serve?',
        'financial' => 'What is your preferred giving frequency?'
    ];

    public function index($type)
    {
        return view('partners.create', [
            'partnerType' => $type
        ]);
    }

    public function show($type, Partner $partner)
    {
        return view('partners.show', [
            'partner' => $partner,
            'partnerType' => $type,
            'commitmentQuestion' => $this->commitmentQuestions[$type]
        ]);
    }

    public function create($type)
    {
        return view('partners.create', [
            'partnerType' => $type,
            'commitmentQuestion' => $this->commitmentQuestions[$type]
        ]);
    }

    public function store(Request $request, $type)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'date', 'before:today'],
            'profession' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'unique:partners'],
            'born_again' => ['required', 'in:yes,no'],
            'salvation_date' => ['required_if:born_again,yes', 'nullable', 'date'],
            'salvation_place' => ['required_if:born_again,yes', 'nullable', 'string'],
            'water_baptized' => ['required', 'in:yes,no'],
            'baptism_type' => ['required_if:water_baptized,yes', 'nullable', 'in:immersion,sprinkling'],
            'holy_ghost_baptism' => ['required', 'in:yes,no'],
            'holy_ghost_baptism_reason' => ['required_if:holy_ghost_baptism,no', 'nullable', 'string'],
            'leadership_experience' => ['required', 'in:yes,no'],
            'calling' => ['required', 'string'],
            'commitment_answer' => ['required'],
            'math_captcha' => ['required', new MathCaptchaRule],
        ]);

        $validated['partner_type'] = $type;
        $validated['commitment_question'] = $this->commitmentQuestions[$type];

        if ($request->leadership_experience === 'yes') {
            $validated['leadership_details'] = $this->processLeadershipDetails($request);
        }

        $partner = Partner::create($validated);

        return redirect()->route('partners.show', ['type' => $type, 'partner' => $partner])
            ->with('success', 'Your application has been submitted successfully.');
    }

    private function processLeadershipDetails($request): array
    {
        return collect($request->church_name)
            ->map(function ($item, $key) use ($request) {
                return [
                    'church_name' => strip_tags($request->church_name[$key]),
                    'post_held' => strip_tags($request->post_held[$key]),
                    'year' => strip_tags($request->leadership_year[$key]),
                    'referee_name' => strip_tags($request->referee_name[$key]),
                    'referee_phone' => strip_tags($request->referee_phone[$key])
                ];
            })->toArray();
    }
}
