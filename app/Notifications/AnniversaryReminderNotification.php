<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AnniversaryReminderNotification extends Notification
{
    use Queueable;

    protected $type;
    protected $years;

    public function __construct($type, $years)
    {
        $this->type = $type; // 'birthday' or 'wedding'
        $this->years = $years;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $message = $this->type === 'birthday' 
            ? "Happy {$this->years}th Birthday!" 
            : "Happy {$this->years}th Wedding Anniversary!";

        return (new MailMessage)
            ->subject($message)
            ->line($message)
            ->line("Dear {$notifiable->name},")
            ->line($this->getCustomMessage());
    }

    public function toArray($notifiable)
    {
        return [
            'type' => $this->type,
            'years' => $this->years,
            'message' => $this->getCustomMessage()
        ];
    }

    private function getCustomMessage()
    {
        return $this->type === 'birthday'
            ? "Wishing you God's blessings on your special day!"
            : "Celebrating your journey of love and commitment!";
    }
}
