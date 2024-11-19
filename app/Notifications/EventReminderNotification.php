<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EventReminderNotification extends Notification
{
    use Queueable;

    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Reminder: ' . $this->event->title)
            ->line('This is a reminder for the upcoming event:')
            ->line($this->event->title)
            ->line('Date: ' . $this->event->date->format('F j, Y'))
            ->line('Time: ' . $this->event->start_time)
            ->line('Location: ' . $this->event->location)
            ->action('View Event Details', route('events.show', $this->event))
            ->line('We look forward to seeing you there!');
    }

    public function toArray($notifiable)
    {
        return [
            'event_id' => $this->event->id,
            'title' => $this->event->title,
            'date' => $this->event->date,
            'message' => 'Reminder for event: ' . $this->event->title
        ];
    }
}
