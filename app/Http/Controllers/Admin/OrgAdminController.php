<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Organization;
use App\Models\OrgDeviceAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrgAdminController extends Controller
{
    /**
     * ログイン中管理者の所属組織を取得
     */
    private function getOrganization(): Organization
    {
        $admin = Auth::guard('admin')->user();
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

        return view('admin.dashboard', compact('organization', 'stats', 'devices'));
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
            ->with('orgAssignment')
            ->firstOrFail();

        $assignment = $device->orgAssignment;

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
}
