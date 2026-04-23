<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\ThrottlesLoginAttempts;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class DeviceLoginController extends Controller
{
    use ThrottlesLoginAttempts;

    protected function username(): string
    {
        return 'device_id';
    }

    /**
     * ログインフォーム表示
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * ログイン処理（device_id + PIN）
     */
    public function login(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string|size:6',
            'pin' => 'required|string|size:4',
        ], [
            'device_id.required' => '品番を入力してください',
            'device_id.size' => '品番は6文字です',
            'pin.required' => 'PINを入力してください',
            'pin.size' => 'PINは4桁です',
        ]);

        // アカウントロックチェック
        $this->checkTooManyAttempts($request);

        // デバイスを検索
        $device = Device::where('device_id', strtoupper($request->device_id))->first();

        if (!$device || !Hash::check($request->pin, $device->pin_hash)) {
            // 失敗回数をインクリメント
            $this->incrementAttempts($request);

            throw ValidationException::withMessages([
                'device_id' => ['品番またはPINが正しくありません'],
            ]);
        }

        // サービス停止中チェック（課金失敗30日経過でsuspended_atが立つ）
        if ($device->suspended_at) {
            throw ValidationException::withMessages([
                'device_id' => ['このデバイスはお支払い未確認のためサービスを停止しています。お問い合わせフォームよりご連絡ください。'],
            ]);
        }

        // 成功 → カウンタクリア
        $this->clearAttempts($request);

        // ログイン
        Auth::login($device, $request->boolean('remember'));

        // 初回ログイン時にactivated_atを記録
        if (is_null($device->activated_at)) {
            $device->update(['activated_at' => now()]);
        }

        $request->session()->regenerate();

        return redirect()->intended('/mypage');
    }

    /**
     * ログアウト
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
