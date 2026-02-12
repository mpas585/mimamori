<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// スケジュールに基づくaway_mode自動ON/OFF（毎分実行）
Schedule::command('schedules:process')->everyMinute();
