<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\MailTemplate;

class CustomMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $template;
    protected $customData;

    public function __construct(MailTemplate $template, array $customData = [])
    {
        $this->template = $template;
        $this->customData = $customData;
    }

    public function build()
    {
        return $this->subject($this->template->subject)
                    ->view('emails.custom')
                    ->with([
                        'content' => $this->parseTemplate(),
                        'template' => $this->template
                    ]);
    }

    protected function parseTemplate()
    {
        $content = $this->template->body;
        foreach ($this->customData as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }
        return $content;
    }
}
