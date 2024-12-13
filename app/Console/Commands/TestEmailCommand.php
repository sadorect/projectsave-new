<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class TestEmailCommand extends Command
{
    protected $signature = 'mail:test';
    protected $description = 'Test email configuration';
    public function handle()
    {
        // Set SSL verification options
        Config::set('mail.mailers.smtp.verify_peer', false);
        Config::set('mail.mailers.smtp.verify_peer_name', false);
        Config::set('mail.mailers.smtp.allow_self_signed', true);

        try {
            Mail::raw('Test email from ProjectSave', function($message) {
                $message->to('tosinomojolanow@gmail.com')
                    ->subject('Mail Server Test');
            });
            
            Log::info('Test email sent successfully ');
            $this->info('Test email sent successfully');
            
        } catch (\Exception $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());
            $this->error('Mail sending failed: ' . $e->getMessage());
        }
    }
}
