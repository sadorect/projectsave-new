<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\TagSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\PostSeeder;
use Database\Seeders\EventSeeder;
use Database\Seeders\CategorySeeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            PostSeeder::class,
            EventSeeder::class,
            ASOMDiplomaSeeder::class,
        ]);

        if (app()->environment('local')) {
            $this->call([
                LmsPreviewContentSeeder::class,
            ]);
        }
    }
}
