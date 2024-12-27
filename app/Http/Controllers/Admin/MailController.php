<?php
namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Mail\CustomMail;
use App\Models\MailTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function compose()
    {
        $templates = MailTemplate::all();
        $users = User::select('id', 'name', 'email')->get();
        $groups = ['students', 'partners', 'prayer_force'];
        
        return view('admin.mail.compose', compact('templates', 'users', 'groups'));
    }

    public function send(Request $request)
{
    $validated = $request->validate([
        'recipients' => 'required|array',
        'recipients.*' => 'required|string',
        'template_id' => 'required|exists:mail_templates,id',
        'custom_message' => 'nullable|string'
    ]);

    $recipients = $this->getRecipients($validated['recipients']);
    $template = MailTemplate::findOrFail($validated['template_id']);
    
    foreach($recipients as $recipient) {
        Mail::to($recipient)->queue(new CustomMail($template));
    }

    return redirect()->back()->with('success', 'Emails queued for sending');
}



protected function getRecipients($selectedRecipients)
{
    $recipients = [];
    
    foreach ($selectedRecipients as $recipient) {
        if (str_starts_with($recipient, 'group:')) {
            $group = str_replace('group:', '', $recipient);
            $users = match($group) {
                'students' => User::whereHas('roles', fn($q) => $q->where('name', 'student')),
                'partners' => User::whereHas('partnerships'),
                'prayer_force' => User::whereHas('prayerForce'),
                default => collect([])
            };
            $recipients = array_merge($recipients, $users->pluck('email')->toArray());
        } else {
            $userId = str_replace('user:', '', $recipient);
            $user = User::find($userId);
            if ($user) {
                $recipients[] = $user->email;
            }
        }
    }
    
    return array_unique($recipients);
}



public function preview(Request $request)
{
    $template = MailTemplate::findOrFail($request->template_id);
    $customMessage = $request->custom_message;
    
    // Get selected recipients data
    $selectedRecipients = $request->recipients;
    $recipientData = [];
    
    if ($selectedRecipients) {
        foreach ($selectedRecipients as $recipient) {
            if (str_starts_with($recipient, 'user:')) {
                $userId = str_replace('user:', '', $recipient);
                $user = User::with(['courses', 'partnerships'])->find($userId);
                if ($user) {
                    $recipientData = [
                        'name' => $user->name,
                        'email' => $user->email,
                        'partnership_type' => $user->partnerships->first()?->type ?? 'Member',
                        'course' => $user->courses->first()?->title ?? 'ASOM'
                    ];
                    break; // Use first recipient for preview
                }
            }
        }
    }
    
    $content = $this->processTemplate($template, $recipientData);
    
    return view('admin.mail.preview', compact('content', 'template', 'customMessage'));
}

protected function processTemplate(MailTemplate $template, array $data)
{
    $content = $template->body;
    
    foreach ($data as $key => $value) {
        $content = str_replace('{' . $key . '}', $value, $content);
    }
    
    return $content;
}


}