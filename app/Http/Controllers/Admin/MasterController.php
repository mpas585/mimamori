<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Organization;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MasterController extends Controller
{
    /**
     * ダッシュボード（アカウント・課金管理）
     */
    public function index(Request $request)
    {
        // 統計
        $stats = [
            'total'             => Device::count(),
            'active'            => Device::where('status', '!=', 'inactive')->count(),
            'normal'            => Device::where('status', 'normal')->count(),
            'alert'             => Device::where('status', 'alert')->count(),
            'offline'           => Device::where('status', 'offline')->count(),
            'inactive'          => Device::where('status', 'inactive')->count(),
            'premium'           => Subscription::where('plan', 'premium')
                                      ->where('status', 'active')->count(),
            'monthly_revenue'   => $this->calcMonthlyRevenue(),
            'pending_transfers' => Subscription::where('plan', 'premium')
                                      ->where('status', 'active')
                                      ->whereNull('stripe_subscription_id')
                                      ->whereNull('current_period_end')
                                      ->count(),
            'expiring_soon'     => Subscription::where('plan', 'premium')
                                      ->where('status', 'active')
                                      ->whereNotNull('current_period_end')
                                      ->where('current_period_end', '<=', now()->addDays(30))
                                      ->count(),
            'new_this_month'    => Device::whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)
                                      ->count(),
        ];

        // デバイス一覧（検索・フィルタ対応）
        $query = Device::with(['subscription', 'notificationSetting']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('device_id', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%")
                  ->orWhereHas('notificationSetting', function ($q2) use ($search) {
                      $q2->where('email_1', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan')) {
            if ($request->plan === 'premium') {
                $query->whereHas('subscription', function ($q) {
                    $q->where('plan', 'premium')->where('status', 'active');
                });
            } elseif ($request->plan === 'free') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('subscription')
                      ->orWhereHas('subscription', function ($q2) {
                          $q2->where('plan', 'free');
                      });
                });
            }
        }

        $devices = $query->orderBy('created_at', 'desc')->paginate(20);

        // 法人一覧
        $organizations = Organization::withCount([
            'devices' => function ($q) {
                $q->where('status', '!=', 'inactive');
            },
        ])->get();

        // 期限切れ間近
        $expiringDevices = Device::with(['subscription', 'notificationSetting'])
            ->whereHas('subscription', function ($q) {
                $q->where('plan', 'premium')
                  ->where('status', 'active')
                  ->whereNotNull('current_period_end')
                  ->where('current_period_end', '<=', now()->addDays(30));
            })
            ->get();

        // 振込待ち
        $pendingTransfers = Subscription::with(['device.notificationSetting'])
            ->where('plan', 'premium')
            ->where('status', 'active')
            ->whereNull('stripe_subscription_id')
            ->whereNull('current_period_end')
            ->get();

        return view('admin.master', compact(
            'stats',
            'devices',
            'organizations',
            'expiringDevices',
            'pendingTransfers'
        ));
    }

    /**
     * 今月売上の概算
     */
    private function calcMonthlyRevenue(): int
    {
        $monthly = Subscription::where('plan', 'premium')
            ->where('status', 'active')
            ->where('billing_cycle', 'monthly')
            ->count();

        $yearly = Subscription::where('plan', 'premium')
            ->where('status', 'active')
            ->where('billing_cycle', 'yearly')
            ->count();

        return ($monthly * 500) + (int) round($yearly * 3000 / 12);
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
