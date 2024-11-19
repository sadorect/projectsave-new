<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DataDeletionRequestNotification extends Notification
{
    use Queueable;

    protected $deletionRequest;

    public function __construct($deletionRequest)
    {
        $this->deletionRequest = $deletionRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Account Deletion Request')
            ->line('A user has requested account deletion.')
            ->line('User: ' . $this->deletionRequest->user->name)
            ->line('Reason: ' . $this->deletionRequest->reason)
            ->action('Review Request', route('admin.deletion-requests.show', $this->deletionRequest->id));
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->deletionRequest->user_id,
            'reason' => $this->deletionRequest->reason,
            'request_id' => $this->deletionRequest->id
        ];
    }
}
