<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class NotificationSettingsController extends Controller
{
    public function edit()
    {
        $settings = [
            'sms_provider' => config('services.sms.provider'),
            'twilio' => config('services.twilio'),
            'africas_talking' => config('services.africas_talking'),
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

        // Update .env file with new values
        $this->updateEnvironmentFile($validated);

        return redirect()->back()->with('success', 'Notification settings updated successfully');
    }

    private function updateEnvironmentFile($data)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        $str = $this->updateEnvValue($str, 'SMS_PROVIDER', $data['sms_provider']);

        if ($data['sms_provider'] === 'twilio') {
            $str = $this->updateEnvValue($str, 'TWILIO_ACCOUNT_SID', $data['twilio_account_sid']);
            $str = $this->updateEnvValue($str, 'TWILIO_AUTH_TOKEN', $data['twilio_auth_token']);
            $str = $this->updateEnvValue($str, 'TWILIO_FROM', $data['twilio_from']);
        } else {
            $str = $this->updateEnvValue($str, 'AT_USERNAME', $data['at_username']);
            $str = $this->updateEnvValue($str, 'AT_API_KEY', $data['at_api_key']);
            $str = $this->updateEnvValue($str, 'AT_FROM', $data['at_from']);
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
}
