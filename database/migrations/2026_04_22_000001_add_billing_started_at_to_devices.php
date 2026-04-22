<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->timestamp('billing_started_at')
                  ->nullable()
                  ->after('billing_start_date')
                  ->comment('課金開始済みフラグ。billing_start_date到来時にStartScheduledBillingコマンドが立てる。');
        });

        // 後方互換: 既存のactiveなBillingContract配下のデバイスには billing_started_at = now を立てる
        // （本移行前から既に課金対象だったデバイスを、新ロジックで対象外にしないため）
        $orgIds = DB::table('billing_contracts')
            ->where('status', 'active')
            ->whereNotNull('organization_id')
            ->pluck('organization_id')
            ->toArray();

        if (!empty($orgIds)) {
            DB::table('devices')
                ->whereIn('organization_id', $orgIds)
                ->whereNull('billing_started_at')
                ->update(['billing_started_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('billing_started_at');
        });
    }
};
