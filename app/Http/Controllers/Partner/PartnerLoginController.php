<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Http\Traits\ThrottlesLoginAttempts;
use App\Models\PartnerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PartnerLoginController extends Controller
{
    use ThrottlesLoginAttempts;

    public function showLoginForm()
    {
        return view('partner.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'メールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',
        ]);

        // アカウントロックチェック
        $this->checkTooManyAttempts($request);

        $admin = PartnerUser::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password_hash)) {
            // 失敗回数をインクリメント
            $this->incrementAttempts($request);

            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません'],
            ]);
        }

        // 成功 → カウンタクリア
        $this->clearAttempts($request);

        Auth::guard('partner')->login($admin, $request->boolean('remember'));
        $admin->update(['last_login_at' => now()]);
        $request->session()->regenerate();

        // roleで振り分け
        if ($admin->isMaster()) {
            return redirect('/partner');
        }

        return redirect('/partner/org');
    }

    public function logout(Request $request)
    {
        Auth::guard('partner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/partner/login');
    }
}
