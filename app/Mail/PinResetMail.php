<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PinResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $resetUrl,
        public string $deviceName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【みまもりデバイス】PIN再設定のご案内',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pin-reset',
        );
    }
}
