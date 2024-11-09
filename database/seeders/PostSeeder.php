<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run()
    {
        $posts = [
            [
                'title' => 'The Power of Prayer in Mission Work',
                'slug' => Str::slug('The Power of Prayer in Mission Work'),
                'scripture' => 'Matthew 28:19-20',
                'subtitle' => 'Reaching the Unreached Through Prayer',
                'details' => 'Prayer is the foundation of effective mission work. Through dedicated prayer, we can break down spiritual barriers and prepare hearts to receive the gospel...',
                'action_point' => 'Commit to praying for missionaries daily and consider joining a prayer team.',
                'author' => 'Pastor John Doe',
                'comments_count' => 5,
                'status' => 'published',
                'published_at' => now(),
                'user_id' => 1
            ],
            [
                'title' => 'Building Disciples in Modern Times',
                'slug' => Str::slug('Building Disciples in Modern Times'),
                'scripture' => '2 Timothy 2:2',
                'subtitle' => 'Effective Discipleship Strategies',
                'details' => 'In our rapidly changing world, the principles of discipleship remain constant. We must adapt our methods while maintaining biblical truth...',
                'action_point' => 'Start a small discipleship group in your community.',
                'author' => 'Sarah Smith',
                'comments_count' => 3,
                'status' => 'published',
                'published_at' => now(),
                'user_id' => 1
            ]
        ];

        foreach ($posts as $postData) {
            $post = Post::create($postData);
            
            // Attach random categories and tags
            $post->categories()->attach(Category::inRandomOrder()->take(2)->pluck('id'));
            $post->tags()->attach(Tag::inRandomOrder()->take(3)->pluck('id'));
        }
    }
}
