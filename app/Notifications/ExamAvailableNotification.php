<?php

namespace App\Notifications;

use App\Models\Exam;
use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ExamAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $exams;
    protected $course;

    /**
     * Create a new notification instance.
     */
    public function __construct($exams, Course $course)
    {
        $this->exams = is_array($exams) ? collect($exams) : collect([$exams]);
        $this->course = $course;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $examCount = $this->exams->count();
        $examWord = $examCount === 1 ? 'exam' : 'exams';
        
        $mail = (new MailMessage)
            ->subject("🎯 New {$examWord} available: {$this->course->title}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Great news! By completing the **{$this->course->title}** course, you have unlocked {$examCount} {$examWord}:");

        // List each exam with details
        foreach ($this->exams as $exam) {
            $mail->line("**📝 {$exam->title}**")
                 ->line("   • Duration: {$exam->duration_minutes} minutes")
                 ->line("   • Questions: {$exam->questions()->count()}")
                 ->line("   • Passing Score: {$exam->passing_score}%")
                 ->line("   • Attempts Allowed: {$exam->max_attempts}");
        }

        $mail->line("These exams are designed to test your understanding and help you demonstrate mastery of the course material.")
             ->line("📚 **Exam Tips:**")
             ->line("• Review your course materials before taking the exam")
             ->line("• Make sure you have a stable internet connection")
             ->line("• Set aside enough time to complete the exam without interruption")
             ->line("• Your answers are automatically saved as you progress");

        if ($examCount === 1) {
            $exam = $this->exams->first();
            return $mail->action('Take the Exam', route('lms.exams.show', $exam->id))
                        ->line('Good luck with your exam!')
                        ->salutation('Blessings, The ASOM Team');
        } else {
            return $mail->action('View All Available Exams', route('lms.exams.index'))
                        ->line('Choose which exam to take first and good luck!')
                        ->salutation('Blessings, The ASOM Team');
        }
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $examTitles = $this->exams->pluck('title')->toArray();
        $examCount = $this->exams->count();
        
        return [
            'type' => 'exams_available',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'exam_ids' => $this->exams->pluck('id')->toArray(),
            'exam_titles' => $examTitles,
            'exam_count' => $examCount,
            'message' => "New {$examCount} exam" . ($examCount > 1 ? 's' : '') . " available for {$this->course->title}: " . implode(', ', $examTitles)
        ];
    }
}
