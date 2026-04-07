<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('sim_id', 5)->nullable()->unique()->after('device_id')
                ->comment('SIMのID（半角英数字5桁）。デバイスからのJSONに含まれ、品番との紐付けに使用。');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropUnique(['sim_id']);
            $table->dropColumn('sim_id');
        });
    }
};
