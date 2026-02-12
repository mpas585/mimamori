<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Device;
use Carbon\Carbon;

class ProcessSchedules extends Command
{
    protected $signature = 'schedules:process';
    protected $description = 'スケジュールに基づいてaway_modeを自動ON/OFF';

    public function handle(): void
    {
        $now = Carbon::now('Asia/Tokyo');
        $dayOfWeek = (int) $now->format('w'); // 0=日, 1=月, ..., 6=土
        $currentTime = $now->format('H:i');

        // アクティブなスケジュールを持つデバイスを取得
        $devices = Device::whereHas('schedules', fn($q) => $q->where('is_active', true))->get();

        foreach ($devices as $device) {
            $shouldBeAway = false;
            $schedules = $device->schedules()->where('is_active', true)->get();

            foreach ($schedules as $schedule) {
                if ($schedule->type === 'oneshot') {
                    $shouldBeAway = $this->checkOneshot($schedule, $now) || $shouldBeAway;
                } else {
                    $shouldBeAway = $this->checkRecurring($schedule, $dayOfWeek, $currentTime) || $shouldBeAway;
                }
            }

            // away_modeを更新（変更があった場合のみ）
            if ($shouldBeAway !== (bool) $device->away_mode) {
                $device->update(['away_mode' => $shouldBeAway]);
                $this->info("Device {$device->device_id}: away_mode → " . ($shouldBeAway ? 'ON' : 'OFF'));
            }
        }
    }

    /**
     * 単発スケジュールのチェック
     */
    private function checkOneshot($schedule, Carbon $now): bool
    {
        $start = Carbon::parse($schedule->start_at, 'Asia/Tokyo');

        // まだ開始前
        if ($now->lt($start)) {
            return false;
        }

        // 終了日時なし（手動復帰）→ 開始後はずっとON
        if ($schedule->end_at === null) {
            return true;
        }

        $end = Carbon::parse($schedule->end_at, 'Asia/Tokyo');

        // 期間内 → ON
        if ($now->lt($end)) {
            return true;
        }

        // 終了日時を過ぎた → スケジュール無効化
        $schedule->update(['is_active' => false]);
        return false;
    }

    /**
     * 定期スケジュールのチェック
     */
    private function checkRecurring($schedule, int $dayOfWeek, string $currentTime): bool
    {
        $days = $schedule->days_of_week ?? [];

        if ($schedule->next_day) {
            // 翌日またぎ: 22:00〜翌06:00 のようなパターン
            // ケース1: 今日が対象曜日で、start_time 以降 → ON
            if (in_array($dayOfWeek, $days) && $currentTime >= $schedule->start_time) {
                return true;
            }
            // ケース2: 昨日が対象曜日で、end_time 前 → ON
            $yesterday = ($dayOfWeek + 6) % 7;
            if (in_array($yesterday, $days) && $currentTime < $schedule->end_time) {
                return true;
            }
        } else {
            // 同日: 10:00〜16:00 のようなパターン
            if (in_array($dayOfWeek, $days)
                && $currentTime >= $schedule->start_time
                && $currentTime < $schedule->end_time) {
                return true;
            }
        }

        return false;
    }
}
