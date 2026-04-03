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
    public function index(Request $request)
    {
        $admin = Auth::guard('partner')->user();
        if ($admin->role === 'operator') {
            return redirect('/partner/org');
        }

        $stats = [
            'total'    => Device::count(),
            'active'   => Device::where('status', '!=', 'inactive')->count(),
            'normal'   => Device::where('status', 'normal')->count(),
            'alert'    => Device::where('status', 'alert')->count(),
            'offline'  => Device::where('status', 'offline')->count(),
            'inactive' => Device::where('status', 'inactive')->count(),
        ];

        $query = Device::with('organization');

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

        if ($request->filled('org')) {
            $query->where('organization_id', $request->org);
        }

        $devices = $query->orderBy('created_at', 'desc')->paginate(20);

        $adminUsers = PartnerUser::orderBy('role', 'asc')->orderBy('created_at', 'desc')->get();

        $organizations = Organization::withCount('devices')->orderBy('created_at', 'desc')->get();

        return view('partner.master', compact('stats', 'devices', 'adminUsers', 'organizations'));
    }

    // ============================================================
    // デバイス発番
    // ============================================================

    public function issueDevice(Request $request)
    {
        $deviceId = $this->generateDeviceId();
        $pin      = $this->generatePin();

        $device = Device::create([
            'device_id' => $deviceId,
            'pin_hash'  => Hash::make($pin),
            'status'    => 'inactive',
        ]);

        DB::table('notification_settings')->insert([
            'device_id'     => $device->id,
            'email_enabled' => 1,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return back()->with('issued', [
            'device_id' => $deviceId,
            'pin'       => $pin,
        ]);
    }

    public function issueBulk(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:100',
        ], [
            'count.required' => '台数を入力してください',
            'count.max'      => '一度に発番できるのは100台までです',
        ]);

        $count  = (int) $request->count;
        $issued = [];

        for ($i = 0; $i < $count; $i++) {
            $deviceId = $this->generateDeviceId();
            $pin      = $this->generatePin();

            $device = Device::create([
                'device_id' => $deviceId,
                'pin_hash'  => Hash::make($pin),
                'status'    => 'inactive',
            ]);

            DB::table('notification_settings')->insert([
                'device_id'     => $device->id,
                'email_enabled' => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $issued[] = [
                'device_id' => $deviceId,
                'pin'       => $pin,
            ];
        }

        return back()->with('issued_bulk', $issued);
    }

    // ============================================================
    // 管理者アカウント管理
    // ============================================================

    public function storeAdminUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:255|unique:admin_users,email',
            'password' => 'required|string|min:8|max:100',
            'role'     => 'required|in:master,operator',
        ], [
            'name.required'     => '名前を入力してください',
            'email.required'    => 'メールアドレスを入力してください',
            'email.unique'      => 'このメールアドレスは既に使用されています',
            'password.required' => 'パスワードを入力してください',
            'password.min'      => 'パスワードは8文字以上にしてください',
        ]);

        PartnerUser::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password_hash' => Hash::make($request->password),
            'role'          => $request->role,
            'organization_id' => $request->filled('organization_id') ? (int) $request->organization_id : null,
        ]);

        return redirect('/partner?tab=admins')->with('success', '管理者アカウント「' . $request->name . '」を作成しました');
    }

    public function updateAdminUser(Request $request, int $id)
    {
        $admin = PartnerUser::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => ['required', 'email', 'max:255', Rule::unique('admin_users', 'email')->ignore($admin->id)],
            'password' => 'nullable|string|min:8|max:100',
            'role'     => 'required|in:master,operator',
        ], [
            'name.required'  => '名前を入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.unique'   => 'このメールアドレスは既に使用されています',
            'password.min'   => 'パスワードは8文字以上にしてください',
        ]);

        $admin->name  = $request->name;
        $admin->email = $request->email;
        $admin->role  = $request->role;
        $admin->organization_id = $request->filled('organization_id') ? (int) $request->organization_id : null;

        if ($request->filled('password')) {
            $admin->password_hash = Hash::make($request->password);
        }

        $admin->save();

        return redirect('/partner?tab=admins')->with('success', '管理者アカウント「' . $request->name . '」を更新しました');
    }

    public function destroyAdminUser(int $id)
    {
        $admin = PartnerUser::findOrFail($id);

        if ($admin->id === Auth::guard('partner')->id()) {
            return redirect('/partner?tab=admins')->with('error', '自分自身のアカウントは削除できません');
        }

        $name = $admin->name;
        $admin->delete();

        return redirect('/partner?tab=admins')->with('success', '管理者アカウント「' . $name . '」を削除しました');
    }

    // ============================================================
    // 組織管理
    // ============================================================

    /**
     * 組織新規作成
     */
    public function storeOrg(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:200',
            'contact_name'  => 'nullable|string|max:100',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
            'notes'         => 'nullable|string|max:1000',
            'device_limit'  => 'nullable|integer|min:1|max:9999',
            'expires_at'    => 'nullable|date',
        ], [
            'name.required'          => '組織名を入力してください',
            'contact_email.required' => '連絡先メールを入力してください',
            'contact_email.email'    => '正しいメールアドレスを入力してください',
        ]);

        $org = Organization::create([
            'name'          => $request->name,
            'contact_name'  => $request->contact_name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'address'       => $request->address,
            'notes'         => $request->notes,
            'device_limit'  => $request->filled('device_limit') ? (int) $request->device_limit : 100,
            'expires_at'    => $request->expires_at ?: null,
            'premium_enabled' => false,
        ]);

        return redirect('/partner?tab=orgs')->with('success', '組織「' . $org->name . '」を作成しました');
    }

    /**
     * 組織更新
     */
    public function updateOrg(Request $request, int $id)
    {
        $org = Organization::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:200',
            'contact_name'  => 'nullable|string|max:100',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
            'notes'         => 'nullable|string|max:1000',
            'device_limit'  => 'nullable|integer|min:1|max:9999',
            'expires_at'    => 'nullable|date',
            'notification_email_1' => 'nullable|email|max:255',
            'notification_email_2' => 'nullable|email|max:255',
            'notification_email_3' => 'nullable|email|max:255',
            'notification_sms_1'   => 'nullable|string|max:20',
            'notification_sms_2'   => 'nullable|string|max:20',
        ], [
            'name.required'          => '組織名を入力してください',
            'contact_email.required' => '連絡先メールを入力してください',
            'contact_email.email'    => '正しいメールアドレスを入力してください',
        ]);

        $org->update([
            'name'          => $request->name,
            'contact_name'  => $request->contact_name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'address'       => $request->address,
            'notes'         => $request->notes,
            'device_limit'  => $request->filled('device_limit') ? (int) $request->device_limit : $org->device_limit,
            'expires_at'    => $request->expires_at ?: null,
            'notification_email_1' => $request->notification_email_1 ?: null,
            'notification_email_2' => $request->notification_email_2 ?: null,
            'notification_email_3' => $request->notification_email_3 ?: null,
            'notification_sms_1'   => $request->notification_sms_1 ?: null,
            'notification_sms_2'   => $request->notification_sms_2 ?: null,
        ]);

        return redirect('/partner?tab=orgs')->with('success', '組織「' . $org->name . '」を更新しました');
    }

    /**
     * 組織削除（デバイスが0台の場合のみ）
     */
    public function destroyOrg(int $id)
    {
        $org = Organization::withCount('devices')->findOrFail($id);

        if ($org->devices_count > 0) {
            return redirect('/partner?tab=orgs')->with('error', '「' . $org->name . '」にはデバイスが登録されているため削除できません');
        }

        $name = $org->name;
        $org->delete();

        return redirect('/partner?tab=orgs')->with('success', '組織「' . $name . '」を削除しました');
    }

    /**
     * 組織プレミアムトグル（AJAX）
     */
    public function toggleOrgPremium(Request $request, int $orgId)
    {
        $org = Organization::findOrFail($orgId);

        $request->validate([
            'premium_enabled' => 'required|boolean',
        ]);

        $org->update(['premium_enabled' => (bool) $request->premium_enabled]);

        return response()->json([
            'success'         => true,
            'premium_enabled' => $org->premium_enabled,
        ]);
    }

    // ============================================================
    // ヘルパー
    // ============================================================

    private function generateDeviceId(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $id = '';
            for ($i = 0; $i < 6; $i++) {
                $id .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while (Device::where('device_id', $id)->exists());

        return $id;
    }

    private function generatePin(): string
    {
        return str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }
}
