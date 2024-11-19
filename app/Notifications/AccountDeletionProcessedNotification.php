<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccountDeletionProcessedNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Account Deletion Completed')
            ->line('Your account deletion request has been processed.')
            ->line('All your data has been permanently removed from our systems.')
            ->line('Thank you for being part of our community.');
    }
}
