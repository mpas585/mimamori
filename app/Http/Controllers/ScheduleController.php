<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DeviceSchedule;

class ScheduleController extends Controller
{
    /**
     * スケジュール一覧取得（JSON）
     */
    public function index(Request $request)
    {
        $device = Auth::user();
        $type = $request->query('type'); // 'oneshot' or 'recurring'

        $query = $device->schedules()->where('is_active', true);

        if ($type) {
            $query->where('type', $type);
        }

        $schedules = $query->orderBy('created_at', 'desc')->get();

        return response()->json($schedules);
    }

    /**
     * スケジュール追加
     */
    public function store(Request $request)
    {
        $device = Auth::user();

        $validated = $request->validate([
            'type' => 'required|in:oneshot,recurring',
            // 単発
            'start_at' => 'required_if:type,oneshot|nullable|date',
            'end_at' => 'nullable|date|after:start_at',
            // 定期
            'days_of_week' => 'required_if:type,recurring|nullable|array',
            'days_of_week.*' => 'integer|between:0,6',
            'start_time' => 'required_if:type,recurring|nullable|date_format:H:i',
            'end_time' => 'required_if:type,recurring|nullable|date_format:H:i',
            'next_day' => 'nullable|boolean',
            // 共通
            'memo' => 'nullable|string|max:200',
        ]);

        $schedule = $device->schedules()->create([
            'type' => $validated['type'],
            'start_at' => $validated['start_at'] ?? null,
            'end_at' => $validated['end_at'] ?? null,
            'days_of_week' => $validated['days_of_week'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'next_day' => $validated['next_day'] ?? false,
            'memo' => $validated['memo'] ?? null,
        ]);

        return response()->json($schedule, 201);
    }

    /**
     * スケジュール削除
     */
    public function destroy($id)
    {
        $device = Auth::user();
        $schedule = $device->schedules()->findOrFail($id);
        $schedule->delete();

        return response()->json(['ok' => true]);
    }
}
