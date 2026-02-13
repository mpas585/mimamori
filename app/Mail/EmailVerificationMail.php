<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $device;
    public $token;
    public $verifyUrl;

    public function __construct($device, $token)
    {
        $this->device = $device;
        $this->token = $token;
        $this->verifyUrl = url('/email-settings/verify/' . $token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【みまもりデバイス】メールアドレスの確認',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.email-verification',
        );
    }
}
