<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PrayerForcePartner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class PrayerForceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function validData()
    {
        return [
            'name' => 'John Doe',
            'dob' => '1990-01-01',
            'profession' => 'Teacher',
            'phone' => '1234567890',
            'email' => 'john@example.com',
            'born_again' => 'yes',
            'salvation_date' => '2010-01-01',
            'salvation_place' => 'Local Church',
            'water_baptized' => 'yes',
            'baptism_type' => 'immersion',
            'holy_ghost_baptism' => 'yes',
            'leadership_experience' => 'yes',
            'church_name' => ['First Church'],
            'post_held' => ['Youth Leader'],
            'leadership_year' => ['2019'],
            'referee_name' => ['Pastor Smith'],
            'referee_phone' => ['0987654321'],
            'calling' => 'Ministry',
            'prayer_commitment' => 'yes'
        ];
    }

    public function test_data_format_validation()
    {
        $this->withoutMiddleware();
        $response = $this->post(route('volunteer.prayer-force.store'), array_merge(
            $this->validData(),
            ['dob' => 'invalid-date']
        ));
        $response->assertSessionHasErrors(['dob']);
    }

    public function test_leadership_details_json_structure()
    {
        $this->withoutMiddleware();
        $data = $this->validData();
        $data['church_name'] = ['Church 1', 'Church 2'];
        $data['post_held'] = ['Position 1', 'Position 2'];
        $data['leadership_year'] = ['2019', '2020'];
        $data['referee_name'] = ['Ref 1', 'Ref 2'];
        $data['referee_phone'] = ['123', '456'];

        $response = $this->post(route('volunteer.prayer-force.store'), $data);
        $this->assertDatabaseHas('prayer_force_partners', ['email' => 'john@example.com']);
    }

    public function test_xss_prevention()
    {
        $this->withoutMiddleware();
        $response = $this->post(route('volunteer.prayer-force.store'), array_merge(
            $this->validData(),
            ['name' => '<script>alert("xss")</script>John']
        ));
        
        $partner = PrayerForcePartner::where('email', 'john@example.com')->first();
        $this->assertStringNotContainsString('<script>', $partner->name);
    }

    public function test_conditional_field_clearing()
    {
        $this->withoutMiddleware();
        $data = $this->validData();
        $data['leadership_experience'] = 'no';
        
        $response = $this->post(route('volunteer.prayer-force.store'), $data);
        $partner = PrayerForcePartner::where('email', 'john@example.com')->first();
        $this->assertNull($partner->leadership_details);
    }
}
