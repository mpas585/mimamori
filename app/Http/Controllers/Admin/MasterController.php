<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MasterController extends Controller
{
    /**
     * ダッシュボード（デバイス一覧 + 統計）
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

        return view('admin.master', compact('stats', 'devices'));
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
