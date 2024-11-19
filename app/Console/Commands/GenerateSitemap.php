<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use App\Models\Post;
use App\Models\Event;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap';

    public function handle()
    {
        $sitemap = SitemapGenerator::create(config('app.url'))
            ->hasCrawled(function ($url) {
                return [
                    'url' => $url,
                    'changefreq' => 'daily',
                    'priority' => 0.8
                ];
            })
            ->getSitemap();

        // Add dynamic routes
        Post::all()->each(function ($post) use ($sitemap) {
            $sitemap->add(route('posts.show', $post));
        });

        Event::all()->each(function ($event) use ($sitemap) {
            $sitemap->add(route('events.show', $event));
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));
    }
}
