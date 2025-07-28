<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CourseCompletionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $course;
    protected $completionDate;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course, $completionDate = null)
    {
        $this->course = $course;
        $this->completionDate = $completionDate ?? now();
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
        $mail = (new MailMessage)
            ->subject("🎓 Course Completed: {$this->course->title}")
            ->greeting("Congratulations {$notifiable->name}!")
            ->line("You have successfully completed the **{$this->course->title}** course!")
            ->line("**Completion Date:** {$this->completionDate->format('F j, Y \a\t g:i A')}")
            ->line("**Total Lessons:** {$this->course->lessons()->count()}")
            ->line("This is a significant milestone in your ministry training journey. Well done!");

        // Check if exams are now available
        $availableExams = $this->course->exams()
            ->where('is_active', true)
            ->whereHas('questions', function($query) {
                $query->havingRaw('COUNT(*) >= 5');
            })
            ->get();

        if ($availableExams->count() > 0) {
            $mail->line("🎯 **Great news!** Completing this course has unlocked {$availableExams->count()} exam(s) for you:")
                 ->line($availableExams->pluck('title')->map(function($title) {
                     return "• {$title}";
                 })->join("\n"))
                 ->line("These exams will test your understanding and help you demonstrate mastery of the course material.")
                 ->action('Take Your Exams', route('lms.exams.index'));
        }

        // Get other available courses
        $otherCourses = Course::where('status', 'published')
            ->where('id', '!=', $this->course->id)
            ->whereDoesntHave('users', function($query) use ($notifiable) {
                $query->where('user_id', $notifiable->id);
            })
            ->limit(3)
            ->get();

        if ($otherCourses->count() > 0) {
            $mail->line("🚀 **Continue your learning journey!** Here are other courses you might be interested in:")
                 ->line($otherCourses->pluck('title')->map(function($title) {
                     return "• {$title}";
                 })->join("\n"))
                 ->action('Explore More Courses', route('asom.welcome') . '#courses');
        }

        return $mail->line('Keep growing in your calling and ministry preparation!')
                    ->salutation('Blessings, The ASOM Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $availableExams = $this->course->exams()
            ->where('is_active', true)
            ->whereHas('questions', function($query) {
                $query->havingRaw('COUNT(*) >= 5');
            })
            ->count();

        return [
            'type' => 'course_completion',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'completion_date' => $this->completionDate,
            'lessons_count' => $this->course->lessons()->count(),
            'unlocked_exams_count' => $availableExams,
            'message' => "Congratulations! You completed {$this->course->title}. " . 
                        ($availableExams > 0 ? "{$availableExams} exam(s) are now available!" : "")
        ];
    }
}
