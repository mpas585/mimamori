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
        $notif = $device->notificationSetting ?? NotificationSetting::create(['device_id' => $device->id]);

        return view('settings', compact('device', 'notif'));
    }

    /**
     * デバイス設定の更新（AJAX）
     */
    public function updateDevice(Request $request)
    {
        $device = Auth::user();

        $rules = [];
        $data = [];

        // 送信されたフィールドのみバリデーション＆更新
        if ($request->has('alert_threshold_hours')) {
            $rules['alert_threshold_hours'] = 'required|in:12,24,36,48,72';
            $data['alert_threshold_hours'] = $request->alert_threshold_hours;
        }

        if ($request->has('pet_exclusion_enabled')) {
            $rules['pet_exclusion_enabled'] = 'required|boolean';
            $data['pet_exclusion_enabled'] = $request->pet_exclusion_enabled;
        }

        if ($request->has('install_height_cm')) {
            $rules['install_height_cm'] = 'required|integer|min:150|max:250';
            $data['install_height_cm'] = $request->install_height_cm;
        }

        if ($request->has('nickname')) {
            $rules['nickname'] = 'nullable|string|max:100';
            $data['nickname'] = $request->nickname;
        }

        if (!empty($rules)) {
            $request->validate($rules);
        }

        if (!empty($data)) {
            $device->update($data);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect('/settings')->with('success', '保存しました');
    }

    /**
     * 通知設定の更新（AJAX）
     */
    public function updateNotification(Request $request)
    {
        $device = Auth::user();
        $notif = $device->notificationSetting ?? NotificationSetting::create(['device_id' => $device->id]);

        $rules = [];
        $data = [];

        if ($request->has('email_1')) {
            $rules['email_1'] = 'nullable|email|max:255';
            $data['email_1'] = $request->email_1;
        }

        if ($request->has('email_enabled')) {
            $rules['email_enabled'] = 'required|boolean';
            $data['email_enabled'] = $request->email_enabled;
        }

        if ($request->has('webpush_enabled')) {
            $rules['webpush_enabled'] = 'required|boolean';
            $data['webpush_enabled'] = $request->webpush_enabled;
        }

        if (!empty($rules)) {
            $request->validate($rules);
        }

        if (!empty($data)) {
            $notif->update($data);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect('/settings')->with('success', '保存しました');
    }

    /**
     * テスト通知送信
     */
    public function sendTestNotification(Request $request)
    {
        $device = Auth::user();
        $notif = $device->notificationSetting;

        $targets = [];

        if ($notif && $notif->email_enabled && $notif->email_1) {
            // TODO: 実際のメール送信
            $targets[] = 'メール';
        }

        if ($notif && $notif->webpush_enabled) {
            // TODO: 実際のWebプッシュ送信
            $targets[] = 'Webプッシュ';
        }

        if (empty($targets)) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => '有効な通知先がありません'], 422);
            }
            return redirect('/settings')->with('error', '有効な通知先がありません');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'テスト通知を送信しました（' . implode('・', $targets) . '）',
                'targets' => $targets,
            ]);
        }

        return redirect('/settings')->with('success', 'テスト通知を送信しました');
    }
}
