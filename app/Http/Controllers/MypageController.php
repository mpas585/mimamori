<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MypageController extends Controller
{
    public function index()
    {
        $device = Auth::user();

        $logs = $device->detectionLogs()
            ->orderBy('period_start', 'desc')
            ->limit(5)
            ->get();

        // 通知先未登録チェック
        $notif = $device->notificationSetting;
        $showNotifyBanner = !$notif || empty($notif->email_1);

        return view('mypage', compact('device', 'logs', 'showNotifyBanner'));
    }

    /**
     * 見守りON/OFF切替（AJAX）
     */
    public function toggleWatch(Request $request)
    {
        $device = Auth::user();
        $awayMode = $request->boolean('away_mode');

        $device->update([
            'away_mode' => $awayMode,
            'away_until' => null,
        ]);

        // ステータス情報を返す
        $statusMap = [
            'normal'   => ['class' => 'ok',      'label' => '正常稼働中'],
            'warning'  => ['class' => 'warning',  'label' => '注意'],
            'alert'    => ['class' => 'error',    'label' => '未検知アラート'],
            'offline'  => ['class' => 'offline',  'label' => '通信途絶'],
            'inactive' => ['class' => 'offline',  'label' => '未稼働'],
        ];

        $status = $statusMap[$device->status] ?? $statusMap['inactive'];

        $subtitle = '検知データなし';
        if ($device->last_human_detected_at) {
            $subtitle = '最終検知: ' . $device->last_human_detected_at->diffForHumans();
        }

        return response()->json([
            'ok'              => true,
            'away_mode'       => $awayMode,
            'status'          => $device->status,
            'indicator_class' => $status['class'],
            'status_label'    => $status['label'],
            'status_subtitle' => $subtitle,
        ]);
    }

    /**
     * 警告解除（ステータスをinactiveに変更）
     */
    public function dismissAlert(Request $request)
    {
        $device = Auth::user();

        // alert / offline 以外では実行不可
        if (!in_array($device->status, ['alert', 'offline'])) {
            return response()->json([
                'success' => false,
                'message' => '現在のステータスでは解除できません',
            ], 422);
        }

        $device->update([
            'status' => 'inactive',
        ]);

        return response()->json([
            'success' => true,
            'message' => '警告を解除しました',
        ]);
    }
}
