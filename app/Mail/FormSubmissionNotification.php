<?php
namespace App\Mail;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormSubmissionNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Form $form, public FormSubmission $submission)
    {
        $this->afterCommit();
    }

    public function build()
    {
        return $this->subject('New Form Submission: ' . $this->form->title)
            ->view('emails.form_submission');
    }
}
