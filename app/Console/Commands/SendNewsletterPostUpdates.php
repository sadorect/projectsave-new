<?php

namespace App\Console\Commands;

use App\Mail\NewsletterPostPublished;
use App\Models\AppSetting;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Models\NewsletterSubscriber;

class SendNewsletterPostUpdates extends Command
{
    protected $signature = 'newsletter:send-post-updates';

    protected $description = 'Queue devotional email updates for newly published posts.';

    public function handle(): int
    {
        if (Schema::hasTable('app_settings')) {
            $newsletterSettings = AppSetting::get('newsletter_sending', ['enabled' => true]);
            if (!data_get($newsletterSettings, 'enabled', true)) {
                $this->info('Newsletter sending is currently disabled.');

                return self::SUCCESS;
            }
        }

        $posts = Post::query()
            ->published()
            ->whereNull('newsletter_sent_at')
            ->orderBy('published_at')
            ->get();

        if ($posts->isEmpty()) {
            $this->info('No newly published posts require newsletter delivery.');

            return self::SUCCESS;
        }

        $subscribers = NewsletterSubscriber::query()
            ->active()
            ->get();

        foreach ($posts as $post) {
            foreach ($subscribers as $subscriber) {
                Mail::to($subscriber->email)->queue(new NewsletterPostPublished($post, $subscriber));
            }

            $post->forceFill([
                'newsletter_sent_at' => now(),
            ])->save();
        }

        $this->info("Queued newsletter delivery for {$posts->count()} post(s).");

        return self::SUCCESS;
    }
}
