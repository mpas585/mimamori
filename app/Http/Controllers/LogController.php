<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $device = Auth::user();

        $query = $device->detectionLogs()->orderBy('period_start', 'desc');

        // 日付フィルタ
        if ($request->filled('date_from')) {
            $query->where('period_start', '>=', $request->date_from . ' 00:00:00');
        }
        if ($request->filled('date_to')) {
            $query->where('period_start', '<=', $request->date_to . ' 23:59:59');
        }

        // 種別フィルタ
        if ($request->filled('type')) {
            if ($request->type === 'human') {
                $query->where('human_count', '>', 0);
            } elseif ($request->type === 'pet') {
                $query->where('pet_count', '>', 0);
            }
        }

        $logs = $query->paginate(20)->withQueryString();

        // サマリー集計（フィルタ条件を反映）
        $summaryQuery = $device->detectionLogs();
        if ($request->filled('date_from')) {
            $summaryQuery->where('period_start', '>=', $request->date_from . ' 00:00:00');
        }
        if ($request->filled('date_to')) {
            $summaryQuery->where('period_start', '<=', $request->date_to . ' 23:59:59');
        }
        $summary = [
            'total'  => $summaryQuery->sum('detection_count'),
            'human'  => $summaryQuery->sum('human_count'),
            'pet'    => $summaryQuery->sum('pet_count'),
        ];

        return view('logs', compact('device', 'logs', 'summary'));
    }
}
