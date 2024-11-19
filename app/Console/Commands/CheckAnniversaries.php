<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use App\Notifications\AnniversaryReminderNotification;
use Illuminate\Console\Command;

class CheckAnniversaries extends Command
{
    protected $signature = 'check:anniversaries';
    protected $description = 'Check for birthdays and wedding anniversaries';

    public function handle()
    {
        $today = Carbon::now();

        // Check Birthdays
        $birthdayCelebrants = User::whereMonth('birthday', $today->month)
            ->whereDay('birthday', $today->day)
            ->get();

        foreach ($birthdayCelebrants as $celebrant) {
            $years = $today->diffInYears($celebrant->birthday);
            $celebrant->notify(new AnniversaryReminderNotification('birthday', $years));
        }

        // Check Wedding Anniversaries
        $anniversaryCelebrants = User::whereMonth('wedding_anniversary', $today->month)
            ->whereDay('wedding_anniversary', $today->day)
            ->get();

        foreach ($anniversaryCelebrants as $celebrant) {
            $years = $today->diffInYears($celebrant->wedding_anniversary);
            $celebrant->notify(new AnniversaryReminderNotification('wedding', $years));
        }

        $this->info('Anniversary checks completed.');
    }
}
