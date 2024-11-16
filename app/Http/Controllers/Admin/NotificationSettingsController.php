<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\ReminderLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use App\Notifications\EventReminderNotification;

class NotificationSettingsController extends Controller
{
    public function edit()
    {
      $settings = [
        'sms_provider' => config('services.sms.provider', 'twilio'),
        'twilio' => config('services.twilio', []),
        'africas_talking' => config('services.africas_talking', []),
    ];

        return view('admin.prayer-force.notification-settings', compact('settings'));
    }

    public function update(Request $request)
    {
      $validated = $request->validate([
        'sms_provider' => 'required|in:twilio,africas_talking',
        'twilio_account_sid' => 'required_if:sms_provider,twilio',
        'twilio_auth_token' => 'required_if:sms_provider,twilio',
        'twilio_from' => 'required_if:sms_provider,twilio',
        'at_username' => 'required_if:sms_provider,africas_talking',
        'at_api_key' => 'required_if:sms_provider,africas_talking',
        'at_from' => 'required_if:sms_provider,africas_talking',
    ]);

    $data = array_merge(['sms_provider' => 'twilio'], $validated);
    $this->updateEnvironmentFile($data);

        return redirect()->back()->with('success', 'Notification settings updated successfully');
    }

    private function updateEnvironmentFile($data)
    {
      $envFile = app()->environmentFilePath();
      $str = file_get_contents($envFile);
  
      $str = $this->updateEnvValue($str, 'SMS_PROVIDER', $data['sms_provider'] ?? 'twilio');
  
      if (($data['sms_provider'] ?? 'twilio') === 'twilio') {
          $str = $this->updateEnvValue($str, 'TWILIO_ACCOUNT_SID', $data['twilio_account_sid'] ?? '');
          $str = $this->updateEnvValue($str, 'TWILIO_AUTH_TOKEN', $data['twilio_auth_token'] ?? '');
          $str = $this->updateEnvValue($str, 'TWILIO_FROM', $data['twilio_from'] ?? '');
      } else {
          $str = $this->updateEnvValue($str, 'AT_USERNAME', $data['at_username'] ?? '');
          $str = $this->updateEnvValue($str, 'AT_API_KEY', $data['at_api_key'] ?? '');
          $str = $this->updateEnvValue($str, 'AT_FROM', $data['at_from'] ?? '');
      }
  
      file_put_contents($envFile, $str);
    }

    private function updateEnvValue($envContent, $key, $value)
    {
        $value = str_replace('"', '\\"', $value);
        $pattern = "/^{$key}=.*/m";
        
        if (preg_match($pattern, $envContent)) {
            return preg_replace($pattern, "{$key}=\"{$value}\"", $envContent);
        }
        
        return $envContent . "\n{$key}=\"{$value}\"";
    }


public function editEventReminders()
{
    $settings = [
        'reminder_enabled' => config('services.reminders.enabled', false),
        'reminder_days' => config('services.reminders.days_before', 2),
        'reminder_time' => config('services.reminders.send_at', '09:00'),
        'last_run' => cache('event_reminders_last_run')
    ];

    return view('admin.notification-settings.event-reminders', compact('settings'));
}
public function updateEventReminders(Request $request)
{
    $validated = $request->validate([
        'reminder_enabled' => 'nullable|boolean',
        'reminder_days' => 'required|integer|min:1|max:7',
        'reminder_time' => 'required|date_format:H:i',
    ]);

    $this->updateEnvironmentFile([
        'EVENT_REMINDERS_ENABLED' => $validated['reminder_enabled'] ?? false,
        'EVENT_REMINDERS_DAYS' => $validated['reminder_days'],
        'EVENT_REMINDERS_TIME' => $validated['reminder_time'],
    ]);

    return redirect()->back()->with('success', 'Event reminder settings updated successfully');
}

    public function viewReminderLogs()
    {
        $logs = ReminderLog::with('event')
            ->latest()
            ->paginate(15);

        return view('admin.notification-settings.reminder-logs', compact('logs'));
    }


public function sendManualReminder(Event $event)
{
    $users = User::whereJsonContains('notification_preferences->event_reminders', true)->get();
    
    foreach ($users as $user) {
        $user->notify(new EventReminderNotification($event));
    }

    ReminderLog::create([
        'event_id' => $event->id,
        'recipients_count' => $users->count(),
        'status' => 'success',
        'notes' => 'Manual reminder sent'
    ]);

    return response()->json(['success' => true]);
}



public function previewReminder(Event $event)
{
    $notification = new EventReminderNotification($event);
    
    // Get email preview
    $emailPreview = $notification->toMail(auth()->user())->render();
    
    // Get in-app notification preview
    $notificationPreview = view('admin.notification-settings.previews.in-app', [
        'notification' => $notification->toArray(auth()->user())
    ])->render();
    
    return response()->json([
        'emailPreview' => $emailPreview,
        'notificationPreview' => $notificationPreview
    ]);
}

}