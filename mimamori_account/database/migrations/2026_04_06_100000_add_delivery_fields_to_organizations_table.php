<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('delivery_name', 100)->nullable()->after('address')->comment('配送先氏名');
            $table->string('delivery_postal', 10)->nullable()->after('delivery_name')->comment('配送先郵便番号');
            $table->string('delivery_address', 500)->nullable()->after('delivery_postal')->comment('配送先住所');
            $table->string('delivery_phone', 20)->nullable()->after('delivery_address')->comment('配送先電話番号');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['delivery_name', 'delivery_postal', 'delivery_address', 'delivery_phone']);
        });
    }
};
