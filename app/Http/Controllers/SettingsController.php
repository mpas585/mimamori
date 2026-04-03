<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationSetting;
use App\Mail\DeviceAlertMail;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client as TwilioClient;

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

        if ($request->has('email_enabled')) {
            $rules['email_enabled'] = 'required|boolean';
            $data['email_enabled'] = $request->email_enabled;
        }

        if ($request->has('webpush_enabled')) {
            $rules['webpush_enabled'] = 'required|boolean';
            $data['webpush_enabled'] = $request->webpush_enabled;
        }

        if ($request->has('sms_enabled')) {
            $rules['sms_enabled'] = 'required|boolean';
            $data['sms_enabled'] = $request->sms_enabled;
        }

        if ($request->has('sms_phone_1')) {
            $rules['sms_phone_1'] = 'nullable|string|max:20';
            $data['sms_phone_1'] = $request->sms_phone_1 ?: null;
        }

        if ($request->has('sms_phone_2')) {
            $rules['sms_phone_2'] = 'nullable|string|max:20';
            $data['sms_phone_2'] = $request->sms_phone_2 ?: null;
        }

        if ($request->has('voice_enabled')) {
            $rules['voice_enabled'] = 'required|boolean';
            $data['voice_enabled'] = $request->voice_enabled;
        }

        if ($request->has('voice_phone_1')) {
            $rules['voice_phone_1'] = 'nullable|string|max:20';
            $data['voice_phone_1'] = $request->voice_phone_1 ?: null;
        }

        if ($request->has('voice_phone_2')) {
            $rules['voice_phone_2'] = 'nullable|string|max:20';
            $data['voice_phone_2'] = $request->voice_phone_2 ?: null;
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
        $errors = [];

        // メール通知
        if ($notif && $notif->email_enabled && $notif->email_1) {
            try {
                $subject = '[みまもりデバイス] テスト通知';
                $body = "テスト通知です。\n\nこのメールが届いていれば、メール通知は正常に動作しています。\n\nデバイス: " . ($device->nickname ?: $device->device_id);
                Mail::to($notif->email_1)->send(new DeviceAlertMail($subject, $body, 'alert'));
                $targets[] = 'メール';
            } catch (\Exception $e) {
                $errors[] = 'メール送信失敗';
            }
        }

        // SMS通知
        if ($notif && $notif->sms_enabled && $notif->sms_phone_1) {
            try {
                $twilio = new TwilioClient(config('services.twilio.sid'), config('services.twilio.token'));
                $name = $device->nickname ?: $device->device_id;
                $twilio->messages->create($notif->sms_phone_1, [
                    'from' => config('services.twilio.from'),
                    'body' => "【みまもりデバイス】テスト通知です。{$name}のSMS通知が正常に動作しています。",
                ]);
                $targets[] = 'SMS';
            } catch (\Exception $e) {
                $errors[] = 'SMS送信失敗';
            }
        }

        if (empty($targets) && empty($errors)) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => '有効な通知先がありません'], 422);
            }
            return redirect('/settings')->with('error', '有効な通知先がありません');
        }

        $message = empty($targets)
            ? implode('・', $errors)
            : 'テスト通知を送信しました（' . implode('・', $targets) . '）';

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => !empty($targets),
                'message' => $message,
            ]);
        }

        return redirect('/settings')->with('success', $message);
    }
}
