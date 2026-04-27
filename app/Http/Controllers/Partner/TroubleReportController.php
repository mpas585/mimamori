<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Mail\TroubleReportNotificationMail;
use App\Models\Device;
use App\Models\PartnerUser;
use App\Models\TroubleReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TroubleReportController extends Controller
{
    /**
     * 一覧表示
     * master: 全件表示
     * operator: 自組織デバイスのみ
     */
    public function index(Request $request)
    {
        $admin = Auth::guard('partner')->user();
        $isMaster = $admin->role === 'master';

        $query = TroubleReport::with(['device.organization']);

        if (!$isMaster) {
            $orgId = $admin->organization_id;
            $deviceIds = Device::where('organization_id', $orgId)->pluck('id');
            $query->whereIn('device_id', $deviceIds);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('device', function ($q) use ($search) {
                $q->where('device_id', 'like', "%{$search}%");
            });
        }

        $reports = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        // 申請フォーム用のデバイス一覧
        if ($isMaster) {
            $devices = Device::whereNotNull('organization_id')
                ->orderBy('device_id')
                ->get(['id', 'device_id', 'organization_id']);
        } else {
            $devices = Device::where('organization_id', $admin->organization_id)
                ->orderBy('device_id')
                ->get(['id', 'device_id']);
        }

        $symptoms     = TroubleReport::symptomOptions();
        $statusLabels = TroubleReport::statusLabels();
        $typeLabels   = TroubleReport::typeLabels();

        return view('partner.trouble', compact(
            'admin', 'isMaster', 'reports', 'devices',
            'symptoms', 'statusLabels', 'typeLabels'
        ));
    }

    /**
     * 申請作成
     */
    public function store(Request $request)
    {
        $admin = Auth::guard('partner')->user();

        $request->validate([
            'device_id'   => 'required|exists:devices,id',
            'type'        => 'required|in:malfunction,abuse_report',
            'symptom'     => 'nullable|string|max:100',
            'description' => 'nullable|string|max:2000',
        ], [
            'device_id.required' => 'デバイスを選択してください',
            'device_id.exists'   => '指定されたデバイスが見つかりません',
            'type.required'      => '申請種別を選択してください',
        ]);

        // operatorは自組織のデバイスのみ
        if ($admin->role !== 'master') {
            $device = Device::where('id', $request->device_id)
                ->where('organization_id', $admin->organization_id)
                ->firstOrFail();
        }

        $report = TroubleReport::create([
            'device_id'   => $request->device_id,
            'type'        => $request->type,
            'symptom'     => $request->symptom,
            'description' => $request->description,
            'status'      => 'open',
        ]);

        $this->notifyAdmin($report, 'partner', $admin->name ?? null);

        return redirect()->route('partner.trouble-reports')
            ->with('success', '申請を受け付けました。');
    }

    /**
     * ステータス更新（masterのみ）
     */
    public function updateStatus(Request $request, int $id)
    {
        $admin = Auth::guard('partner')->user();
        if ($admin->role !== 'master') {
            return response()->json(['success' => false, 'message' => '権限がありません'], 403);
        }

        $report = TroubleReport::findOrFail($id);

        $request->validate([
            'status'      => 'required|in:open,in_progress,resolved,closed',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $data = ['status' => $request->status];
        if ($request->has('admin_notes')) {
            $data['admin_notes'] = $request->admin_notes;
        }
        if ($request->status === 'resolved' && !$report->resolved_at) {
            $data['resolved_at'] = now();
        }

        $report->update($data);

        return response()->json([
            'success' => true,
            'message' => 'ステータスを更新しました',
            'status'  => $report->status,
        ]);
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
