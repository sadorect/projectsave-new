<?php

namespace App\Notifications;

use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ExamResultsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $exam;
    protected $attempt;

    /**
     * Create a new notification instance.
     */
    public function __construct(Exam $exam, ExamAttempt $attempt)
    {
        $this->exam = $exam;
        $this->attempt = $attempt;
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
        $passed = $this->attempt->score >= $this->exam->passing_score;
        $subject = $passed 
            ? "🎉 Congratulations! You passed the {$this->exam->title} exam"
            : "📝 Your {$this->exam->title} exam results are ready";

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name}!");

        if ($passed) {
            $mail->line("Great news! You have successfully passed the **{$this->exam->title}** exam.")
                 ->line("**Your Score:** {$this->attempt->score}% (Passing score: {$this->exam->passing_score}%)")
                 ->line("**Course:** {$this->exam->course->title}")
                 ->line("**Completed:** {$this->attempt->completed_at->format('F j, Y \a\t g:i A')}")
                 ->line("Congratulations on this achievement! This demonstrates your understanding of the course material.")
                 ->action('View Detailed Results', route('lms.exams.results', [$this->exam->id, $this->attempt->id]));
            
            // Check if this opens new opportunities (other available exams)
            $completedCourses = $notifiable->courses()
                ->wherePivot('status', 'completed')
                ->orWhere(function($query) use ($notifiable) {
                    $query->whereHas('lessons', function($lessonQuery) use ($notifiable) {
                        $lessonQuery->whereHas('progress', function($progressQuery) use ($notifiable) {
                            $progressQuery->where('user_id', $notifiable->id)
                                         ->where('completed', true);
                        });
                    });
                })
                ->with('exams')
                ->get();
            
            $availableExamsCount = $completedCourses->flatMap->exams
                ->where('is_active', true)
                ->where('id', '!=', $this->exam->id)
                ->filter(function($exam) use ($notifiable) {
                    // Check if user hasn't passed this exam yet
                    $passedAttempt = $notifiable->examAttempts()
                        ->where('exam_id', $exam->id)
                        ->where('score', '>=', $exam->passing_score)
                        ->exists();
                    return !$passedAttempt;
                })
                ->count();
            
            if ($availableExamsCount > 0) {
                $mail->line("🚀 **You have {$availableExamsCount} other exam" . ($availableExamsCount > 1 ? 's' : '') . " available to take!**")
                     ->action('View Available Exams', route('lms.exams.index'));
            }
        } else {
            $mail->line("Thank you for completing the **{$this->exam->title}** exam.")
                 ->line("**Your Score:** {$this->attempt->score}% (Passing score: {$this->exam->passing_score}%)")
                 ->line("**Course:** {$this->exam->course->title}")
                 ->line("**Completed:** {$this->attempt->completed_at->format('F j, Y \a\t g:i A')}")
                 ->line("While you didn't achieve the passing score this time, don't be discouraged! Review the course materials and try again when you're ready.")
                 ->action('View Detailed Results', route('lms.exams.results', [$this->exam->id, $this->attempt->id]));

            // Check if retakes are allowed
            if ($this->exam->allow_retakes) {
                $remainingAttempts = $this->exam->max_attempts - $notifiable->examAttempts()->where('exam_id', $this->exam->id)->count();
                if ($remainingAttempts > 0) {
                    $mail->line("💪 **You have {$remainingAttempts} attempt(s) remaining.** Take time to review and try again!")
                         ->action('Retake Exam', route('lms.exams.show', $this->exam->id));
                }
            }
        }

        return $mail->line('Keep up the great work in your ministry training!')
                    ->salutation('Blessings, The ASOM Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $passed = $this->attempt->score >= $this->exam->passing_score;
        
        return [
            'type' => 'exam_results',
            'exam_id' => $this->exam->id,
            'exam_title' => $this->exam->title,
            'course_title' => $this->exam->course->title,
            'attempt_id' => $this->attempt->id,
            'score' => $this->attempt->score,
            'passing_score' => $this->exam->passing_score,
            'passed' => $passed,
            'completed_at' => $this->attempt->completed_at,
            'message' => $passed 
                ? "Congratulations! You passed the {$this->exam->title} exam with {$this->attempt->score}%"
                : "You scored {$this->attempt->score}% on the {$this->exam->title} exam. Review and try again!"
        ];
    }
}
