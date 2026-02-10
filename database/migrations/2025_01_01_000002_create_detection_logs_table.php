<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detection_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->dateTime('period_start')->comment('集計期間開始');
            $table->dateTime('period_end')->comment('集計期間終了');
            $table->unsignedSmallInteger('detection_count')->default(0)->comment('検知回数');
            $table->unsignedSmallInteger('human_count')->default(0)->comment('人間判定回数');
            $table->unsignedSmallInteger('pet_count')->default(0)->comment('ペット判定回数');
            $table->unsignedSmallInteger('last_distance_cm')->nullable()->comment('最終検知距離(cm)');
            $table->decimal('battery_voltage', 3, 2)->nullable();
            $table->unsignedTinyInteger('battery_pct')->nullable();
            $table->smallInteger('rssi')->nullable();
            $table->string('error_code', 20)->nullable()->comment('エラーコード');
            $table->json('raw_json')->nullable()->comment('生データ保存');
            $table->dateTime('received_at')->useCurrent()->comment('サーバー受信日時');

            $table->index(['device_id', 'period_start']);
            $table->index('received_at');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detection_logs');
    }
};
