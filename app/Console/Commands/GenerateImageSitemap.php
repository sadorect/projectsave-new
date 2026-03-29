<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\MinistryReport;
use App\Models\Post;
use App\Models\Event;

class GenerateImageSitemap extends Command
{
    protected $signature = 'sitemap:images';
    protected $description = 'Generate the image sitemap';

    public function handle()
    {
        $sitemap = Sitemap::create();

        Post::all()->each(function (Post $post) use ($sitemap) {
            if ($post->image) {
                $sitemap->add(
                    Url::create(asset('storage/' . $post->image))
                        ->addImage(asset('storage/' . $post->image), $post->title)
                );
            }
        });

        Event::all()->each(function (Event $event) use ($sitemap) {
            if ($event->image) {
                $sitemap->add(
                    Url::create(asset('storage/' . $event->image))
                        ->addImage(asset('storage/' . $event->image), $event->title)
                );
            }
        });

        MinistryReport::published()->get()->each(function (MinistryReport $report) use ($sitemap) {
            if ($report->featured_image) {
                $sitemap->add(
                    Url::create(asset('storage/' . $report->featured_image))
                        ->addImage(asset('storage/' . $report->featured_image), $report->title)
                );
            }

            collect($report->gallery ?? [])->each(function (string $imagePath) use ($sitemap, $report) {
                $sitemap->add(
                    Url::create(asset('storage/' . $imagePath))
                        ->addImage(asset('storage/' . $imagePath), $report->title)
                );
            });
        });

        $sitemap->writeToFile(public_path('image-sitemap.xml'));
    }
}
