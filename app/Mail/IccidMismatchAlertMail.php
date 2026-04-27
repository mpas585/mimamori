<?php

namespace App\Mail;

use App\Models\Device;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IccidMismatchAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $deviceIdentifier;
    public ?string $organizationName;
    public string $expectedIccid;
    public string $receivedIccid;
    public string $detectedAt;
    public ?string $clientIp;
    public string $adminUrl;

    /**
     * @param Device      $device        該当デバイス
     * @param string      $expectedIccid DBに登録済みの正しいICCID
     * @param string      $receivedIccid 受信したICCID
     * @param string|null $clientIp      送信元IP
     */
    public function __construct(Device $device, string $expectedIccid, string $receivedIccid, ?string $clientIp)
    {
        $device->loadMissing('organization');

        $this->deviceIdentifier = $device->device_id ?? '不明';
        $this->organizationName = $device->organization->name ?? null;
        $this->expectedIccid    = $expectedIccid;
        $this->receivedIccid    = $receivedIccid;
        $this->detectedAt       = now()->format('Y/m/d H:i');
        $this->clientIp         = $clientIp;
        $this->adminUrl         = url('/partner/devices/' . $device->id . '/detail');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【みまもりデバイス・運営警告】ICCID不一致を検知しました',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.iccid-mismatch-alert',
        );
    }
}
