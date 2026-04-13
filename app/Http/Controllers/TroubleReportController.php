<?php

namespace App\Http\Controllers;

use App\Models\TroubleReport;
use Illuminate\Http\Request;

class TroubleReportController extends Controller
{
    public function index()
    {
        $device = auth()->user();

        $reports = TroubleReport::where('device_id', $device->id)
            ->orderByDesc('created_at')
            ->get();

        $symptoms     = TroubleReport::symptomOptions();
        $statusLabels = TroubleReport::statusLabels();
        $typeLabels   = TroubleReport::typeLabels();

        return view('trouble.index', compact('device', 'reports', 'symptoms', 'statusLabels', 'typeLabels'));
    }

    public function store(Request $request)
    {
        $device = auth()->user();

        $request->validate([
            'type'        => 'required|in:malfunction,abuse_report',
            'symptom'     => 'nullable|string|max:100',
            'description' => 'nullable|string|max:2000',
        ], [
            'type.required' => '申請種別を選択してください',
            'type.in'       => '無効な申請種別です',
        ]);

        TroubleReport::create([
            'device_id'   => $device->id,
            'type'        => $request->type,
            'symptom'     => $request->symptom,
            'description' => $request->description,
            'status'      => 'open',
        ]);

        return redirect('/trouble')->with('success', '申請を受け付けました。確認後、ご連絡いたします。');
    }
}
