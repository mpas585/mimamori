<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id', 10)->unique()->comment('品番（A3K9X2形式）');
            $table->string('pin_hash', 255)->comment('PIN（bcryptハッシュ）');

            // 表示情報
            $table->string('nickname', 100)->nullable()->comment('表示名（任意）');
            $table->string('location_memo', 255)->nullable()->comment('設置場所メモ');

            // センサー状態（最新）
            $table->enum('status', ['normal', 'warning', 'alert', 'offline', 'inactive'])->default('inactive')->comment('normal=正常, warning=注意, alert=未検知アラート, offline=通信途絶, inactive=未稼働');
            $table->decimal('battery_voltage', 3, 2)->nullable()->comment('電池電圧(V)');
            $table->unsignedTinyInteger('battery_pct')->nullable()->comment('電池残量(%)');
            $table->smallInteger('rssi')->nullable()->comment('電波強度(dBm)');
            $table->dateTime('last_received_at')->nullable()->comment('最終データ受信日時');
            $table->dateTime('last_human_detected_at')->nullable()->comment('最終人間検知日時');

            // 設定
            $table->unsignedTinyInteger('alert_threshold_hours')->default(24)->comment('未検知アラート閾値(時間)');
            $table->boolean('pet_exclusion_enabled')->default(false)->comment('ペット除外ON/OFF');
            $table->unsignedSmallInteger('pet_exclusion_threshold_cm')->default(100)->comment('ペット除外閾値(cm)');
            $table->unsignedSmallInteger('install_height_cm')->default(200)->comment('設置高さ(cm)');
            $table->boolean('away_mode')->default(false)->comment('外出モード');
            $table->dateTime('away_until')->nullable()->comment('外出モード解除予定');

            // 管理
            $table->unsignedBigInteger('organization_id')->nullable()->comment('所属組織ID（NULL=個人）');
            $table->dateTime('activated_at')->nullable()->comment('初回ログイン日時');
            $table->date('warranty_expires_at')->nullable()->comment('保証期限');
            $table->timestamps();

            $table->index('status');
            $table->index('organization_id');
            $table->index('last_received_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
