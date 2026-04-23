<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dateTime('suspended_at')
                  ->nullable()
                  ->comment('デバイス停止日時（課金失敗後30日経過で設定）');
            $table->index('suspended_at');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropIndex(['suspended_at']);
            $table->dropColumn('suspended_at');
        });
    }
};
