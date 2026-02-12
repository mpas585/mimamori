<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PinResetController extends Controller
{
    /**
     * Step 1: デバイスID入力フォーム
     */
    public function showForm()
    {
        return view('auth.pin-reset');
    }

    /**
     * Step 1 → Step 2: デバイスIDを検証して方法選択画面へ
     */
    public function verifyDevice(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string|size:6',
        ], [
            'device_id.required' => '品番を入力してください',
            'device_id.size' => '品番は6文字です',
        ]);

        $deviceId = strtoupper($request->device_id);
        $device = Device::where('device_id', $deviceId)->first();

        if (!$device) {
            return back()->withErrors(['device_id' => '登録情報と一致しません'])->withInput();
        }

        // メールアドレスが登録されているか確認
        $notif = $device->notificationSetting;
        $hasEmail = $notif && $notif->email_1;

        // セッションにデバイスIDを保存（5分間有効）
        $request->session()->put('pin_reset_device_id', $deviceId);
        $request->session()->put('pin_reset_expires', now()->addMinutes(5)->timestamp);

        return view('auth.pin-reset-select', [
            'device_id' => $deviceId,
            'has_email' => $hasEmail,
            'masked_email' => $hasEmail ? $this->maskEmail($notif->email_1) : null,
        ]);
    }

    /**
     * Method A: メールでPIN再設定リンクを送信
     */
    public function sendResetEmail(Request $request)
    {
        if (!$this->isSessionValid($request)) {
            return redirect('/pin-reset')->with('error', 'セッションが切れました。もう一度やり直してください。');
        }

        $deviceId = $request->session()->get('pin_reset_device_id');
        $device = Device::where('device_id', $deviceId)->first();

        if (!$device) {
            return redirect('/pin-reset')->with('error', 'デバイスが見つかりません');
        }

        $notif = $device->notificationSetting;
        if (!$notif || !$notif->email_1) {
            return redirect('/pin-reset')->with('error', 'メールアドレスが登録されていません');
        }

        // 既存のトークンを削除
        DB::table('pin_reset_tokens')->where('device_id', $device->id)->delete();

        // 新しいトークンを生成
        $token = Str::random(64);
        DB::table('pin_reset_tokens')->insert([
            'device_id' => $device->id,
            'token' => hash('sha256', $token),
            'created_at' => now(),
            'expires_at' => now()->addHours(1),
        ]);

        // TODO: 実際のメール送信（Phase1後半で実装）
        // $resetUrl = url('/pin-reset/token/' . $token);
        // Mail::to($notif->email_1)->send(new PinResetMail($device, $resetUrl));

        // セッションクリア
        $request->session()->forget(['pin_reset_device_id', 'pin_reset_expires']);

        return view('auth.pin-reset-email-sent', [
            'masked_email' => $this->maskEmail($notif->email_1),
            // デバッグ用（本番では削除）
            'debug_token' => $token,
            'debug_url' => url('/pin-reset/token/' . $token),
        ]);
    }

    /**
     * Method B: 初期PINリセットフォーム表示
     */
    public function showInitialPinForm(Request $request)
    {
        if (!$this->isSessionValid($request)) {
            return redirect('/pin-reset')->with('error', 'セッションが切れました。もう一度やり直してください。');
        }

        $deviceId = $request->session()->get('pin_reset_device_id');

        return view('auth.pin-reset-initial', [
            'device_id' => $deviceId,
        ]);
    }

    /**
     * Method B: 初期PINで認証して新PINを設定
     */
    public function resetWithInitialPin(Request $request)
    {
        if (!$this->isSessionValid($request)) {
            return redirect('/pin-reset')->with('error', 'セッションが切れました。もう一度やり直してください。');
        }

        $request->validate([
            'initial_pin' => 'required|string|size:4',
            'new_pin' => 'required|string|size:4|confirmed',
        ], [
            'initial_pin.required' => '初期PINを入力してください',
            'initial_pin.size' => 'PINは4桁です',
            'new_pin.required' => '新しいPINを入力してください',
            'new_pin.size' => 'PINは4桁です',
            'new_pin.confirmed' => '新しいPINが一致しません',
        ]);

        $deviceId = $request->session()->get('pin_reset_device_id');
        $device = Device::where('device_id', $deviceId)->first();

        if (!$device) {
            return redirect('/pin-reset')->with('error', 'デバイスが見つかりません');
        }

        // order_devicesテーブルから初期PINを取得して照合
        $orderDevice = DB::table('order_devices')
            ->where('device_id', $device->id)
            ->first();

        if (!$orderDevice || $orderDevice->initial_pin !== $request->initial_pin) {
            return back()->withErrors(['initial_pin' => '初期PINが正しくありません'])->withInput();
        }

        // PINを更新
        $device->update([
            'pin_hash' => Hash::make($request->new_pin),
        ]);

        // セッションクリア
        $request->session()->forget(['pin_reset_device_id', 'pin_reset_expires']);

        return view('auth.pin-reset-complete');
    }

    /**
     * メールリンクから: 新PIN設定フォーム表示
     */
    public function showNewPinForm(string $token)
    {
        $record = DB::table('pin_reset_tokens')
            ->where('token', hash('sha256', $token))
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return redirect('/pin-reset')->with('error', 'リンクが無効または期限切れです。もう一度やり直してください。');
        }

        return view('auth.pin-reset-new-pin', [
            'token' => $token,
        ]);
    }

    /**
     * メールリンクから: 新PINを設定
     */
    public function resetWithToken(Request $request, string $token)
    {
        $request->validate([
            'new_pin' => 'required|string|size:4|confirmed',
        ], [
            'new_pin.required' => '新しいPINを入力してください',
            'new_pin.size' => 'PINは4桁です',
            'new_pin.confirmed' => '新しいPINが一致しません',
        ]);

        $record = DB::table('pin_reset_tokens')
            ->where('token', hash('sha256', $token))
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return redirect('/pin-reset')->with('error', 'リンクが無効または期限切れです。');
        }

        $device = Device::find($record->device_id);
        if (!$device) {
            return redirect('/pin-reset')->with('error', 'デバイスが見つかりません');
        }

        // PINを更新
        $device->update([
            'pin_hash' => Hash::make($request->new_pin),
        ]);

        // トークンを削除
        DB::table('pin_reset_tokens')->where('device_id', $device->id)->delete();

        return view('auth.pin-reset-complete');
    }

    /**
     * セッションの有効性チェック
     */
    private function isSessionValid(Request $request): bool
    {
        $deviceId = $request->session()->get('pin_reset_device_id');
        $expires = $request->session()->get('pin_reset_expires');

        if (!$deviceId || !$expires) {
            return false;
        }

        if (now()->timestamp > $expires) {
            $request->session()->forget(['pin_reset_device_id', 'pin_reset_expires']);
            return false;
        }

        return true;
    }

    /**
     * メールアドレスをマスク表示
     */
    private function maskEmail(string $email): string
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
