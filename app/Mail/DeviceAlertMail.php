<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeviceAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $mailSubject;
    public string $mailBody;
    public string $alertType;

    /**
     * @param string $subject メール件名
     * @param string $body 本文（プレーンテキスト）
     * @param string $alertType 'alert' or 'offline'
     */
    public function __construct(string $subject, string $body, string $alertType = 'alert')
    {
        $this->mailSubject = $subject;
        $this->mailBody = $body;
        $this->alertType = $alertType;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.device-alert',
        );
    }
}


