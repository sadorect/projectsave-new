<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CustomMail;
use App\Models\MailTemplate;
use App\Models\Partner;
use App\Models\PrayerForcePartner;
use App\Models\User;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function compose()
    {
        $templates = MailTemplate::query()
            ->orderBy('name')
            ->get(['id', 'name', 'subject']);

        $users = User::query()
            ->select('id', 'name', 'email', 'user_type')
            ->orderBy('name')
            ->get();

        $groups = collect([
            [
                'value' => 'group:students',
                'label' => 'ASOM Students',
                'description' => 'Learners currently enrolled in the school of ministry.',
                'count' => User::where('user_type', 'asom_student')->count(),
            ],
            [
                'value' => 'group:partners',
                'label' => 'Partners',
                'description' => 'Partnership applicants and approved ministry partners with email addresses.',
                'count' => Partner::whereNotNull('email')->count(),
            ],
            [
                'value' => 'group:prayer_force',
                'label' => 'Prayer Force',
                'description' => 'Prayer force records with email addresses available for communication.',
                'count' => PrayerForcePartner::whereNotNull('email')->count(),
            ],
        ]);

        return view('admin.mail.compose', compact('templates', 'users', 'groups'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|string',
            'template_id' => 'required|exists:mail_templates,id',
            'custom_message' => 'nullable|string',
        ]);

        $template = MailTemplate::findOrFail($validated['template_id']);
        $customMessage = trim((string) ($validated['custom_message'] ?? ''));
        $recipients = $this->getRecipients($validated['recipients']);

        foreach ($recipients as $recipient) {
            Mail::to($recipient['email'])->queue(
                new CustomMail($template, $recipient['context'], $customMessage)
            );
        }

        return redirect()
            ->route('admin.mail.compose')
            ->with('success', 'Emails queued for ' . $recipients->count() . ' recipient(s).');
    }

    public function preview(Request $request, ?MailTemplate $template = null)
    {
        $validated = $request->validate([
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|string',
            'template_id' => 'required|exists:mail_templates,id',
            'custom_message' => 'nullable|string',
        ]);

        $template = $template ?? MailTemplate::findOrFail($validated['template_id']);
        $customMessage = trim((string) ($validated['custom_message'] ?? ''));
        $recipients = $this->getRecipients($validated['recipients']);
        $previewContext = $recipients->first()['context'] ?? $this->defaultPreviewContext();
        $content = $this->processTemplate($template, $previewContext);

        return view('admin.mail.preview', [
            'content' => $content,
            'template' => $template,
            'customMessage' => HtmlSanitizer::clean($customMessage),
            'recipientDetails' => $recipients->take(8)->map(fn (array $recipient) => [
                'name' => $recipient['name'],
                'email' => $recipient['email'],
            ])->all(),
            'totalRecipients' => $recipients->count(),
            'selectedRecipients' => $validated['recipients'],
        ]);
    }

    protected function getRecipients(array $selectedRecipients): Collection
    {
        return collect($selectedRecipients)
            ->flatMap(function (string $selection) {
                if (str_starts_with($selection, 'group:')) {
                    return $this->groupRecipients(str_replace('group:', '', $selection))->all();
                }

                $recipient = $this->userRecipientFromSelection($selection);

                return $recipient ? [$recipient] : [];
            })
            ->filter(fn (?array $recipient) => ! empty($recipient['email']))
            ->unique('email')
            ->values();
    }

    protected function groupRecipients(string $group): Collection
    {
        return match ($group) {
            'students' => User::query()
                ->where('user_type', 'asom_student')
                ->whereNotNull('email')
                ->orderBy('name')
                ->get(['name', 'email'])
                ->map(fn (User $user) => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'context' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'partnership_type' => 'Student',
                        'course' => 'ASOM',
                    ],
                ]),
            'partners' => Partner::query()
                ->whereNotNull('email')
                ->orderBy('name')
                ->get(['name', 'email', 'partner_type'])
                ->map(fn (Partner $partner) => [
                    'name' => $partner->name,
                    'email' => $partner->email,
                    'context' => [
                        'name' => $partner->name,
                        'email' => $partner->email,
                        'partnership_type' => ucfirst($partner->partner_type) . ' partner',
                        'course' => 'Projectsave',
                    ],
                ]),
            'prayer_force' => PrayerForcePartner::query()
                ->whereNotNull('email')
                ->orderBy('name')
                ->get(['name', 'email'])
                ->map(fn (PrayerForcePartner $partner) => [
                    'name' => $partner->name,
                    'email' => $partner->email,
                    'context' => [
                        'name' => $partner->name,
                        'email' => $partner->email,
                        'partnership_type' => 'Prayer force',
                        'course' => 'Projectsave',
                    ],
                ]),
            default => collect(),
        };
    }

    protected function userRecipientFromSelection(string $selection): ?array
    {
        if (! str_starts_with($selection, 'user:')) {
            return null;
        }

        $userId = (int) str_replace('user:', '', $selection);
        $user = User::query()->find($userId);

        if (! $user || ! $user->email) {
            return null;
        }

        return [
            'name' => $user->name,
            'email' => $user->email,
            'context' => [
                'name' => $user->name,
                'email' => $user->email,
                'partnership_type' => $user->user_type === 'asom_student' ? 'Student' : 'Member',
                'course' => 'ASOM',
            ],
        ];
    }

    protected function defaultPreviewContext(): array
    {
        return [
            'name' => 'Recipient',
            'email' => 'recipient@example.com',
            'partnership_type' => 'Member',
            'course' => 'ASOM',
        ];
    }

    protected function processTemplate(MailTemplate $template, array $data): string
    {
        $content = $template->body;

        foreach ($data as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }

        return HtmlSanitizer::clean($content);
    }
}
