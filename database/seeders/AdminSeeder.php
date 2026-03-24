<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Admin account
        $admin = User::firstOrCreate(
            ['email' => 'admin@localhost'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->forceFill(['is_admin' => true])->save();
        if ($role = Role::query()->where('slug', 'super-admin')->first()) {
            $admin->assignRole($role);
        }

        // Regular user 1
        $user1 = User::firstOrCreate(
            ['email' => 'user1@localhost'],
            [
                'name' => 'User One',
                'password' => Hash::make('user123'),
                'email_verified_at' => now(),
            ]
        );

        // Regular user 2
        $user2 = User::firstOrCreate(
            ['email' => 'user2@localhost'],
            [
                'name' => 'User Two',
                'password' => Hash::make('user123'),
                'email_verified_at' => now(),
            ]
        );
    }
}
