<?php

namespace App\Mail;

use App\Models\BillingContract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeviceSuspendedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $displayName;
    public string $suspendedAt;
    public bool $isB2B;
    public string $actionUrl;

    /**
     * @param BillingContract $contract 対象契約
     */
    public function __construct(BillingContract $contract)
    {
        $this->displayName = $contract->getNotificationDisplayName();
        $this->suspendedAt = now()->format('Y/m/d H:i');
        $this->isB2B       = (bool) $contract->organization_id;

        $this->actionUrl = $this->isB2B
            ? url('/partner/billing')
            : url('/plan');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【みまもりデバイス】サービスを停止しました',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.device-suspended',
        );
    }
}
