<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MypageController extends Controller
{
    public function index()
    {
        $device = Auth::user();
        $logs = $device->detectionLogs()
            ->orderBy('period_start', 'desc')
            ->limit(10)
            ->get();

        return view('mypage', compact('device', 'logs'));
    }
}
