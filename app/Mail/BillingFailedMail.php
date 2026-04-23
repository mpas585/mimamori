<?php

namespace App\Mail;

use App\Models\BillingContract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BillingFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $displayName;
    public int $amount;
    public string $failedAt;
    public bool $isB2B;
    public string $actionUrl;

    /**
     * @param BillingContract $contract 対象契約
     */
    public function __construct(BillingContract $contract)
    {
        $this->displayName = $contract->getNotificationDisplayName();
        $this->amount      = (int) ($contract->amount ?? 0);
        $this->failedAt    = now()->format('Y/m/d H:i');
        $this->isB2B       = (bool) $contract->organization_id;

        // B2B: パートナー課金画面、B2C: マイページのプランページ
        $this->actionUrl = $this->isB2B
            ? url('/partner/billing')
            : url('/plan');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【みまもりデバイス】月額課金に失敗しました',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.billing-failed',
        );
    }
}
