<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->date('billing_start_date')->nullable()->after('premium_enabled')->comment('pay.jp 定期課金開始日。マスターがカード登録確認後に設定。デフォルト翌月1日。');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('billing_start_date');
        });
    }
};
