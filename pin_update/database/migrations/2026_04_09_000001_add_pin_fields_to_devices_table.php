<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('initial_pin', 4)->nullable()->after('pin_hash')->comment('初期PIN（平文）');
            $table->string('current_pin', 4)->nullable()->after('initial_pin')->comment('現在PIN（平文）');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['initial_pin', 'current_pin']);
        });
    }
};
