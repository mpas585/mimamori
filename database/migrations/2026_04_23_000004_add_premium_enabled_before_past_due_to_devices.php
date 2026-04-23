<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * past_due遷移前のpremium_enabled状態を保存するカラムを追加する。
     * markActive時にこの値を元にpremium_enabledを復元することで、
     * 「もともとfalseだったデバイスまで一律trueに戻る」副作用を防ぐ。
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->boolean('premium_enabled_before_past_due')
                  ->nullable()
                  ->comment('past_due遷移直前のpremium_enabled値（復元用、NULL=未使用）');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('premium_enabled_before_past_due');
        });
    }
};
