<?php

namespace App\Mail;

use App\Models\MailTemplate;
use App\Services\HtmlSanitizer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;

class CustomMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $template;
    protected $customData;
    protected $customMessage;

    public function __construct(MailTemplate $template, array $customData = [], ?string $customMessage = null)
    {
        $this->template = $template;
        $this->customData = $customData;
        $this->customMessage = $customMessage ? HtmlSanitizer::clean($customMessage) : null;
    }

    public function build()
    {
      Log::info('Sending email to: ' . $this->to[0]['address']);
        return $this->subject($this->template->subject)
                    ->view('emails.custom')
                    ->with([
                        'content' => $this->parseTemplate(),
                        'template' => $this->template,
                        'customMessage' => $this->customMessage,
                    ]);
    }

    protected function parseTemplate()
    {
        $content = $this->template->body;
        foreach ($this->customData as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }
        return HtmlSanitizer::clean($content);
    }
}
