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

        return response()->json(['ok' => true, 'away_mode' => $awayMode]);
    }
}
