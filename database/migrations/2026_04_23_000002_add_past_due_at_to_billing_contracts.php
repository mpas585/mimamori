<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_contracts', function (Blueprint $table) {
            $table->dateTime('past_due_at')
                  ->nullable()
                  ->comment('past_due遷移日時（30日猶予期間のカウント起点）');
            $table->index('past_due_at');
        });
    }

    public function down(): void
    {
        Schema::table('billing_contracts', function (Blueprint $table) {
            $table->dropIndex(['past_due_at']);
            $table->dropColumn('past_due_at');
        });
    }
};
