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

        $logs = $query->paginate(20)->withQueryString();

        return view('logs', compact('device', 'logs'));
    }
}
