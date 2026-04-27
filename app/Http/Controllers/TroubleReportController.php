<?php

namespace App\Http\Controllers;

use App\Mail\TroubleReportNotificationMail;
use App\Models\PartnerUser;
use App\Models\TroubleReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TroubleReportController extends Controller
{
    public function index()
    {
        $device = auth()->user();

        $reports = TroubleReport::where('device_id', $device->id)
            ->orderByDesc('created_at')
            ->get();

        $symptoms     = TroubleReport::symptomOptions();
        $statusLabels = TroubleReport::statusLabels();
        $typeLabels   = TroubleReport::typeLabels();

        return view('trouble.index', compact('device', 'reports', 'symptoms', 'statusLabels', 'typeLabels'));
    }

    public function store(Request $request)
    {
        $device = auth()->user();

        $request->validate([
            'type'        => 'required|in:malfunction,abuse_report',
            'symptom'     => 'nullable|string|max:100',
            'description' => 'nullable|string|max:2000',
        ], [
            'type.required' => '申請種別を選択してください',
            'type.in'       => '無効な申請種別です',
        ]);

        $report = TroubleReport::create([
            'device_id'   => $device->id,
            'type'        => $request->type,
            'symptom'     => $request->symptom,
            'description' => $request->description,
            'status'      => 'open',
        ]);

        $this->notifyAdmin($report, 'user', null);

        return redirect('/trouble')->with('success', '申請を受け付けました。確認後、ご連絡いたします。');
    }

    /**
     * partner_admins の master 全員に通知メールを送信（失敗してもメインフローを止めない）
     */
    private function notifyAdmin(TroubleReport $report, string $reporterRole, ?string $reporterName): void
    {
        $masterEmails = PartnerUser::where('role', 'master')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->pluck('email')
            ->unique()
            ->values()
            ->toArray();

        if (empty($masterEmails)) {
            Log::warning('TroubleReport admin notification skipped: no master admin with email found', [
                'report_id' => $report->id,
            ]);
            return;
        }

        foreach ($masterEmails as $email) {
            try {
                Mail::to($email)->send(new TroubleReportNotificationMail($report, $reporterRole, $reporterName));
            } catch (\Throwable $e) {
                Log::error('TroubleReport admin notification mail failed', [
                    'report_id' => $report->id,
                    'recipient' => $email,
                    'error'     => mb_substr($e->getMessage(), 0, 500),
                ]);
            }
        }
    }
}
