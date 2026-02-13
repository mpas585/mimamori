<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\EmailVerificationMail;

class EmailSettingsController extends Controller
{
    /**
     * メールアドレス設定画面表示
     */
    public function index()
    {
        $device = Auth::user();
        $notif = $device->notificationSetting;
        $currentEmail = $notif ? $notif->email_1 : null;

        return view('email-settings', [
            'currentEmail' => $currentEmail,
            'device' => $device,
        ]);
    }

    /**
     * 確認メール送信
     */
    public function sendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'email_confirmation' => 'required|same:email',
        ], [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => '有効なメールアドレスを入力してください。',
            'email_confirmation.same' => 'メールアドレスが一致しません。',
        ]);

        $device = Auth::user();
        $email = $request->email;

        // 現在のメアドと同じならエラー
        $notif = $device->notificationSetting;
        if ($notif && $notif->email_1 === $email) {
            return back()->withErrors(['email' => '現在のメールアドレスと同じです。'])->withInput();
        }

        // 古いトークンを削除
        DB::table('email_verification_tokens')
            ->where('device_id', $device->id)
            ->whereNull('verified_at')
            ->delete();

        // 新しいトークン生成
        $token = Str::random(64);
        DB::table('email_verification_tokens')->insert([
            'device_id' => $device->id,
            'email' => $email,
            'token' => $token,
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
        ]);

        // 確認メール送信
        try {
            Mail::to($email)->send(new EmailVerificationMail($device, $token));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'メールの送信に失敗しました。時間をおいて再度お試しください。'])->withInput();
        }

        // マスク処理してセッションに保存
        $masked = $this->maskEmail($email);

        return redirect()->route('email-settings.sent')->with('masked_email', $masked);
    }

    /**
     * 送信完了画面
     */
    public function sent()
    {
        $maskedEmail = session('masked_email');
        if (!$maskedEmail) {
            return redirect()->route('email-settings');
        }

        return view('email-settings-sent', [
            'maskedEmail' => $maskedEmail,
        ]);
    }

    /**
     * メール内リンクからの認証
     */
    public function verify($token)
    {
        $record = DB::table('email_verification_tokens')
            ->where('token', $token)
            ->whereNull('verified_at')
            ->first();

        if (!$record) {
            return view('email-settings-result', [
                'success' => false,
                'message' => 'このリンクは無効です。',
            ]);
        }

        if (now()->gt($record->expires_at)) {
            return view('email-settings-result', [
                'success' => false,
                'message' => 'このリンクの有効期限が切れています。再度メールアドレスの設定をやり直してください。',
            ]);
        }

        // トークンを使用済みにする
        DB::table('email_verification_tokens')
            ->where('id', $record->id)
            ->update(['verified_at' => now()]);

        // notification_settingsのemail_1を更新
        $device = \App\Models\Device::find($record->device_id);
        if ($device) {
            $device->notificationSetting()->updateOrCreate(
                ['device_id' => $device->id],
                [
                    'email_1' => $record->email,
                    'email_enabled' => true,
                ]
            );
        }

        return view('email-settings-result', [
            'success' => true,
            'message' => 'メールアドレスの登録が完了しました。',
            'email' => $record->email,
        ]);
    }

    /**
     * メールアドレス削除
     */
    public function delete(Request $request)
    {
        $device = Auth::user();
        $notif = $device->notificationSetting;

        if ($notif) {
            $notif->update([
                'email_1' => null,
                'email_enabled' => false,
            ]);
        }

        return redirect()->route('email-settings')->with('success', 'メールアドレスを削除しました。');
    }

    /**
     * メールアドレスマスク処理
     */
    private function maskEmail($email)
    {
        $parts = explode('@', $email);
        $local = $parts[0];
        $domain = $parts[1];

        if (strlen($local) <= 2) {
            return $local[0] . '***@' . $domain;
        }

        return substr($local, 0, 2) . '***@' . $domain;
    }
}
