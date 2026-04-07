<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DetectionLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeviceReportController extends Controller
{
    /**
     * ESP32からの定時送信データを受信
     * POST /api/device/report
     */
    public function store(Request $request): JsonResponse
    {
        // バリデーション
        $validated = $request->validate([
            'device_id'       => 'nullable|string|max:10',
            'sim_id'          => 'nullable|string|max:5',
            'period_start'    => 'required|date',
            'period_end'      => 'required|date',
            'detection_count' => 'required|integer|min:0',
            'last_distance_cm'=> 'nullable|integer|min:0',
            'battery_v'       => 'nullable|numeric',
            'battery_pct'     => 'nullable|integer|min:0|max:100',
            'rssi'            => 'nullable|integer',
            'error_code'      => 'nullable|string|max:20',
            'iccid'           => 'nullable|string|max:22',
        ]);

        // sim_id または device_id でデバイスを検索
        $device = null;

        if (!empty($validated['sim_id'])) {
            // sim_id 優先で検索
            $device = Device::where('sim_id', strtoupper($validated['sim_id']))->first();
            if (!$device) {
                Log::warning('Unknown device report by sim_id', ['sim_id' => $validated['sim_id']]);
                return response()->json(['error' => 'device_not_found'], 404);
            }
        } elseif (!empty($validated['device_id'])) {
            // 後方互換: device_id で検索
            $device = Device::where('device_id', $validated['device_id'])->first();
            if (!$device) {
                Log::warning('Unknown device report', ['device_id' => $validated['device_id']]);
                return response()->json(['error' => 'device_not_found'], 404);
            }
        } else {
            return response()->json(['error' => 'device_id or sim_id is required'], 422);
        }

        // ICCID検証（送信された場合）
        if (!empty($validated['iccid'] ?? null) && $device->simBinding) {
            if ($device->simBinding->iccid !== $validated['iccid']) {
                Log::alert('ICCID mismatch', [
                    'device_id' => $device->device_id,
                    'expected'  => $device->simBinding->iccid,
                    'received'  => $validated['iccid'],
                ]);
                return response()->json(['error' => 'iccid_mismatch'], 403);
            }
        }

        // ペット除外判定
        $humanCount = 0;
        $petCount = 0;
        $lastDistance = $validated['last_distance_cm'] ?? null;

        if ($validated['detection_count'] > 0 && $lastDistance !== null) {
            $threshold = $device->pet_exclusion_enabled
                ? $device->pet_exclusion_threshold_cm
                : 9999; // 無効時は全て人間扱い

            if ($lastDistance <= $threshold) {
                // 人間判定
                $humanCount = $validated['detection_count'];
            } else {
                // ペット判定
                $petCount = $validated['detection_count'];
            }
        } else {
            // 距離データなし → 全て人間扱い
            $humanCount = $validated['detection_count'];
        }

        // 検知ログ保存
        $log = DetectionLog::create([
            'device_id'        => $device->id,
            'period_start'     => $validated['period_start'],
            'period_end'       => $validated['period_end'],
            'detection_count'  => $validated['detection_count'],
            'human_count'      => $humanCount,
            'pet_count'        => $petCount,
            'last_distance_cm' => $lastDistance,
            'battery_voltage'  => $validated['battery_v'] ?? null,
            'battery_pct'      => $validated['battery_pct'] ?? null,
            'rssi'             => $validated['rssi'] ?? null,
            'error_code'       => $validated['error_code'] ?? null,
            'raw_json'         => $request->all(),
            'received_at'      => now(),
        ]);

        // デバイスステータス更新
        $updateData = [
            'status'           => 'normal',
            'last_received_at' => now(),
            'battery_voltage'  => $validated['battery_v'] ?? null,
            'battery_pct'      => $validated['battery_pct'] ?? null,
            'rssi'             => $validated['rssi'] ?? null,
        ];

        if ($humanCount > 0) {
            $updateData['last_human_detected_at'] = $validated['period_end'];
        }

        // 電池残量低下チェック
        $batteryPct = $validated['battery_pct'] ?? null;
        if ($batteryPct !== null && $batteryPct <= 10) {
            $updateData['status'] = 'warning';
        }

        $device->update($updateData);

        return response()->json([
            'status'      => 'ok',
            'log_id'      => $log->id,
            'human_count' => $humanCount,
            'pet_count'   => $petCount,
        ], 201);
    }
}
