<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccountDeletionProcessedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $status = 'completed',
        protected ?string $notes = null
    ) {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject($this->status === 'completed' ? 'Account Deletion Completed' : 'Account Deletion Request Reviewed');

        if ($this->status === 'completed') {
            $mail->line('Your account deletion request has been completed.')
                ->line('All account data covered by the request has been removed from our systems.');
        } else {
            $mail->line('Your account deletion request has been reviewed but was not completed.')
                ->line('You can contact the ministry if you need clarification or want to submit an updated request.');
        }

        if ($this->notes) {
            $mail->line('Admin note: ' . $this->notes);
        }

        return $mail->line('Thank you for being part of our community.');
    }
}
