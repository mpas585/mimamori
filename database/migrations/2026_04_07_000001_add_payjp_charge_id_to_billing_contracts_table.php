<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_contracts', function (Blueprint $table) {
            // 3Dセキュア処理中の一時Charge IDを保持するカラム
            $table->string('payjp_charge_id')->nullable()->after('payjp_customer_id')
                ->comment('3DS処理中のcharge ID。認証完了後にnullにする。');
        });
    }

    public function down(): void
    {
        Schema::table('billing_contracts', function (Blueprint $table) {
            $table->dropColumn('payjp_charge_id');
        });
    }
};
