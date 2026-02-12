<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class DeviceLoginController extends Controller
{
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

        // デバイスを検索
        $device = Device::where('device_id', strtoupper($request->device_id))->first();

        if (!$device || !Hash::check($request->pin, $device->pin_hash)) {
            throw ValidationException::withMessages([
                'device_id' => ['品番またはPINが正しくありません'],
            ]);
        }

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
