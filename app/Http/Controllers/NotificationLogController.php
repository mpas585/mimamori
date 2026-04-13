<?php

namespace App\Http\Controllers;

use App\Models\AiCallLog;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationLogController extends Controller
{
    /**
     * デバイスの通知履歴を返す（AJAX）
     * パートナー（master）用 — 全デバイスアクセス可
     */
    public function show(string $deviceId)
    {
        $device = Device::where('device_id', $deviceId)->firstOrFail();
        return $this->buildResponse($device);
    }

    /**
     * パートナー（operator）用 — 自組織のデバイスのみ
     */
    public function showForOrg(string $deviceId)
    {
        $admin = Auth::guard('partner')->user();
        $device = Device::where('device_id', $deviceId)
            ->where('organization_id', $admin->organization_id)
            ->firstOrFail();
        return $this->buildResponse($device);
    }

    /**
     * ユーザー用（自分のデバイスの通知履歴）
     */
    public function showMine()
    {
        $device = Auth::user();
        return $this->buildResponse($device);
    }

    /**
     * 共通レスポンス生成
     */
    private function buildResponse(Device $device)
    {
        // AIコールログ（最新20件）
        $aiCallLogs = AiCallLog::where('device_id', $device->id)
            ->orderBy('called_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($log) {
                return [
                    'called_at'    => $log->called_at ? $log->called_at->format('Y/m/d H:i') : null,
                    'direction'    => $log->direction ?? 'outbound',
                    'call_status'  => $log->call_status,
                    'judgment'     => $log->judgment,
                    'transcript'   => $log->transcript,
                    'duration_sec' => $log->duration_sec,
                    'gpt_response' => $log->gpt_response,
                ];
            });

        // 通知ログ（最新20件）
        $notifLogs = DB::table('notification_logs')
            ->where('device_id', $device->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($log) {
                return [
                    'created_at' => $log->created_at ? Carbon::parse($log->created_at)->format('Y/m/d H:i') : null,
                    'type'       => $log->type,
                    'channel'    => $log->channel,
                    'recipient'  => $log->recipient,
                    'status'     => $log->status,
                    'subject'    => $log->subject,
                ];
            });

        // AIコール合計回数
        $aiCallCount = AiCallLog::where('device_id', $device->id)->count();

        return response()->json([
            'ai_call_logs'      => $aiCallLogs,
            'notification_logs' => $notifLogs,
            'ai_call_count'     => $aiCallCount,
        ]);
    }
}
