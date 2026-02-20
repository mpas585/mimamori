<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminPasswordController extends Controller
{
    /**
     * アカウント設定画面表示
     */
    public function showForm()
    {
        $admin = Auth::guard('admin')->user();

        return view('admin.password-change', [
            'admin' => $admin,
        ]);
    }

    /**
     * パスワード変更処理
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => '現在のパスワードを入力してください',
            'new_password.required' => '新しいパスワードを入力してください',
            'new_password.min' => '新しいパスワードは8文字以上にしてください',
            'new_password.confirmed' => '新しいパスワードが一致しません',
        ]);

        $admin = Auth::guard('admin')->user();

        // 現在のパスワード確認
        if (!Hash::check($request->current_password, $admin->password_hash)) {
            return back()->withErrors(['current_password' => '現在のパスワードが正しくありません'])->withInput();
        }

        // 新旧パスワード同一チェック
        if ($request->current_password === $request->new_password) {
            return back()->withErrors(['new_password' => '新しいパスワードは現在と異なるものにしてください'])->withInput();
        }

        $admin->update([
            'password_hash' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'パスワードを変更しました');
    }

    /**
     * メールアドレス変更処理
     */
    public function updateEmail(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'email' => 'required|email|unique:admin_users,email,' . $admin->id,
            'email_password' => 'required|string',
        ], [
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => '正しいメールアドレスを入力してください',
            'email.unique' => 'このメールアドレスは既に使用されています',
            'email_password.required' => 'パスワードを入力してください',
        ]);

        // パスワード確認
        if (!Hash::check($request->email_password, $admin->password_hash)) {
            return back()->withErrors(['email_password' => 'パスワードが正しくありません'])->withInput();
        }

        // 同じメールアドレスチェック
        if ($admin->email === $request->email) {
            return back()->withErrors(['email' => '現在と同じメールアドレスです'])->withInput();
        }

        $admin->update([
            'email' => $request->email,
        ]);

        return back()->with('success', 'メールアドレスを変更しました');
    }
}
