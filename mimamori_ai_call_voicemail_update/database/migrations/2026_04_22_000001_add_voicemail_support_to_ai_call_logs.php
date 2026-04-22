<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_call_logs', function (Blueprint $table) {
            $table->string('answered_by', 30)->nullable()->after('call_sid')->comment('Twilio AMD判定結果');
            $table->unsignedBigInteger('responded_inbound_id')->nullable()->after('answered_by')->comment('折り返しで紐付いたinboundログID');
        });

        // judgment enum に voicemail を追加
        DB::statement("ALTER TABLE ai_call_logs MODIFY COLUMN judgment ENUM('good','check','alert','unclear','voicemail') NULL COMMENT 'AIによる判定'");
    }

    public function down(): void
    {
        Schema::table('ai_call_logs', function (Blueprint $table) {
            $table->dropColumn(['answered_by', 'responded_inbound_id']);
        });

        DB::statement("ALTER TABLE ai_call_logs MODIFY COLUMN judgment ENUM('good','check','alert','unclear') NULL COMMENT 'AIによる判定'");
    }
};
