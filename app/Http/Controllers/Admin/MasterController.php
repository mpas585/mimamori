<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MasterController extends Controller
{
    /**
     * ダッシュボード（デバイス一覧 + 統計 + 管理者一覧）
     */
    public function index(Request $request)
    {
        // 統計
        $stats = [
            'total' => Device::count(),
            'active' => Device::where('status', '!=', 'inactive')->count(),
            'normal' => Device::where('status', 'normal')->count(),
            'alert' => Device::where('status', 'alert')->count(),
            'offline' => Device::where('status', 'offline')->count(),
            'inactive' => Device::where('status', 'inactive')->count(),
        ];

        // デバイス一覧（検索・フィルタ対応）
        $query = Device::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('device_id', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $devices = $query->orderBy('created_at', 'desc')->paginate(20);

        // 管理者一覧
        $adminUsers = AdminUser::orderBy('role', 'asc')->orderBy('created_at', 'desc')->get();

        return view('admin.master', compact('stats', 'devices', 'adminUsers'));
    }

    /**
     * デバイス発番（1台）
     */
    public function issueDevice(Request $request)
    {
        $deviceId = $this->generateDeviceId();
        $pin = $this->generatePin();

        $device = Device::create([
            'device_id' => $deviceId,
            'pin_hash' => Hash::make($pin),
            'status' => 'inactive',
        ]);

        // 通知設定を初期作成
        DB::table('notification_settings')->insert([
            'device_id' => $device->id,
            'email_enabled' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('issued', [
            'device_id' => $deviceId,
            'pin' => $pin,
        ]);
    }

    /**
     * デバイス一括発番
     */
    public function issueBulk(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:100',
        ], [
            'count.required' => '台数を入力してください',
            'count.max' => '一度に発番できるのは100台までです',
        ]);

        $count = (int) $request->count;
        $issued = [];

        for ($i = 0; $i < $count; $i++) {
            $deviceId = $this->generateDeviceId();
            $pin = $this->generatePin();

            $device = Device::create([
                'device_id' => $deviceId,
                'pin_hash' => Hash::make($pin),
                'status' => 'inactive',
            ]);

            DB::table('notification_settings')->insert([
                'device_id' => $device->id,
                'email_enabled' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $issued[] = [
                'device_id' => $deviceId,
                'pin' => $pin,
            ];
        }

        return back()->with('issued_bulk', $issued);
    }

    // ============================================================
    // 管理者アカウント管理
    // ============================================================

    /**
     * 管理者アカウント作成
     */
    public function storeAdminUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:admin_users,email',
            'password' => 'required|string|min:8|max:100',
            'role' => 'required|in:master,operator',
        ], [
            'name.required' => '名前を入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.unique' => 'このメールアドレスは既に使用されています',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは8文字以上にしてください',
        ]);

        AdminUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect('/admin?tab=admins')->with('success', '管理者アカウント「' . $request->name . '」を作成しました');
    }

    /**
     * 管理者アカウント更新
     */
    public function updateAdminUser(Request $request, int $id)
    {
        $admin = AdminUser::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:255', Rule::unique('admin_users', 'email')->ignore($admin->id)],
            'password' => 'nullable|string|min:8|max:100',
            'role' => 'required|in:master,operator',
        ], [
            'name.required' => '名前を入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.unique' => 'このメールアドレスは既に使用されています',
            'password.min' => 'パスワードは8文字以上にしてください',
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->role = $request->role;

        if ($request->filled('password')) {
            $admin->password_hash = Hash::make($request->password);
        }

        $admin->save();

        return redirect('/admin?tab=admins')->with('success', '管理者アカウント「' . $request->name . '」を更新しました');
    }

    /**
     * 管理者アカウント削除
     */
    public function destroyAdminUser(int $id)
    {
        $admin = AdminUser::findOrFail($id);

        // 自分自身は削除不可
        if ($admin->id === Auth::guard('admin')->id()) {
            return redirect('/admin?tab=admins')->with('error', '自分自身のアカウントは削除できません');
        }

        $name = $admin->name;
        $admin->delete();

        return redirect('/admin?tab=admins')->with('success', '管理者アカウント「' . $name . '」を削除しました');
    }

    // ============================================================
    // ヘルパー
    // ============================================================

    /**
     * デバイスIDを生成（英数字6文字、重複なし）
     */
    private function generateDeviceId(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // 紛らわしい文字を除外（0,O,1,I）

        do {
            $id = '';
            for ($i = 0; $i < 6; $i++) {
                $id .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while (Device::where('device_id', $id)->exists());

        return $id;
    }

    /**
     * PIN生成（数字4桁）
     */
    private function generatePin(): string
    {
        return str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }
}
