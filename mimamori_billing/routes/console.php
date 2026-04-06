<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// スケジュールに基づくaway_mode自動ON/OFF（毎分実行）
Schedule::command('schedules:process')->everyMinute();

// 未検知チェック（30分ごと実行）
Schedule::command('devices:check-undetected')->everyThirtyMinutes();

// 月次課金（毎月1日 午前9時）
Schedule::command('billing:run-monthly')->monthlyOn(1, '09:00');
