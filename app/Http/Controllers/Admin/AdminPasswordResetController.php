<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminPasswordResetMail;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminPasswordResetController extends Controller
{
    /**
     * パスワードリセット申請フォーム表示
     */
    public function showRequestForm()
    {
        return view('admin.password-reset');
    }

    /**
     * リセットメール送信
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => '正しいメールアドレスを入力してください',
        ]);

        $admin = AdminUser::where('email', $request->email)->first();

        if (!$admin) {
            return back()->withErrors(['email' => 'このメールアドレスは登録されていません'])->withInput();
        }

        // 既存トークンを削除
        DB::table('admin_password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // 新しいトークンを生成
        $token = Str::random(64);

        DB::table('admin_password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        // メール送信
        $resetUrl = url('/admin/password-reset/' . $token . '?email=' . urlencode($request->email));
        Mail::to($request->email)->send(new AdminPasswordResetMail($resetUrl, $admin->name));

        return view('admin.password-reset', [
            'sent'  => true,
            'email' => $request->email,
        ]);
    }

    /**
     * 新パスワード設定フォーム表示
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('admin.password-reset-new', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * パスワードリセット実行
     */
    public function reset(Request $request, string $token)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required'     => 'メールアドレスが指定されていません',
            'password.required'  => '新しいパスワードを入力してください',
            'password.min'       => 'パスワードは8文字以上で入力してください',
            'password.confirmed' => 'パスワードが一致しません',
        ]);

        // トークン検証
        $record = DB::table('admin_password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'パスワードリセットの申請が見つかりません'])->withInput();
        }

        // トークン一致チェック
        if (!Hash::check($token, $record->token)) {
            return back()->withErrors(['email' => 'リセットリンクが無効です'])->withInput();
        }

        // 有効期限チェック（1時間）
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('admin_password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return back()->withErrors(['email' => 'リセットリンクの有効期限が切れています。再度申請してください。'])->withInput();
        }

        // パスワード更新
        $admin = AdminUser::where('email', $request->email)->first();

        if (!$admin) {
            return back()->withErrors(['email' => 'アカウントが見つかりません'])->withInput();
        }

        $admin->update([
            'password_hash' => Hash::make($request->password),
        ]);

        // 使用済みトークン削除
        DB::table('admin_password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return redirect('/admin/login')
            ->with('success', 'パスワードを変更しました。新しいパスワードでログインしてください。');
    }
}
