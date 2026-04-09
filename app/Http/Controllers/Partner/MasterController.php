<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\PartnerUser;
use App\Models\Device;
use App\Models\Organization;
use App\Models\OrgDeviceAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\BillingLog;

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
            if ($request->org === 'none') {
                $query->whereNull('organization_id');
            } else {
                $query->where('organization_id', $request->org);
            }
        }

        $devices       = $query->orderBy('created_at', 'desc')->paginate(20);
        // masterアカウントのみ
        $adminUsers    = PartnerUser::where('role', 'master')->orderBy('created_at', 'desc')->get();
        $organizations = Organization::withCount('devices')
            ->with(['partnerUsers' => function ($q) { $q->where('role', 'operator'); }])
            ->orderBy('created_at', 'desc')->get();

        $salesData = $this->buildSalesData();
        return view('partner.master', compact('stats', 'devices', 'adminUsers', 'organizations', 'salesData'));
    }

    // ============================================================
    // デバイス発番
    // ============================================================

    public function issueDevice(Request $request)
    {
        $deviceId = $this->generateDeviceId();
        $pin      = $this->generatePin();

        $device = Device::create(['device_id' => $deviceId, 'pin_hash' => Hash::make($pin), 'status' => 'inactive']);

        DB::table('notification_settings')->insert(['device_id' => $device->id, 'email_enabled' => 1, 'created_at' => now(), 'updated_at' => now()]);

        return back()->with('issued', ['device_id' => $deviceId, 'pin' => $pin]);
    }

    public function issueBulk(Request $request)
    {
        $request->validate(['count' => 'required|integer|min:1|max:100'], [
            'count.required' => '台数を入力してください',
            'count.max'      => '一度に発番できるのは100台までです',
        ]);

        $count  = (int) $request->count;
        $issued = [];

        for ($i = 0; $i < $count; $i++) {
            $deviceId = $this->generateDeviceId();
            $pin      = $this->generatePin();
            $device   = Device::create(['device_id' => $deviceId, 'pin_hash' => Hash::make($pin), 'status' => 'inactive']);
            DB::table('notification_settings')->insert(['device_id' => $device->id, 'email_enabled' => 1, 'created_at' => now(), 'updated_at' => now()]);
            $issued[] = ['device_id' => $deviceId, 'pin' => $pin];
        }

        return back()->with('issued_bulk', $issued);
    }

    // ============================================================
    // デバイス詳細・編集系
    // ============================================================

    public function deviceDetail(string $deviceId)
    {
        $device = Device::where('device_id', $deviceId)
            ->with(['organization', 'orgAssignment', 'notificationSetting', 'schedules' => function ($q) {
                $q->where('is_active', true)->orderBy('created_at', 'desc');
            }])
            ->firstOrFail();

        $assignment = $device->orgAssignment;
        $notif      = $device->notificationSetting;

        $rssiLabel = '-';
        if ($device->rssi !== null) {
            if ($device->rssi > -70)     $rssiLabel = '良好 (' . $device->rssi . 'dBm)';
            elseif ($device->rssi > -85) $rssiLabel = '普通 (' . $device->rssi . 'dBm)';
            else                          $rssiLabel = '弱い (' . $device->rssi . 'dBm)';
        }

        $dayNames  = ['日', '月', '火', '水', '木', '金', '土'];
        $schedules = $device->schedules->map(function ($s) use ($dayNames) {
            $data = ['id' => $s->id, 'type' => $s->type, 'memo' => $s->memo];
            if ($s->type === 'oneshot') {
                $data['start_at'] = $s->start_at ? $s->start_at->format('Y-m-d H:i') : null;
                $data['end_at']   = $s->end_at   ? $s->end_at->format('Y-m-d H:i')   : null;
            } else {
                $days = $s->days_of_week ?? [];
                $data['days_label'] = implode('・', array_map(fn($d) => $dayNames[$d] ?? '', $days));
                $data['start_time'] = $s->start_time;
                $data['end_time']   = $s->end_time;
                $data['next_day']   = $s->next_day;
            }
            return $data;
        });

        return response()->json([
            'device_id'                    => $device->device_id,
            'sim_id'                       => $device->sim_id,
            'notification_service_enabled' => (bool) $device->notification_service_enabled,
            'status'                       => $device->status,
            'organization_id'              => $device->organization_id,
            'organization_name'            => $device->organization ? $device->organization->name : null,
            'room_number'                  => $assignment ? $assignment->room_number : null,
            'tenant_name'                  => $assignment ? $assignment->tenant_name : null,
            'last_received_at'             => $device->last_received_at       ? $device->last_received_at->format('Y/m/d H:i')      : null,
            'last_human_detected_at'       => $device->last_human_detected_at ? $device->last_human_detected_at->format('Y/m/d H:i') : null,
            'battery_pct'                  => $device->battery_pct,
            'battery_voltage'              => $device->battery_voltage,
            'rssi_label'                   => $rssiLabel,
            'alert_threshold_hours'        => $device->alert_threshold_hours,
            'pet_exclusion_enabled'        => (bool) $device->pet_exclusion_enabled,
            'install_height_cm'            => $device->install_height_cm,
            'away_mode'                    => (bool) $device->away_mode,
            'away_until'                   => $device->away_until ? $device->away_until->format('Y/m/d H:i') : null,
            'memo'                         => $device->location_memo,
            'registered_at'                => $device->created_at->format('Y/m/d'),
            'billing_start_date'           => $device->billing_start_date ? $device->billing_start_date->format('Y-m-d') : null,
            'schedules'                    => $schedules,
            'sms_enabled'                  => $notif ? (bool) $notif->sms_enabled   : false,
            'sms_phone_1'                  => $notif && $notif->sms_phone_1 ? preg_replace('/^\+81/', '0', $notif->sms_phone_1) : null,
            'sms_phone_2'                  => $notif && $notif->sms_phone_2 ? preg_replace('/^\+81/', '0', $notif->sms_phone_2) : null,
            'voice_enabled'                => $notif ? (bool) $notif->voice_enabled : false,
            'voice_phone_1'                => $notif && $notif->voice_phone_1 ? preg_replace('/^\+81/', '0', $notif->voice_phone_1) : null,
            'voice_phone_2'                => $notif && $notif->voice_phone_2 ? preg_replace('/^\+81/', '0', $notif->voice_phone_2) : null,
        ]);
    }

    public function updateDeviceAssignment(Request $request, string $deviceId)
    {
        $device = Device::where('device_id', $deviceId)->firstOrFail();

        $request->validate([
            'room_number'           => 'nullable|string|max:50',
            'tenant_name'           => 'nullable|string|max:100',
            'memo'                  => 'nullable|string|max:255',
            'alert_threshold_hours' => 'nullable|integer|in:12,24,36,48,72',
            'install_height_cm'     => 'nullable|integer|min:100|max:300',
            'pet_exclusion_enabled' => 'nullable|boolean',
            'billing_start_date'    => 'nullable|date',
            'sim_id'                => [
                'nullable',
                'string',
                'max:22',
                'regex:/^[0-9]+$/',
                Rule::unique('devices', 'sim_id')->ignore($device->id),
            ],
        ], [
            'sim_id.max'    => 'SIM IDは22桁以内で入力してください',
            'sim_id.regex'  => 'SIM IDは数字のみ使用できます',
            'sim_id.unique' => 'このSIM IDは既に別のデバイスに登録されています',
        ]);

        if ($device->organization_id) {
            OrgDeviceAssignment::updateOrCreate(
                ['organization_id' => $device->organization_id, 'device_id' => $device->id],
                ['room_number' => $request->room_number, 'tenant_name' => $request->tenant_name]
            );
        }

        $device->update([
            'location_memo'         => $request->memo,
            'alert_threshold_hours' => $request->alert_threshold_hours ?? $device->alert_threshold_hours,
            'install_height_cm'     => $request->install_height_cm     ?? $device->install_height_cm,
            'pet_exclusion_enabled' => $request->has('pet_exclusion_enabled') ? (int) $request->pet_exclusion_enabled : $device->pet_exclusion_enabled,
            'billing_start_date'    => $request->billing_start_date    ?? $device->billing_start_date,
            'sim_id'                => $request->filled('sim_id') ? $request->sim_id : null,
        ]);

        return response()->json(['success' => true, 'message' => '更新しました']);
    }

    public function updateDeviceNotification(Request $request, string $deviceId)
    {
        $device = Device::where('device_id', $deviceId)->firstOrFail();

        $request->validate([
            'sms_enabled'   => 'nullable|boolean',
            'sms_phone_1'   => 'nullable|string|max:20',
            'sms_phone_2'   => 'nullable|string|max:20',
            'voice_enabled' => 'nullable|boolean',
            'voice_phone_1' => 'nullable|string|max:20',
            'voice_phone_2' => 'nullable|string|max:20',
        ]);

        $notif = $device->notificationSetting;
        if (!$notif) {
            $notif = \App\Models\NotificationSetting::create(['device_id' => $device->id]);
        }

        $data = [];
        if ($request->has('sms_enabled'))   $data['sms_enabled']   = (bool) $request->sms_enabled;
        if ($request->has('sms_phone_1'))   $data['sms_phone_1']   = \App\Helpers\PhoneHelper::normalize($request->sms_phone_1);
        if ($request->has('sms_phone_2'))   $data['sms_phone_2']   = \App\Helpers\PhoneHelper::normalize($request->sms_phone_2);
        if ($request->has('voice_enabled')) $data['voice_enabled'] = (bool) $request->voice_enabled;
        if ($request->has('voice_phone_1')) $data['voice_phone_1'] = \App\Helpers\PhoneHelper::normalize($request->voice_phone_1);
        if ($request->has('voice_phone_2')) $data['voice_phone_2'] = \App\Helpers\PhoneHelper::normalize($request->voice_phone_2);

        if (!empty($data)) $notif->update($data);

        return response()->json(['success' => true, 'message' => '通知設定を保存しました']);
    }

    public function toggleDeviceWatch(Request $request, string $deviceId)
    {
        $device = Device::where('device_id', $deviceId)->firstOrFail();
        $request->validate(['away_mode' => 'required|boolean']);
        $device->update(['away_mode' => $request->away_mode]);

        return response()->json([
            'success'   => true,
            'away_mode' => (bool) $device->away_mode,
            'message'   => $device->away_mode ? '外出モードをONにしました' : '外出モードをOFFにしました',
        ]);
    }

    public function clearDeviceAlert(Request $request, string $deviceId)
    {
        $device = Device::where('device_id', $deviceId)->firstOrFail();

        $device->update([
            'status'                 => 'inactive',
            'last_human_detected_at' => null,
            'last_received_at'       => null,
            'battery_voltage'        => null,
            'battery_pct'            => null,
            'rssi'                   => null,
        ]);

        $device->detectionLogs()->delete();

        return response()->json(['success' => true, 'message' => "デバイス {$deviceId} の警告を解除しました"]);
    }

    public function destroyDevice(string $deviceId)
    {
        $device = Device::where('device_id', $deviceId)->firstOrFail();
        $device->delete();

        return response()->json(['success' => true, 'message' => "デバイス {$deviceId} を削除しました"]);
    }

    public function storeDeviceSchedule(Request $request, string $deviceId)
    {
        $device    = Device::where('device_id', $deviceId)->firstOrFail();
        $validated = $request->validate([
            'type'           => 'required|in:oneshot,recurring',
            'start_at'       => 'required_if:type,oneshot|nullable|date',
            'end_at'         => 'nullable|date|after:start_at',
            'days_of_week'   => 'required_if:type,recurring|nullable|array',
            'days_of_week.*' => 'integer|between:0,6',
            'start_time'     => 'required_if:type,recurring|nullable|date_format:H:i',
            'end_time'       => 'required_if:type,recurring|nullable|date_format:H:i',
            'next_day'       => 'nullable|boolean',
            'memo'           => 'nullable|string|max:200',
        ]);

        $schedule = $device->schedules()->create([
            'type'         => $validated['type'],
            'start_at'     => $validated['start_at']     ?? null,
            'end_at'       => $validated['end_at']       ?? null,
            'days_of_week' => $validated['days_of_week'] ?? null,
            'start_time'   => $validated['start_time']   ?? null,
            'end_time'     => $validated['end_time']     ?? null,
            'next_day'     => $validated['next_day']     ?? false,
            'memo'         => $validated['memo']         ?? null,
        ]);

        return response()->json(['success' => true, 'schedule' => $schedule], 201);
    }

    public function destroyDeviceSchedule(Request $request, string $deviceId, int $scheduleId)
    {
        $device   = Device::where('device_id', $deviceId)->firstOrFail();
        $schedule = $device->schedules()->findOrFail($scheduleId);
        $schedule->delete();

        return response()->json(['success' => true, 'message' => 'スケジュールを削除しました']);
    }

    // ============================================================
    // 管理者アカウント管理（masterのみ）
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
            'name'            => $request->name,
            'email'           => $request->email,
            'password_hash'   => Hash::make($request->password),
            'role'            => $request->role,
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

        $admin->name            = $request->name;
        $admin->email           = $request->email;
        $admin->role            = $request->role;
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

    public function storeOrg(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:200',
            'contact_name'     => 'nullable|string|max:100',
            'contact_email'    => 'nullable|email|max:255',
            'contact_phone'    => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:500',
            'notes'            => 'nullable|string|max:1000',
            // パートナーアカウント（任意）
            'partner_email'    => 'nullable|email|max:255|unique:admin_users,email',
            'partner_password' => 'nullable|string|min:8|max:100',
        ], [
            'name.required'        => '組織名を入力してください',
            'partner_email.unique'   => 'このメールアドレスは既に使用されています',
            'partner_password.min'   => 'パスワードは8文字以上にしてください',
        ]);

        // パートナー情報がどれか入力されていたら2項目すべて必須
        if ($request->filled('partner_email') || $request->filled('partner_password')) {
            $request->validate([
                'partner_email'    => 'required|email|max:255|unique:admin_users,email',
                'partner_password' => 'required|string|min:8|max:100',
            ], [
                'partner_email.required'    => 'パートナーメールを入力してください',
                'partner_password.required' => 'パスワードを入力してください',
            ]);
        }

        $org = Organization::create([
            'name'            => $request->name,
            'contact_name'    => $request->contact_name,
            'contact_email'   => $request->contact_email,
            'contact_phone'   => $request->contact_phone,
            'address'         => $request->address,
            'notes'           => $request->notes,
            'premium_enabled' => false,
        ]);

        // パートナーアカウント作成（任意）。名前は担当者名、なければ組織名を使用
        if ($request->filled('partner_email')) {
            PartnerUser::create([
                'name'            => $request->contact_name ?: $request->name,
                'email'           => $request->partner_email,
                'password_hash'   => Hash::make($request->partner_password),
                'role'            => 'operator',
                'organization_id' => $org->id,
            ]);
        }

        return redirect('/partner?tab=orgs')->with('success', '組織「' . $org->name . '」を作成しました');
    }

    public function updateOrg(Request $request, int $id)
    {
        $org = Organization::findOrFail($id);

        $request->validate([
            'name'                 => 'required|string|max:200',
            'contact_name'         => 'nullable|string|max:100',
            'contact_email'        => 'required|email|max:255',
            'contact_phone'        => 'nullable|string|max:20',
            'address'              => 'nullable|string|max:500',
            'notes'                => 'nullable|string|max:1000',
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
            'name'                 => $request->name,
            'contact_name'         => $request->contact_name,
            'contact_email'        => $request->contact_email,
            'contact_phone'        => $request->contact_phone,
            'address'              => $request->address,
            'notes'                => $request->notes,
            'notification_email_1' => $request->notification_email_1 ?: null,
            'notification_email_2' => $request->notification_email_2 ?: null,
            'notification_email_3' => $request->notification_email_3 ?: null,
            'notification_sms_1'   => $request->notification_sms_1 ?: null,
            'notification_sms_2'   => $request->notification_sms_2 ?: null,
        ]);

        return redirect('/partner?tab=orgs')->with('success', '組織「' . $org->name . '」を更新しました');
    }

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

    public function toggleOrgPremium(Request $request, int $orgId)
    {
        $org = Organization::findOrFail($orgId);

        $request->validate(['premium_enabled' => 'required|boolean']);

        $enabled = (bool) $request->premium_enabled;

        $org->update(['premium_enabled' => $enabled]);

        Device::where('organization_id', $orgId)->update(['premium_enabled' => $enabled ? 1 : 0]);

        return response()->json([
            'success'         => true,
            'premium_enabled' => $org->premium_enabled,
            'message'         => $enabled ? '組織内全デバイスのプレミアムを有効にしました' : '組織内全デバイスのプレミアムを無効にしました',
        ]);
    }

    // ============================================================
    // パートナーアカウント管理（組織管理タブから、Ajax）
    // ============================================================

    public function orgUsers(int $orgId)
    {
        $org = Organization::findOrFail($orgId);
        $users = PartnerUser::where('organization_id', $orgId)
            ->where('role', 'operator')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'email', 'last_login_at', 'created_at']);

        return response()->json([
            'org_name' => $org->name,
            'users'    => $users,
        ]);
    }

    public function storeOrgUser(Request $request, int $orgId)
    {
        Organization::findOrFail($orgId);

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:255|unique:admin_users,email',
            'password' => 'required|string|min:8|max:100',
        ], [
            'name.required'     => '名前を入力してください',
            'email.required'    => 'メールアドレスを入力してください',
            'email.unique'      => 'このメールアドレスは既に使用されています',
            'password.required' => 'パスワードを入力してください',
            'password.min'      => 'パスワードは8文字以上にしてください',
        ]);

        $user = PartnerUser::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password_hash'   => Hash::make($request->password),
            'role'            => 'operator',
            'organization_id' => $orgId,
        ]);

        return response()->json(['success' => true, 'message' => 'アカウント「' . $user->name . '」を作成しました', 'user' => $user->only(['id', 'name', 'email', 'created_at'])]);
    }

    public function updateOrgUser(Request $request, int $orgId, int $userId)
    {
        $user = PartnerUser::where('id', $userId)->where('organization_id', $orgId)->where('role', 'operator')->firstOrFail();

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => ['required', 'email', 'max:255', Rule::unique('admin_users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|max:100',
        ], [
            'name.required'  => '名前を入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.unique'   => 'このメールアドレスは既に使用されています',
            'password.min'   => 'パスワードは8文字以上にしてください',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password_hash = Hash::make($request->password);
        }
        $user->save();

        return response()->json(['success' => true, 'message' => 'アカウント「' . $user->name . '」を更新しました']);
    }

    public function destroyOrgUser(int $orgId, int $userId)
    {
        $user = PartnerUser::where('id', $userId)->where('organization_id', $orgId)->where('role', 'operator')->firstOrFail();
        $name = $user->name;
        $user->delete();

        return response()->json(['success' => true, 'message' => 'アカウント「' . $name . '」を削除しました']);
    }

    // ============================================================
    // パートナーアカウント パスワードリセット（masterのみ）
    // ============================================================

    public function resetOrgUserPassword(Request $request, int $orgId, int $userId)
    {
        $user = PartnerUser::where('id', $userId)->where('organization_id', $orgId)->where('role', 'operator')->firstOrFail();

        $request->validate([
            'password' => 'required|string|min:8|max:100',
        ], [
            'password.required' => 'パスワードを入力してください',
            'password.min'      => 'パスワードは8文字以上にしてください',
        ]);

        $user->password_hash = Hash::make($request->password);
        $user->save();

        return response()->json(['success' => true, 'message' => '「' . $user->name . '」のパスワードをリセットしました']);
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

    public function toggleNotifyService(Request $request, string $deviceId)
    {
        $device = Device::where('device_id', $deviceId)->firstOrFail();
        $request->validate(['enabled' => 'required|boolean']);
        $device->update(['notification_service_enabled' => (bool) $request->enabled]);

        return response()->json([
            'success' => true,
            'message' => $request->enabled ? '通知サービスを有効にしました' : '通知サービスを停止しました',
        ]);
    }

    public function toggleDevicePremium(Request $request, string $deviceId)
    {
        $device = Device::where('device_id', $deviceId)->firstOrFail();
        $request->validate(['premium_enabled' => 'required|boolean']);
        $device->update(['premium_enabled' => (bool) $request->premium_enabled]);

        return response()->json([
            'success'         => true,
            'premium_enabled' => (bool) $device->premium_enabled,
            'message'         => $request->premium_enabled ? 'プレミアムを有効にしました' : 'プレミアムを無効にしました',
        ]);
    }

    private function buildSalesData(): array
    {
        $now            = now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd   = $now->copy()->subMonth()->endOfMonth();

        $totalAll  = BillingLog::where('status', 'success')->sum('amount');
        $countAll  = BillingLog::where('status', 'success')->count();
        $thisMonth = BillingLog::where('status', 'success')->where('billed_at', '>=', $thisMonthStart)->sum('amount');
        $countThis = BillingLog::where('status', 'success')->where('billed_at', '>=', $thisMonthStart)->count();
        $lastMonth = BillingLog::where('status', 'success')->whereBetween('billed_at', [$lastMonthStart, $lastMonthEnd])->sum('amount');

        $monthly = BillingLog::where('status', 'success')
            ->where('billed_at', '>=', $now->copy()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(billed_at, '%Y-%m') as month, SUM(amount) as total, COUNT(*) as count")
            ->groupBy('month')->orderBy('month', 'desc')->get();

        $byOrg = BillingLog::where('billing_logs.status', 'success')
            ->where('billing_logs.billed_at', '>=', $thisMonthStart)
            ->join('billing_contracts', 'billing_logs.billing_contract_id', '=', 'billing_contracts.id')
            ->leftJoin('organizations', 'billing_contracts.organization_id', '=', 'organizations.id')
            ->selectRaw('COALESCE(organizations.name, "個人") as org_name, SUM(billing_logs.amount) as total, COUNT(*) as count')
            ->groupBy('org_name')->orderByDesc('total')->get();

        return [
            'total_all'  => $totalAll,
            'count_all'  => $countAll,
            'this_month' => $thisMonth,
            'count_this' => $countThis,
            'last_month' => $lastMonth,
            'monthly'    => $monthly,
            'by_org'     => $byOrg,
        ];
    }
}
