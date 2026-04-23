<?php

namespace App\Mail;

use App\Models\BillingContract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BillingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $displayName;
    public int $amount;
    public int $remainingDays;
    public string $suspendDate;
    public bool $isB2B;
    public string $actionUrl;

    /**
     * @param BillingContract $contract 対象契約
     * @param int $remainingDays 停止までの残日数
     */
    public function __construct(BillingContract $contract, int $remainingDays = 15)
    {
        $this->displayName   = $contract->getNotificationDisplayName();
        $this->amount        = (int) ($contract->amount ?? 0);
        $this->remainingDays = $remainingDays;
        $this->suspendDate   = $contract->past_due_at
            ? $contract->past_due_at->copy()->addDays(30)->format('Y年n月j日')
            : now()->addDays($remainingDays)->format('Y年n月j日');
        $this->isB2B         = (bool) $contract->organization_id;

        $this->actionUrl = $this->isB2B
            ? url('/partner/billing')
            : url('/plan');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【みまもりデバイス】お支払いが確認できていません（停止まで残り' . $this->remainingDays . '日）',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.billing-reminder',
        );
    }
}
