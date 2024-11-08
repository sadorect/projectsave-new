<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Prayer',
            'Faith',
            'Evangelism',
            'Discipleship',
            'Worship',
            'Leadership',
            'Service',
            'Missions'
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'name' => $tag,
                'slug' => Str::slug($tag)
            ]);
        }
    }
}
