<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmission extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Contact $contact)
    {
        $this->afterCommit();
    }

    public function build()
    {
        return $this->subject('Contact Form Submission')
            ->markdown('emails.contact-submission')
            ->with('contact', $this->contact);
    }
}
