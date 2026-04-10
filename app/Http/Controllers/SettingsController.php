<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationSetting;
use App\Mail\DeviceAlertMail;
use App\Helpers\PhoneHelper;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client as TwilioClient;

class SettingsController extends Controller
{
    public function index()
    {
        $device = Auth::user();
        $notif = $device->notificationSetting ?? NotificationSetting::create(['device_id' => $device->id]);

        return view('settings', compact('device', 'notif'));
    }

    public function updateDevice(Request $request)
    {
        $device = Auth::user();

        $rules = [];
        $data = [];

        if ($request->has('alert_threshold_hours')) {
            $rules['alert_threshold_hours'] = 'required|in:3,6,12,24,36,48,72';
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

    public function updateNotification(Request $request)
    {
        $device = Auth::user();
        $notif = $device->notificationSetting ?? NotificationSetting::create(['device_id' => $device->id]);

        // ★ プレミアムガード
        $premiumFields = ['sms_enabled', 'sms_phone_1', 'sms_phone_2', 'voice_enabled', 'voice_phone_1', 'voice_phone_2'];
        $hasPremiumField = collect($premiumFields)->some(fn($f) => $request->has($f));

        if ($hasPremiumField && !$device->premium_enabled) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'プレミアムプランが必要です',
                    'upgrade' => true,
                ], 403);
            }
            return redirect('/plan')->with('error', 'SMS・電話通知はプレミアムプランで利用できます');
        }

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
            $data['sms_phone_1'] = PhoneHelper::normalize($request->sms_phone_1);
        }

        if ($request->has('sms_phone_2')) {
            $data['sms_phone_2'] = PhoneHelper::normalize($request->sms_phone_2);
        }

        if ($request->has('voice_enabled')) {
            $rules['voice_enabled'] = 'required|boolean';
            $data['voice_enabled'] = $request->voice_enabled;
        }

        if ($request->has('voice_phone_1')) {
            $data['voice_phone_1'] = PhoneHelper::normalize($request->voice_phone_1);
        }

        if ($request->has('voice_phone_2')) {
            $data['voice_phone_2'] = PhoneHelper::normalize($request->voice_phone_2);
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

    public function sendTestNotification(Request $request)
    {
        $device = Auth::user();
        $notif = $device->notificationSetting;

        $targets = [];
        $errors = [];

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

        // ★ プレミアムの場合のみSMS送信
        if ($device->premium_enabled && $notif && $notif->sms_enabled && $notif->sms_phone_1) {
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
            return response()->json(['ok' => !empty($targets), 'message' => $message]);
        }

        return redirect('/settings')->with('success', $message);
    }
}
