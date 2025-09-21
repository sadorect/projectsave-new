<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CertificateApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Certificate $certificate)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $cert = $this->certificate;
        $title = $cert->course_id ? ($cert->course->title ?? 'Your Course') : 'Diploma in Ministry';
        $verifyUrl = route('certificates.public.verify', $cert->certificate_id);

        $mail = (new MailMessage)
            ->subject('🎓 Your Certificate Has Been Approved')
            ->greeting("Hello {$notifiable->name}!")
            ->line('Great news! Your certificate has been approved and is now available.')
            ->line("Certificate: {$title}")
            ->line('You can verify the authenticity of your certificate using the link below:')
            ->action('Verify Certificate', $verifyUrl);

        if ($cert->is_approved && $cert->issued_at) {
            $mail->line('Issued on: ' . $cert->issued_at->format('F j, Y'));
        }

        if (!is_null($cert->final_grade)) {
            $mail->line('Final Grade: ' . number_format((float)$cert->final_grade, 1) . '%');
        }

        // If course certificate and user is authenticated, include direct link to download from LMS
        try {
            $downloadUrl = route('lms.certificates.download', $cert);
            $mail->action('Download PDF', $downloadUrl);
        } catch (\Throwable $e) {
            // Route might not be accessible publicly; ignore
        }

        return $mail->line('Congratulations on this achievement!');
    }

    public function toArray(object $notifiable): array
    {
        $cert = $this->certificate;
        return [
            'type' => 'certificate_approved',
            'certificate_id' => $cert->certificate_id,
            'course_id' => $cert->course_id,
            'course_title' => $cert->course_id ? ($cert->course->title ?? null) : 'Diploma in Ministry',
            'issued_at' => $cert->issued_at,
            'final_grade' => $cert->final_grade,
            'verification_url' => route('certificates.public.verify', $cert->certificate_id),
        ];
    }
}
