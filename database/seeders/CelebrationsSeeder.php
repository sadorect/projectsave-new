<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CelebrationsSeeder extends Seeder
{
    public function run()
    {
        // Create 75 users with birthdays and some with wedding anniversaries
        for ($i = 0; $i < 75; $i++) {
            $user = User::factory()->create([
                // Random birthday between 25 and 65 years ago
                'birthday' => Carbon::now()->subYears(rand(25, 65))->subDays(rand(0, 365)),
                
                // 60% chance of having a wedding anniversary
                'wedding_anniversary' => rand(1, 100) <= 60 
                    ? Carbon::now()->subYears(rand(1, 30))->subDays(rand(0, 365))
                    : null,
            ]);
        }

        // Ensure some celebrations for the current month
        for ($i = 0; $i < 10; $i++) {
            User::factory()->create([
                'birthday' => Carbon::now()->addDays(rand(1, 30)),
                'wedding_anniversary' => Carbon::now()->addDays(rand(1, 30)),
            ]);
        }
    }
}
