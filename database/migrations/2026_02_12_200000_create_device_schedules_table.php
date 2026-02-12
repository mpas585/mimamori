<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->enum('type', ['oneshot', 'recurring'])->comment('単発 or 定期');

            // 単発用
            $table->dateTime('start_at')->nullable()->comment('停止開始日時');
            $table->dateTime('end_at')->nullable()->comment('自動再開日時（NULL=手動復帰）');

            // 定期用
            $table->json('days_of_week')->nullable()->comment('曜日 [0=日,1=月,...,6=土]');
            $table->string('start_time', 5)->nullable()->comment('開始時刻 HH:MM');
            $table->string('end_time', 5)->nullable()->comment('終了時刻 HH:MM');
            $table->boolean('next_day')->default(false)->comment('翌日までまたぐ');

            // 共通
            $table->string('memo', 200)->nullable()->comment('メモ');
            $table->boolean('is_active')->default(true)->comment('有効/無効');
            $table->timestamps();

            $table->index(['device_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_schedules');
    }
};
