<?php

namespace App\Mail;

use App\Models\TroubleReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TroubleReportNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public TroubleReport $report;
    public string $typeLabel;
    public string $symptomLabel;
    public string $deviceIdentifier;
    public ?string $organizationName;
    public string $reporterRole;       // 'user' or 'partner'
    public ?string $reporterName;      // partner経由の場合は管理者名
    public string $submittedAt;
    public string $adminUrl;
    public string $description;

    /**
     * @param TroubleReport $report       対象の申請レコード
     * @param string        $reporterRole 'user' or 'partner'
     * @param string|null   $reporterName partner経由時の管理者名
     */
    public function __construct(TroubleReport $report, string $reporterRole, ?string $reporterName = null)
    {
        $report->loadMissing(['device.organization']);

        $this->report = $report;

        $typeMap = TroubleReport::typeLabels();
        $this->typeLabel = $typeMap[$report->type] ?? $report->type;

        $symptomMap = TroubleReport::symptomOptions();
        $this->symptomLabel = $report->symptom
            ? ($symptomMap[$report->symptom] ?? $report->symptom)
            : '（未選択）';

        $this->deviceIdentifier = $report->device->device_id ?? '不明';
        $this->organizationName = $report->device->organization->name ?? null;
        $this->reporterRole     = $reporterRole;
        $this->reporterName     = $reporterName;
        $this->submittedAt      = $report->created_at
            ? $report->created_at->format('Y/m/d H:i')
            : now()->format('Y/m/d H:i');
        $this->adminUrl    = url('/partner/trouble-reports');
        $this->description = $report->description ?: '';
    }

    public function envelope(): Envelope
    {
        $prefix = $this->report->type === 'abuse_report'
            ? '不正利用通報'
            : '故障・交換申請';

        return new Envelope(
            subject: "【みまもりデバイス・運営通知】{$prefix}を受け付けました",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trouble-report-notification',
        );
    }
}
