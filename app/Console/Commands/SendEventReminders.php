<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\User;
use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for upcoming events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $upcomingEvents = Event::where('date', '>', now())
            ->where('date', '<=', now()->addDays(2))
            ->get();

            foreach ($upcomingEvents as $event) {
                try {
                    $users = User::whereJsonContains('notification_preferences->event_reminders', true)->get();
                    
                    foreach ($users as $user) {
                        $user->notify(new EventReminderNotification($event));
                    }
        
                    ReminderLog::create([
                        'event_id' => $event->id,
                        'recipients_count' => $users->count(),
                        'status' => 'success',
                        'notes' => 'Reminders sent successfully'
                    ]);
        
                } catch (\Exception $e) {
                    ReminderLog::create([
                        'event_id' => $event->id,
                        'recipients_count' => 0,
                        'status' => 'failed',
                        'notes' => $e->getMessage()
                    ]);
                }
            }

        $this->info('Event reminders sent successfully.');
    }
}
