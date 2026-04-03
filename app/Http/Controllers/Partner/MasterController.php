<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\PartnerUser;
use App\Models\Device;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MasterController extends Controller
{
    /**
     * ダチE��ュボ�Eド（デバイス一覧 + 統訁E+ 管琁E��E��覧�E�E     */
    public function index(Request $request)
    {
        // operatorは自刁E�E絁E��ダチE��ュボ�EドへリダイレクチE        $admin = Auth::guard('partner')->user();
        if ($admin->role === 'operator') {
            return redirect('/partner/org');
        }

        // 統訁E        $stats = [
            'total' => Device::count(),
            'active' => Device::where('status', '!=', 'inactive')->count(),
            'normal' => Device::where('status', 'normal')->count(),
            'alert' => Device::where('status', 'alert')->count(),
            'offline' => Device::where('status', 'offline')->count(),
            'inactive' => Device::where('status', 'inactive')->count(),
        ];

        // チE��イス一覧�E�検索・フィルタ対応！E        $query = Device::query();

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

        // 管琁E��E��覧
        $adminUsers = PartnerUser::orderBy('role', 'asc')->orderBy('created_at', 'desc')->get();

        $organizations = Organization::withCount('devices')->orderBy('created_at', 'desc')->get();

        return view('partner.master', compact('stats', 'devices', 'adminUsers', 'organizations'));
    }

    /**
     * チE��イス発番�E�E台�E�E     */
    public function issueDevice(Request $request)
    {
        $deviceId = $this->generateDeviceId();
        $pin = $this->generatePin();

        $device = Device::create([
            'device_id' => $deviceId,
            'pin_hash' => Hash::make($pin),
            'status' => 'inactive',
        ]);

        // 通知設定を初期作�E
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
     * チE��イス一括発番
     */
    public function issueBulk(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:100',
        ], [
            'count.required' => '台数を�E力してください',
            'count.max' => '一度に発番できるのは100台まででぁE,
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
    // 管琁E��E��カウント管琁E    // ============================================================

    /**
     * 管琁E��E��カウント作�E
     */
    public function storeAdminUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:admin_users,email',
            'password' => 'required|string|min:8|max:100',
            'role' => 'required|in:master,operator',
        ], [
            'name.required' => '名前を�E力してください',
            'email.required' => 'メールアドレスを�E力してください',
            'email.unique' => 'こ�Eメールアドレスは既に使用されてぁE��ぁE,
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワード�E8斁E��以上にしてください',
        ]);

        PartnerUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect('/partner?tab=admins')->with('success', '管琁E��E��カウント、E . $request->name . '」を作�Eしました');
    }

    /**
     * 管琁E��E��カウント更新
     */
    public function updateAdminUser(Request $request, int $id)
    {
        $admin = PartnerUser::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:255', Rule::unique('admin_users', 'email')->ignore($admin->id)],
            'password' => 'nullable|string|min:8|max:100',
            'role' => 'required|in:master,operator',
        ], [
            'name.required' => '名前を�E力してください',
            'email.required' => 'メールアドレスを�E力してください',
            'email.unique' => 'こ�Eメールアドレスは既に使用されてぁE��ぁE,
            'password.min' => 'パスワード�E8斁E��以上にしてください',
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->role = $request->role;

        if ($request->filled('password')) {
            $admin->password_hash = Hash::make($request->password);
        }

        $admin->save();

        return redirect('/partner?tab=admins')->with('success', '管琁E��E��カウント、E . $request->name . '」を更新しました');
    }

    /**
     * 管琁E��E��カウント削除
     */
    public function destroyAdminUser(int $id)
    {
        $admin = PartnerUser::findOrFail($id);

        // 自刁E�E身は削除不可
        if ($admin->id === Auth::guard('partner')->id()) {
            return redirect('/partner?tab=admins')->with('error', '自刁E�E身のアカウント�E削除できません');
        }

        $name = $admin->name;
        $admin->delete();

        return redirect('/partner?tab=admins')->with('success', '管琁E��E��カウント、E . $name . '」を削除しました');
    }

    // ============================================================
    // ヘルパ�E
    // ============================================================

    /**
     * チE��イスIDを生成（英数孁E斁E��、E��褁E��し！E     */
    private function generateDeviceId(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // 紛らわしぁE��字を除外！E,O,1,I�E�E
        do {
            $id = '';
            for ($i = 0; $i < 6; $i++) {
                $id .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while (Device::where('device_id', $id)->exists());

        return $id;
    }

    /**
     * PIN生�E�E�数孁E桁E��E     */
    private function generatePin(): string
    {
        return str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }
}


