<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PartnerStatusUpdate extends Notification
{
    use Queueable;

    protected $partner;
    protected $status;
    protected $channels;

    public function __construct($partner, $status, $channels)
    {
        $this->partner = $partner;
        $this->status = $status;
        $this->channels = $channels;
    }

    public function via($notifiable)
    {
        return $this->channels;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Partner Application Update')
            ->markdown('mail.partners.' . $this->status, [
                'partner' => $this->partner,
                'url' => route('partners.show', ['type' => $this->partner->partner_type, 'partner' => $this->partner])
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'partner_id' => $this->partner->id,
            'status' => $this->status,
            'message' => "Partner application {$this->status}",
            'action_url' => route('partners.show', ['type' => $this->partner->partner_type, 'partner' => $this->partner])
          ];
    }
}
