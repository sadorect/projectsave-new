<?php

namespace App\Console\Commands;

use App\Jobs\GeneratePostFeaturedImage;
use App\Models\Post;
use Illuminate\Console\Command;

class QueuePublishedPostFeaturedImages extends Command
{
    protected $signature = 'posts:generate-featured-images';

    protected $description = 'Queue AI featured image generation for published posts that are eligible.';

    public function handle(): int
    {
        if (!config('ai-images.enabled')) {
            $this->info('AI featured image generation is disabled.');

            return self::SUCCESS;
        }

        $count = 0;

        Post::query()
            ->where('featured_image_generation_enabled', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('image')
                    ->orWhere('featured_image_source', 'ai');
            })
            ->where(function ($query) {
                $query->whereNull('featured_image_generation_status')
                    ->orWhereIn('featured_image_generation_status', ['pending', 'failed']);
            })
            ->chunkById(100, function ($posts) use (&$count) {
                foreach ($posts as $post) {
                    GeneratePostFeaturedImage::dispatch($post->getKey());
                    $count++;
                }
            });

        $this->info("Queued {$count} post featured image job(s).");

        return self::SUCCESS;
    }
}