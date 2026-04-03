<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Helpers\PhoneHelper;
use App\Models\Device;
use App\Models\DeviceSchedule;
use App\Models\Organization;
use App\Models\OrgDeviceAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrgAdminController extends Controller
{
    /**
     * ログイン中管理者の所属組織を取得
     */
    private function getOrganization(): Organization
    {
        $admin = Auth::guard('partner')->user();
        $organization = $admin->organization;

        if (!$organization) {
            abort(403, '組織が割り当てられていません');
        }

        return $organization;
    }

    /**
     * B2B管理画面ダッシュボード
     */
    public function index(Request $request)
    {
        $organization = $this->getOrganization();

        // この組織に所属するデバイスを取得
        $query = Device::where('organization_id', $organization->id)
            ->with(['orgAssignment', 'notificationSetting']);

        // ステータスフィルタ
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'vacant') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('orgAssignment')
                      ->orWhereHas('orgAssignment', function ($q2) {
                          $q2->where(function ($q3) {
                              $q3->whereNull('tenant_name')->orWhere('tenant_name', '');
                          });
                      });
                });
            } else {
                $query->where('status', $status);
            }
        }

        // 見守りフィルタ
        if ($request->filled('watch')) {
            $watch = $request->watch;
            if ($watch === 'on') {
                $query->where('away_mode', false);
            } elseif ($watch === 'off') {
                $query->where('away_mode', true)->whereNull('away_until');
            } elseif ($watch === 'timer') {
                $query->where('away_mode', true)->whereNotNull('away_until');
            }
        }

        // 検索
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('device_id', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%")
                  ->orWhereHas('orgAssignment', function ($q2) use ($search) {
                      $q2->where('room_number', 'like', "%{$search}%")
                         ->orWhere('tenant_name', 'like', "%{$search}%");
                  });
            });
        }

        // ソート
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['status', 'device_id', 'last_human_detected_at', 'battery_pct', 'rssi', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $devices = $query->paginate(20)->appends($request->query());

        // 統計
        $allDevices = Device::where('organization_id', $organization->id);
        $stats = [
            'normal'  => (clone $allDevices)->where('status', 'normal')->count(),
            'warning' => (clone $allDevices)->where('status', 'warning')->count(),
            'alert'   => (clone $allDevices)->where('status', 'alert')->count(),
            'offline' => (clone $allDevices)->where('status', 'offline')->count(),
            'vacant'  => (clone $allDevices)->where(function ($q) {
                            $q->whereDoesntHave('orgAssignment')
                              ->orWhereHas('orgAssignment', function ($q2) {
                                  $q2->where(function ($q3) {
                                      $q3->whereNull('tenant_name')->orWhere('tenant_name', '');
                                  });
                              });
                         })->count(),
        ];

        return view('partner.dashboard', compact('organization', 'stats', 'devices'));
    }

    /**
     * 組織の通知設定を更新
     */
    public function updateNotification(Request $request)
    {
        $organization = $this->getOrganization();

        $request->validate([
            'notification_email_1'     => 'nullable|email|max:255',
            'notification_email_2'     => 'nullable|email|max:255',
            'notification_email_3'     => 'nullable|email|max:255',
            'notification_enabled'     => 'nullable|boolean',
            'notification_sms_1'       => 'nullable|string|max:20',
            'notification_sms_2'       => 'nullable|string|max:20',
            'notification_sms_enabled' => 'nullable|boolean',
        ]);

        $organization->update([
            'notification_email_1'     => $request->notification_email_1 ?: null,
            'notification_email_2'     => $request->notification_email_2 ?: null,
            'notification_email_3'     => $request->notification_email_3 ?: null,
            'notification_enabled'     => $request->has('notification_enabled') ? (bool) $request->notification_enabled : true,
            'notification_sms_1'       => PhoneHelper::normalize($request->notification_sms_1),
            'notification_sms_2'       => PhoneHelper::normalize($request->notification_sms_2),
            'notification_sms_enabled' => $request->has('notification_sms_enabled') ? (bool) $request->notification_sms_enabled : false,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => '通知設定を更新しました',
            ]);
        }

        return back()->with('success', '通知設定を更新しました');
    }

    /**
     * 組織の通知設定を取得（JSON）
     */
    public function getNotification()
    {
        $organization = $this->getOrganization();

        return response()->json([
            'notification_email_1'     => $organization->notification_email_1,
            'notification_email_2'     => $organization->notification_email_2,
            'notification_email_3'     => $organization->notification_email_3,
            'notification_enabled'     => (bool) $organization->notification_enabled,
            'notification_sms_1'       => $organization->notification_sms_1,
            'notification_sms_2'       => $organization->notification_sms_2,
            'notification_sms_enabled' => (bool) $organization->notification_sms_enabled,
        ]);
    }

    /**
     * デバイス追加（品番で組織に紐付け）
     */
    public function addDevice(Request $request)
    {
        $organization = $this->getOrganization();

        $request->validate([
            'device_id' => 'required|string|size:6',
            'room_number' => 'nullable|string|max:50',
            'tenant_name' => 'nullable|string|max:100',
            'memo' => 'nullable|string|max:255',
        ]);

        $deviceCode = strtoupper($request->device_id);

        // デバイス存在チェック
        $device = Device::where('device_id', $deviceCode)->first();
        if (!$device) {
            return back()->with('error', "デバイス {$deviceCode} が見つかりません");
        }

        // 既に別の組織に所属していないかチェック
        if ($device->organization_id && $device->organization_id !== $organization->id) {
            return back()->with('error', "デバイス {$deviceCode} は別の組織に登録されています");
        }

        // 組織に紐付け
        $device->update([
            'organization_id' => $organization->id,
            'location_memo' => $request->memo,
        ]);

        // 割当情報作成・更新
        OrgDeviceAssignment::updateOrCreate(
            [
                'organization_id' => $organization->id,
                'device_id' => $device->id,
            ],
            [
                'room_number' => $request->room_number,
                'tenant_name' => $request->tenant_name,
                'assigned_at' => now(),
                'unassigned_at' => null,
            ]
        );

        return back()->with('success', "デバイス {$deviceCode} を追加しました");
    }

    /**
     * デバイス削除（組織から解除）
     */
    public function removeDevice(Request $request, $deviceId)
    {
        $organization = $this->getOrganization();

        $device = Device::where('device_id', $deviceId)
            ->where('organization_id', $organization->id)
            ->firstOrFail();

        // 割当を解除
        OrgDeviceAssignment::where('organization_id', $organization->id)
            ->where('device_id', $device->id)
            ->update(['unassigned_at' => now()]);

        // 組織紐付けを解除
        $device->update(['organization_id' => null]);

        // 割当レコードを削除
        OrgDeviceAssignment::where('organization_id', $organization->id)
            ->where('device_id', $device->id)
            ->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => "デバイス {$deviceId} を削除しました"]);
        }

        return back()->with('success', "デバイス {$deviceId} を組織から削除しました");
    }

    /**
     * 見守りON/OFF トグル（AJAX）
     */
    public function toggleWatch(Request $request, $deviceId)
    {
        $organization = $this->getOrganization();

        $device = Device::where('device_id', $deviceId)
            ->where('organization_id', $organization->id)
            ->firstOrFail();

        $request->validate([
            'away_mode' => 'required|boolean',
        ]);

        $device->update([
            'away_mode' => $request->away_mode,
            'away_until' => $request->away_mode ? null : null,
        ]);

        return response()->json([
            'success' => true,
            'away_mode' => (bool) $device->away_mode,
            'message' => $device->away_mode ? '見守りをOFFにしました' : '見守りをONにしました',
        ]);
    }

    /**
     * デバイス詳細（JSON）
     */
    public function deviceDetail($deviceId)
    {
        $organization = $this->getOrganization();

        $device = Device::where('device_id', $deviceId)
            ->where('organization_id', $organization->id)
            ->with(['orgAssignment', 'schedules' => function ($q) {
                $q->where('is_active', true)->orderBy('created_at', 'desc');
            }])
            ->firstOrFail();

        $assignment = $device->orgAssignment;
        $dayNames = ['日', '月', '火', '水', '木', '金', '土'];

        $schedules = $device->schedules->map(function ($schedule) use ($dayNames) {
            $data = [
                'id' => $schedule->id,
                'type' => $schedule->type,
                'memo' => $schedule->memo,
            ];
            if ($schedule->type === 'oneshot') {
                $data['start_at'] = $schedule->start_at ? $schedule->start_at->format('Y-m-d H:i') : null;
                $data['end_at'] = $schedule->end_at ? $schedule->end_at->format('Y-m-d H:i') : null;
            } else {
                $days = $schedule->days_of_week ?? [];
                $data['days_label'] = implode('・', array_map(fn($d) => $dayNames[$d] ?? '', $days));
                $data['start_time'] = $schedule->start_time;
                $data['end_time'] = $schedule->end_time;
                $data['next_day'] = $schedule->next_day;
            }
            return $data;
        });

        $notif = $device->notificationSetting;

        return response()->json([
            'device_id' => $device->device_id,
            'status' => $device->status,
            'room_number' => $assignment->room_number ?? null,
            'tenant_name' => $assignment->tenant_name ?? null,
            'last_human_detected' => $device->last_human_detected_at
                ? $device->last_human_detected_at->format('Y/m/d H:i')
                : null,
            'battery_pct' => $device->battery_pct,
            'battery_voltage' => $device->battery_voltage,
            'rssi' => $device->rssi,
            'alert_threshold_hours' => $device->alert_threshold_hours,
            'pet_exclusion_enabled' => $device->pet_exclusion_enabled,
            'pet_exclusion_threshold_cm' => $device->pet_exclusion_threshold_cm,
            'install_height_cm' => $device->install_height_cm,
            'away_mode' => $device->away_mode,
            'away_until' => $device->away_until ? $device->away_until->format('Y/m/d H:i') : null,
            'memo' => $device->location_memo,
            'registered_at' => $device->created_at->format('Y/m/d'),
            'schedules' => $schedules,
            'sms_enabled'   => $notif ? (bool) $notif->sms_enabled : false,
            'sms_phone_1'   => $notif && $notif->sms_phone_1 ? preg_replace('/^\+81/', '0', $notif->sms_phone_1) : null,
            'sms_phone_2'   => $notif && $notif->sms_phone_2 ? preg_replace('/^\+81/', '0', $notif->sms_phone_2) : null,
            'voice_enabled' => $notif ? (bool) $notif->voice_enabled : false,
            'voice_phone_1' => $notif && $notif->voice_phone_1 ? preg_replace('/^\+81/', '0', $notif->voice_phone_1) : null,
            'voice_phone_2' => $notif && $notif->voice_phone_2 ? preg_replace('/^\+81/', '0', $notif->voice_phone_2) : null,
            'premium_enabled' => (bool) ($device->organization?->premium_enabled ?? false),
        ]);
    }

    /**
     * 割当情報更新（部屋番号・入居者名）
     */
    public function updateAssignment(Request $request, $deviceId)
    {
        $organization = $this->getOrganization();

        $device = Device::where('device_id', $deviceId)
            ->where('organization_id', $organization->id)
            ->firstOrFail();

        $request->validate([
            'room_number' => 'nullable|string|max:50',
            'tenant_name' => 'nullable|string|max:100',
            'memo' => 'nullable|string|max:255',
        ]);

        // 割当情報更新
        OrgDeviceAssignment::updateOrCreate(
            [
                'organization_id' => $organization->id,
                'device_id' => $device->id,
            ],
            [
                'room_number' => $request->room_number,
                'tenant_name' => $request->tenant_name,
            ]
        );

        // メモはdevicesテーブル
        $device->update(['location_memo' => $request->memo]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => '更新しました']);
        }

        return back()->with('success', "デバイス {$deviceId} の情報を更新しました");
    }

    /**
     * 警告解除（ステータスをinactiveに戻し、検知データをクリア）
     */
    public function clearAlert(Request $request, $deviceId)
    {
        $organization = $this->getOrganization();

        $device = Device::where('device_id', $deviceId)
            ->where('organization_id', $organization->id)
            ->firstOrFail();

        // ステータスをinactiveに戻す（初期状態「-」表示）
        $device->update([
            'status' => 'inactive',
            'last_human_detected_at' => null,
            'last_received_at' => null,
            'battery_voltage' => null,
            'battery_pct' => null,
            'rssi' => null,
        ]);

        // 検知ログをクリア
        $device->detectionLogs()->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "デバイス {$deviceId} の警告を解除しました",
            ]);
        }

        return back()->with('success', "デバイス {$deviceId} の警告を解除しました");
    }

    /**
     * CSV出力
     */
    public function exportCsv()
    {
        $organization = $this->getOrganization();

        $devices = Device::where('organization_id', $organization->id)
            ->with('orgAssignment')
            ->orderBy('created_at')
            ->get();

        $filename = $organization->name . '_デバイス一覧_' . date('Ymd') . '.csv';

        return new StreamedResponse(function () use ($devices) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fwrite($handle, "\xEF\xBB\xBF");

            // ヘッダー
            fputcsv($handle, [
                'ステータス', '部屋番号', '入居者名', 'デバイスID',
                '見守り', '最終検知', '電池残量(%)', '電波(dBm)', 'メモ',
            ]);

            $statusLabels = [
                'normal' => '正常', 'warning' => '注意', 'alert' => '警告',
                'offline' => '離線', 'inactive' => '未稼働',
            ];

            foreach ($devices as $device) {
                $assignment = $device->orgAssignment;
                fputcsv($handle, [
                    $statusLabels[$device->status] ?? $device->status,
                    $assignment->room_number ?? '',
                    $assignment->tenant_name ?? '',
                    $device->device_id,
                    $device->away_mode ? 'OFF' : 'ON',
                    $device->last_human_detected_at ? $device->last_human_detected_at->format('Y/m/d H:i') : '',
                    $device->battery_pct ?? '',
                    $device->rssi ?? '',
                    $device->location_memo ?? '',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * タイマー一覧（JSON）
     */
    public function timerList(Request $request)
    {
        $organization = $this->getOrganization();

        $devices = Device::where('organization_id', $organization->id)
            ->with(['orgAssignment', 'schedules' => function ($q) {
                $q->where('is_active', true)->orderBy('created_at', 'desc');
            }])
            ->get();

        $result = [];
        $dayNames = ['日', '月', '火', '水', '木', '金', '土'];

        foreach ($devices as $device) {
            $assignment = $device->orgAssignment;
            $roomNumber = $assignment ? $assignment->room_number : null;
            $tenantName = $assignment ? $assignment->tenant_name : null;

            $hasSchedules = $device->schedules->isNotEmpty();
            $isAwayMode = $device->away_mode;

            if (!$hasSchedules && !$isAwayMode) {
                continue;
            }

            $schedules = $device->schedules->map(function ($schedule) use ($dayNames) {
                $data = [
                    'id' => $schedule->id,
                    'type' => $schedule->type,
                    'memo' => $schedule->memo,
                ];

                if ($schedule->type === 'oneshot') {
                    $data['start_at'] = $schedule->start_at ? $schedule->start_at->format('Y-m-d H:i') : null;
                    $data['end_at'] = $schedule->end_at ? $schedule->end_at->format('Y-m-d H:i') : null;
                } else {
                    $days = $schedule->days_of_week ?? [];
                    $data['days_label'] = implode('・', array_map(fn($d) => $dayNames[$d] ?? '', $days));
                    $data['start_time'] = $schedule->start_time;
                    $data['end_time'] = $schedule->end_time;
                    $data['next_day'] = $schedule->next_day;
                }

                return $data;
            });

            $result[] = [
                'device_id' => $device->device_id,
                'room_number' => $roomNumber,
                'tenant_name' => $tenantName,
                'is_vacant' => !$assignment || !$tenantName,
                'away_mode' => (bool) $device->away_mode,
                'away_until' => $device->away_until ? $device->away_until->format('Y-m-d H:i') : null,
                'schedules' => $schedules,
            ];
        }

        return response()->json($result);
    }

    /**
     * デバイスにスケジュール追加
     */
    public function storeSchedule(Request $request, $deviceId)
    {
        $organization = $this->getOrganization();

        $device = Device::where('device_id', $deviceId)
            ->where('organization_id', $organization->id)
            ->firstOrFail();

        $validated = $request->validate([
            'type' => 'required|in:oneshot,recurring',
            'start_at' => 'required_if:type,oneshot|nullable|date',
            'end_at' => 'nullable|date|after:start_at',
            'days_of_week' => 'required_if:type,recurring|nullable|array',
            'days_of_week.*' => 'integer|between:0,6',
            'start_time' => 'required_if:type,recurring|nullable|date_format:H:i',
            'end_time' => 'required_if:type,recurring|nullable|date_format:H:i',
            'next_day' => 'nullable|boolean',
            'memo' => 'nullable|string|max:200',
        ]);

        $schedule = $device->schedules()->create([
            'type' => $validated['type'],
            'start_at' => $validated['start_at'] ?? null,
            'end_at' => $validated['end_at'] ?? null,
            'days_of_week' => $validated['days_of_week'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'next_day' => $validated['next_day'] ?? false,
            'memo' => $validated['memo'] ?? null,
        ]);

        return response()->json(['success' => true, 'schedule' => $schedule], 201);
    }

    /**
     * デバイスのスケジュール削除
     */
    public function destroySchedule(Request $request, $deviceId, $scheduleId)
    {
        $organization = $this->getOrganization();

        $device = Device::where('device_id', $deviceId)
            ->where('organization_id', $organization->id)
            ->firstOrFail();

        $schedule = $device->schedules()->findOrFail($scheduleId);
        $schedule->delete();

        return response()->json(['success' => true, 'message' => 'スケジュールを削除しました']);
    }

    /**
     * デバイス個別の通知先設定を更新（SMS/電話）
     */
    public function updateDeviceNotification(Request $request, $deviceId)
    {
        $organization = $this->getOrganization();

        $device = Device::where('device_id', $deviceId)
            ->where('organization_id', $organization->id)
            ->firstOrFail();

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

        if (!empty($data)) {
            $notif->update($data);
        }

        return response()->json(['success' => true, 'message' => '通知設定を更新しました']);
    }

    /**
     * デバイス一括生成 + 組織紐付け（AJAX）
     */
    public function bulkCheckout(Request $request)
    {
        $organization = $this->getOrganization();

        $request->validate([
            'count'   => 'required|integer|min:1|max:300',
            'opt_ai'  => 'nullable|boolean',
            'opt_sms' => 'nullable|boolean',
        ]);

        $count  = (int) $request->count;
        $optAi  = (bool) $request->opt_ai;
        $optSms = (bool) $request->opt_sms;

        $issued = [];

        for ($i = 0; $i < $count; $i++) {
            $deviceId = $this->generateDeviceId();
            $pin      = $this->generatePin();

            $device = Device::create([
                'device_id'       => $deviceId,
                'pin_hash'        => Hash::make($pin),
                'status'          => 'inactive',
                'organization_id' => $organization->id,
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

        return response()->json([
            'success'      => true,
            'count'        => $count,
            'issued'       => $issued,
            'checkout_url' => null,
        ]);
    }

    /**
     * デバイスIDを生成（英数字6文字、重複なし）
     */
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

    /**
     * PIN生成（数字4桁）
     */
    private function generatePin(): string
    {
        return str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }
}
