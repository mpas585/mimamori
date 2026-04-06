<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Helpers\PhoneHelper;
use App\Models\BillingContract;
use App\Models\BillingLog;
use App\Models\Device;
use App\Models\DeviceSchedule;
use App\Models\Organization;
use App\Models\OrgDeviceAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrgAdminController extends Controller
{
    private function getOrganization(): Organization
    {
        $admin = Auth::guard('partner')->user();
        $organization = $admin->organization;

        if (!$organization) {
            abort(403, '組織が割り当てられていません');
        }

        return $organization;
    }

    public function index(Request $request)
    {
        $organization = $this->getOrganization();

        $query = Device::where('organization_id', $organization->id)
            ->with(['orgAssignment', 'notificationSetting']);

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

        $sortBy  = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['status', 'device_id', 'last_human_detected_at', 'battery_pct', 'rssi', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $devices = $query->paginate(20)->appends($request->query());

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
            return response()->json(['success' => true, 'message' => '通知設定を更新しました']);
        }

        return back()->with('success', '通知設定を更新しました');
    }

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

    public function addDevice(Request $request)
    {
        $organization = $this->getOrganization();

        $request->validate([
            'device_id'   => 'required|string|size:6',
            'room_number' => 'nullable|string|max:50',
            'tenant_name' => 'nullable|string|max:100',
            'memo'        => 'nullable|string|max:255',
        ]);

        $deviceCode = strtoupper($request->device_id);

        $device = Device::where('device_id', $deviceCode)->first();
        if (!$device) {
            return back()->with('error', "デバイス {$deviceCode} が見つかりません");
        }

        if ($device->organization_id && $device->organization_id !== $organization->id) {
            return back()->with('error', "デバイス {$deviceCode} は別の組織に登録されています");
        }

        $device->update([
            'organization_id' => $organization->id,
            'location_memo'   => $request->memo,
        ]);

        OrgDeviceAssignment::updateOrCreate(
            ['organization_id' => $organization->id, 'device_id' => $device->id],
            ['room_number' => $request->room_number, 'tenant_name' => $request->tenant_name, 'assigned_at' => now(), 'unassigned_at' => null]
        );

        return back()->with('success', "デバイス {$deviceCode} を追加しました");
    }

    public function removeDevice(Request $request, $deviceId)
    {
        $organization = $this->getOrganization();

        $device = Device::where('device_id', $deviceId)
            ->where('organization_id', $organization->id)
            ->firstOrFail();

        OrgDeviceAssignment::where('organization_id', $organization->id)
            ->where('device_id', $device->id)
            ->update(['unassigned_at' => now()]);

        $device->update(['organization_id' => null]);

        OrgDeviceAssignment::where('organization_id', $organization->id)
            ->where('device_id', $device->id)
            ->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => "デバイス {$deviceId} を削除しました"]);
        }

        return back()->with('success', "デバイス {$deviceId} を組織から削除しました");
    }

    public function toggleWatch(Request $request, $deviceId)
    {
        $organization = $this->getOrganization();

        $device = Device::where('device_id', $deviceId)
            ->where('organization_id', $organization->id)
            ->firstOrFail();

        $request->validate(['away_mode' => 'required|boolean']);

        $device->update(['away_mode' => $request->away_mode, 'away_until' => null]);

        return response()->json([
            'success'   => true,
            'away_mode' => (bool) $device->away_mode,
            'message'   => $device->away_mode ? '見守りをOFFにしました' : '見守りをONにしました',
        ]);
    }

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
        $dayNames   = ['日', '月', '火', '水', '木', '金', '土'];

        $schedules = $device->schedules->map(function ($schedule) use ($dayNames) {
            $data = ['id' => $schedule->id, 'type' => $schedule->type, 'memo' => $schedule->memo];
            if ($schedule->type === 'oneshot') {
                $data['start_at'] = $schedule->start_at ? $schedule->start_at->format('Y-m-d H:i') : null;
                $data['end_at']   = $schedule->end_at   ? $schedule->end_at->format('Y-m-d H:i')   : null;
            } else {
                $days = $schedule->days_of_week ?? [];
                $data['days_label'] = implode('・', array_map(fn($d) => $dayNames[$d] ?? '', $days));
                $data['start_time'] = $schedule->start_time;
                $data['end_time']   = $schedule->end_time;
                $data['next_day']   = $schedule->next_day;
            }
            return $data;
        });

        $notif = $device->notificationSetting;

        return response()->json([
            'device_id'                  => $device->device_id,
            'status'                     => $device->status,
            'room_number'                => $assignment->room_number ?? null,
            'tenant_name'                => $assignment->tenant_name ?? null,
            'last_human_detected'        => $device->last_human_detected_at ? $device->last_human_detected_at->format('Y/m/d H:i') : null,
            'battery_pct'                => $device->battery_pct,
            'battery_voltage'            => $device->battery_voltage,
            'rssi'                       => $device->rssi,
            'alert_threshold_hours'      => $device->alert_threshold_hours,
            'pet_exclusion_enabled'      => $device->pet_exclusion_enabled,
            'pet_exclusion_threshold_cm' => $device->pet_exclusion_threshold_cm,
            'install_height_cm'          => $device->install_height_cm,
            'away_mode'                  => $device->away_mode,
            'away_until'                 => $device->away_until ? $device->away_until->format('Y/m/d H:i') : null,
            'memo'                       => $device->location_memo,
            'registered_at'              => $device->created_at->format('Y/m/d'),
            'schedules'                  => $schedules,
            'sms_enabled'                => $notif ? (bool) $notif->sms_enabled   : false,
            'sms_phone_1'                => $notif && $notif->sms_phone_1 ? preg_replace('/^\+81/', '0', $notif->sms_phone_1) : null,
            'sms_phone_2'                => $notif && $notif->sms_phone_2 ? preg_replace('/^\+81/', '0', $notif->sms_phone_2) : null,
            'voice_enabled'              => $notif ? (bool) $notif->voice_enabled : false,
            'voice_phone_1'              => $notif && $notif->voice_phone_1 ? preg_replace('/^\+81/', '0', $notif->voice_phone_1) : null,
            'voice_phone_2'              => $notif && $notif->voice_phone_2 ? preg_replace('/^\+81/', '0', $notif->voice_phone_2) : null,
            'premium_enabled'            => (bool) ($device->premium_enabled ?? false),
        ]);
    }

    public function toggleDevicePremium(Request $request, $deviceId)
    {
        $organization = $this->getOrganization();

        $device = Device::where('device_id', $deviceId)
            ->where('organization_id', $organization->id)
            ->firstOrFail();

        $request->validate(['premium_enabled' => 'required|boolean']);

        $device->update(['premium_enabled' => (bool) $request->premium_enabled]);

        return response()->json([
            'success'         => true,
            'premium_enabled' => (bool) $device->premium_enabled,
            'message'         => $device->premium_enabled ? 'プレミアムを有効にしました' : 'プレミアムを無効にしました',
        ]);
    }

    public function updateAssignment(Request $request, $deviceId)
    {
        $organization = $this->getOrganization();

        $device = Device::where('device_id', $deviceId)
            ->where('organization_id', $organization->id)
            ->firstOrFail();

        $request->validate([
            'room_number' => 'nullable|string|max:50',
            'tenant_name' => 'nullable|string|max:100',
            'memo'        => 'nullable|string|max:255',
        ]);

        OrgDeviceAssignment::updateOrCreate(
            ['organization_id' => $organization->id, 'device_id' => $device->id],
            ['room_number' => $request->room_number, 'tenant_name' => $request->tenant_name]
        );

        $device->update(['location_memo' => $request->memo]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => '更新しました']);
        }

        return back()->with('success', "デバイス {$deviceId} の情報を更新しました");
    }

    public function clearAlert(Request $request, $deviceId)
    {
        $organization = $this->getOrganization();

        $device = Device::where('device_id', $deviceId)
            ->where('organization_id', $organization->id)
            ->firstOrFail();

        $device->update([
            'status'                 => 'inactive',
            'last_human_detected_at' => null,
            'last_received_at'       => null,
            'battery_voltage'        => null,
            'battery_pct'            => null,
            'rssi'                   => null,
        ]);

        $device->detectionLogs()->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => "デバイス {$deviceId} の警告を解除しました"]);
        }

        return back()->with('success', "デバイス {$deviceId} の警告を解除しました");
    }

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
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['ステータス', '部屋番号', '入居者名', 'デバイスID', '見守り', '最終検知', '電池残量(%)', '電波(dBm)', 'メモ', 'プレミアム']);

            $statusLabels = ['normal' => '正常', 'warning' => '注意', 'alert' => '警告', 'offline' => '離線', 'inactive' => '未稼働'];

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
                    $device->premium_enabled ? '有効' : '無効',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function timerList(Request $request)
    {
        $organization = $this->getOrganization();

        $devices = Device::where('organization_id', $organization->id)
            ->with(['orgAssignment', 'schedules' => function ($q) {
                $q->where('is_active', true)->orderBy('created_at', 'desc');
            }])
            ->get();

        $result   = [];
        $dayNames = ['日', '月', '火', '水', '木', '金', '土'];

        foreach ($devices as $device) {
            $assignment  = $device->orgAssignment;
            $hasSchedules = $device->schedules->isNotEmpty();
            $isAwayMode  = $device->away_mode;

            if (!$hasSchedules && !$isAwayMode) continue;

            $schedules = $device->schedules->map(function ($schedule) use ($dayNames) {
                $data = ['id' => $schedule->id, 'type' => $schedule->type, 'memo' => $schedule->memo];
                if ($schedule->type === 'oneshot') {
                    $data['start_at'] = $schedule->start_at ? $schedule->start_at->format('Y-m-d H:i') : null;
                    $data['end_at']   = $schedule->end_at   ? $schedule->end_at->format('Y-m-d H:i')   : null;
                } else {
                    $days = $schedule->days_of_week ?? [];
                    $data['days_label'] = implode('・', array_map(fn($d) => $dayNames[$d] ?? '', $days));
                    $data['start_time'] = $schedule->start_time;
                    $data['end_time']   = $schedule->end_time;
                    $data['next_day']   = $schedule->next_day;
                }
                return $data;
            });

            $result[] = [
                'device_id'   => $device->device_id,
                'room_number' => $assignment ? $assignment->room_number : null,
                'tenant_name' => $assignment ? $assignment->tenant_name : null,
                'is_vacant'   => !$assignment || !($assignment->tenant_name),
                'away_mode'   => (bool) $device->away_mode,
                'away_until'  => $device->away_until ? $device->away_until->format('Y-m-d H:i') : null,
                'schedules'   => $schedules,
            ];
        }

        return response()->json($result);
    }

    public function storeSchedule(Request $request, $deviceId)
    {
        $organization = $this->getOrganization();

        $device = Device::where('device_id', $deviceId)
            ->where('organization_id', $organization->id)
            ->firstOrFail();

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
        if ($request->has('sms_phone_1'))   $data['sms_phone_1']   = PhoneHelper::normalize($request->sms_phone_1);
        if ($request->has('sms_phone_2'))   $data['sms_phone_2']   = PhoneHelper::normalize($request->sms_phone_2);
        if ($request->has('voice_enabled')) $data['voice_enabled'] = (bool) $request->voice_enabled;
        if ($request->has('voice_phone_1')) $data['voice_phone_1'] = PhoneHelper::normalize($request->voice_phone_1);
        if ($request->has('voice_phone_2')) $data['voice_phone_2'] = PhoneHelper::normalize($request->voice_phone_2);

        if (!empty($data)) $notif->update($data);

        return response()->json(['success' => true, 'message' => '通知設定を更新しました']);
    }

    // ============================================================
    // 登録カード情報取得（ステップ4の確認画面で表示）
    // ============================================================

    public function getCardInfo()
    {
        $organization = $this->getOrganization();

        $contract = BillingContract::where('organization_id', $organization->id)
            ->where('status', 'active')
            ->first();

        if (!$contract || !$contract->payjp_customer_id) {
            return response()->json(['found' => false]);
        }

        try {
            \Payjp\Payjp::setApiKey(config('services.payjp.secret_key'));
            $customer = \Payjp\Customer::retrieve($contract->payjp_customer_id);
            $card     = $customer->cards->data[0] ?? null;

            if (!$card) {
                return response()->json(['found' => false]);
            }

            return response()->json([
                'found' => true,
                'brand' => $card->brand,
                'last4' => $card->last4,
            ]);

        } catch (\Exception $e) {
            Log::error('getCardInfo error: ' . $e->getMessage());
            return response()->json(['found' => false]);
        }
    }

    // ============================================================
    // デバイス新規お申込み（4ステップ → 決済）
    // ============================================================

    public function bulkCheckout(Request $request)
    {
        $organization = $this->getOrganization();

        $request->validate([
            'count'    => 'required|integer|min:1|max:300',
            'opt_ai'   => 'nullable|boolean',
            'opt_sms'  => 'nullable|boolean',
        ]);

        $count  = (int) $request->count;
        $optAi  = (bool) ($request->opt_ai  ?? false);
        $optSms = (bool) ($request->opt_sms ?? false);

        // ── 料金計算（税抜単価）
        $unitPrice = 700 + ($optAi ? 300 : 0) + ($optSms ? 100 : 0);
        $subtotal  = $unitPrice * $count;
        $tax       = (int) floor($subtotal * 0.1);
        $total     = $subtotal + $tax;

        // ── BillingContract（カード情報）取得
        $contract = BillingContract::where('organization_id', $organization->id)
            ->where('status', 'active')
            ->first();

        if (!$contract || !$contract->payjp_customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'クレジットカードが登録されていません。管理者にお問い合わせください。',
            ], 422);
        }

        // ── Pay.jp 課金
        \Payjp\Payjp::setApiKey(config('services.payjp.secret_key'));

        try {
            $charge = \Payjp\Charge::create([
                'amount'      => $total,
                'currency'    => 'jpy',
                'customer'    => $contract->payjp_customer_id,
                'description' => "みまもりデバイス 追加料金 - {$organization->name} {$count}台"
                               . ($optAi ? ' / AIコール' : '')
                               . ($optSms ? ' / SMS' : ''),
            ]);
        } catch (\Exception $e) {
            Log::error('bulkCheckout charge error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '決済に失敗しました: ' . $e->getMessage(),
            ], 500);
        }

        // ── 課金ログ
        BillingLog::create([
            'billing_contract_id'  => $contract->id,
            'amount'               => $total,
            'device_count'         => $count,
            'premium_device_count' => 0,
            'payjp_charge_id'      => $charge->id,
            'status'               => 'success',
            'billed_at'            => now(),
        ]);

        // ── BillingContract の台数・金額を更新（翌月分の自動課金に反映）
        $contract->update([
            'device_count' => $contract->device_count + $count,
            'amount'       => $contract->calcAmount() + ($unitPrice * $count),
        ]);

        // ── デバイス発番
        $issued = [];

        for ($i = 0; $i < $count; $i++) {
            $deviceId = $this->generateDeviceId();
            $pin      = $this->generatePin();

            $device = Device::create([
                'device_id'       => $deviceId,
                'pin_hash'        => Hash::make($pin),
                'status'          => 'inactive',
                'organization_id' => $organization->id,
                'premium_enabled' => $optAi || $optSms ? 1 : 0,
            ]);

            DB::table('notification_settings')->insert([
                'device_id'     => $device->id,
                'email_enabled' => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $issued[] = ['device_id' => $deviceId, 'pin' => $pin];
        }

        return response()->json([
            'success'    => true,
            'count'      => $count,
            'issued'     => $issued,
            'amount'     => $total,
            'charge_id'  => $charge->id,
        ]);
    }

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
