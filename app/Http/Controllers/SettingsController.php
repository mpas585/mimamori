<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationSetting;

class SettingsController extends Controller
{
    /**
     * 設定画面表示
     */
    public function index()
    {
        $device = Auth::user();
        $notif = $device->notificationSetting ?? new NotificationSetting();

        return view('settings', compact('device', 'notif'));
    }

    /**
     * デバイス設定の保存（アラート閾値・ペット除外）
     */
    public function updateDevice(Request $request)
    {
        $request->validate([
            'alert_threshold_hours' => 'required|in:12,24,36,48,72',
            'pet_exclusion_enabled' => 'required|boolean',
            'pet_exclusion_threshold_cm' => 'required|integer|min:50|max:200',
        ]);

        $device = Auth::user();
        $device->update([
            'alert_threshold_hours' => $request->alert_threshold_hours,
            'pet_exclusion_enabled' => $request->pet_exclusion_enabled,
            'pet_exclusion_threshold_cm' => $request->pet_exclusion_threshold_cm,
        ]);

        return back()->with('success', 'デバイス設定を保存しました');
    }

    /**
     * 通知設定の保存（メールアドレス）
     */
    public function updateNotification(Request $request)
    {
        $request->validate([
            'email_1' => 'nullable|email|max:255',
            'email_enabled' => 'required|boolean',
        ]);

        $device = Auth::user();

        $device->notificationSetting()->updateOrCreate(
            ['device_id' => $device->id],
            [
                'email_1' => $request->email_1,
                'email_enabled' => $request->email_enabled,
            ]
        );

        return back()->with('success', '通知設定を保存しました');
    }

    /**
     * テスト通知送信
     */
    public function sendTestNotification(Request $request)
    {
        $device = Auth::user();
        $notif = $device->notificationSetting;

        if (!$notif || !$notif->email_1) {
            return back()->with('error', 'メールアドレスが登録されていません');
        }

        // TODO: 実際のメール送信はPhase1後半で実装
        // ここではログに記録のみ

        return back()->with('success', 'テスト通知を送信しました（※現在はテストモード）');
    }
}
