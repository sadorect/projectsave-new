<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('admin123'),
            ]
        );

        // forceFill bypasses $guarded so privileged fields can be set by seeders/migrations
        $user->forceFill(['is_admin' => true])->save();
    }
}
