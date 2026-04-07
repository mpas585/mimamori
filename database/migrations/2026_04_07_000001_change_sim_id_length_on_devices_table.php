<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('sim_id', 22)->nullable()->unique()->change()
                ->comment('SIMのICCID（最大22桁の数字）。デバイスからのJSONに含まれ、品番との紐付けに使用。');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('sim_id', 5)->nullable()->unique()->change();
        });
    }
};
