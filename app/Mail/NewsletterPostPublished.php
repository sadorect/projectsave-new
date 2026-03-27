<?php

namespace App\Mail;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class NewsletterPostPublished extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Post $post,
        public NewsletterSubscriber $subscriber
    ) {
        $this->afterCommit();
    }

    public function build(): self
    {
        return $this->subject('New devotional: ' . $this->post->title)
            ->view('emails.newsletter-post-published');
    }
}
