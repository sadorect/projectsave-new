<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomePartnerNotification extends Notification
{
    use Queueable;

    protected $password;
    protected $partnerType;

    public function __construct($password, $partnerType)
    {
        $this->password = $password;
        $this->partnerType = $partnerType;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $roleDescription = $this->getRoleDescription();

        return (new MailMessage)
            ->subject('Welcome to ProjectSave International Partnership Program')
            ->greeting('Welcome ' . $notifiable->name . '!')
            ->line('Thank you for joining our mission as a ' . ucfirst($this->partnerType) . ' Partner.')
            ->line($roleDescription)
            ->line('Your account has been created with these credentials:')
            ->line('Email: ' . $notifiable->email)
            ->line('Password: ' . $this->password)
            ->action('Access Your Account', route('login'))
            ->line('Next Steps:')
            ->line('1. Log in to your account')
            ->line('2. Complete your profile')
            ->line('3. Connect with other partners')
            ->line('Together, we can make a difference!');
    }

    private function getRoleDescription()
    {
        return match($this->partnerType) {
            'prayer' => 'As a Prayer Force Partner, you\'ll be part of our spiritual warfare team, interceding for missions and souls.',
            'skilled' => 'Your professional skills will help advance our mission in various capacities.',
            'ground' => 'You\'ll be directly involved in our field operations and community outreach programs.',
            default => 'You\'re now part of our mission to reach the nations.',
        };
    }
}
