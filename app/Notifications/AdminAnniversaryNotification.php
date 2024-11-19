<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminAnniversaryNotification extends Notification
{
    protected $celebrants;
    protected $type;

    public function __construct($celebrants, $type)
    {
        $this->celebrants = $celebrants;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => $this->type,
            'celebrants' => $this->celebrants->map(function($celebrant) {
                return [
                    'id' => $celebrant->id,
                    'name' => $celebrant->name,
                    'years' => $this->calculateYears($celebrant)
                ];
            })
        ];
    }

    private function calculateYears($celebrant)
    {
        $date = $this->type === 'birthday' ? $celebrant->birthday : $celebrant->wedding_anniversary;
        return now()->diffInYears($date);
    }
}
