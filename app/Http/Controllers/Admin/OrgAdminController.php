<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Organization;
use App\Models\OrgDeviceAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrgAdminController extends Controller
{
    /**
     * B2B管理画面ダッシュボード
     */
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $organization = $admin->organization;

        if (!$organization) {
            abort(403, '組織が割り当てられていません');
        }

        // この組織に所属するデバイスを取得
        $query = Device::where('organization_id', $organization->id)
            ->with(['orgAssignment', 'notificationSetting']);

        // ステータスフィルタ
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'vacant') {
                $query->whereDoesntHave('orgAssignment', function ($q) {
                    $q->whereNotNull('tenant_name')->where('tenant_name', '!=', '');
                });
            } else {
                $query->where('status', $status);
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

        $devices = $query->orderBy('created_at', 'desc')->paginate(20);

        // 統計
        $allDevices = Device::where('organization_id', $organization->id);
        $stats = [
            'normal'  => (clone $allDevices)->where('status', 'normal')->count(),
            'warning' => (clone $allDevices)->where('status', 'warning')->count(),
            'alert'   => (clone $allDevices)->where('status', 'alert')->count(),
            'offline' => (clone $allDevices)->where('status', 'offline')->count(),
            'vacant'  => OrgDeviceAssignment::where('organization_id', $organization->id)
                            ->where(function ($q) {
                                $q->whereNull('tenant_name')->orWhere('tenant_name', '');
                            })->count(),
        ];

        return view('admin.dashboard', compact('organization', 'stats', 'devices'));
    }
}
