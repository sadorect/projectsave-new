<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;
use NotificationChannels\AfricasTalking\AfricasTalkingMessage;

class PrayerForceStatusUpdate extends Notification
{
    private $partner;
    private $status;
    private $channels;

    public function __construct($partner, $status, array $channels = ['mail', 'database'])
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
            ->subject('Prayer Force Application Update')
            ->markdown('emails.prayer-force.' . $this->status, [
                'partner' => $this->partner,
                'url' => route('volunteer.prayer-force')
            ]);
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content($this->getSmsMessage());
    }

    public function toAfricasTalking($notifiable)
    {
        return (new AfricasTalkingMessage())
            ->content($this->getSmsMessage());
    }

    private function getSmsMessage()
    {
        return $this->status === 'approved' 
            ? "Your Prayer Force application has been approved! Check your email for details."
            : "Your Prayer Force application status has been updated. Please check your email.";
    }

    public function toArray($notifiable)
    {
        return [
            'partner_id' => $this->partner->id,
            'status' => $this->status,
            'message' => "Prayer Force application {$this->status}",
            'action_url' => route('volunteer.prayer-force')
        ];
    }
}


