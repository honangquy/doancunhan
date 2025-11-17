<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Bảng đã tồn tại, chỉ thêm cột nếu cần
        if (!Schema::hasColumn('notification_outbox', 'conference_id')) {
            Schema::table('notification_outbox', function (Blueprint $table) {
                $table->unsignedBigInteger('conference_id')->nullable()->after('id');
                $table->foreign('conference_id')->references('conference_id')->on('hoithao')->onDelete('cascade');
            });
        }
        
        if (!Schema::hasColumn('notification_outbox', 'template_id')) {
            Schema::table('notification_outbox', function (Blueprint $table) {
                $table->unsignedBigInteger('template_id')->nullable()->after('conference_id');
                $table->foreign('template_id')->references('template_id')->on('notification_templates')->onDelete('set null');
            });
        }
        
        if (!Schema::hasColumn('notification_outbox', 'event_type')) {
            Schema::table('notification_outbox', function (Blueprint $table) {
                $table->string('event_type', 50)->nullable()->after('template_id')->comment('deadline_submission, deadline_review, etc');
                $table->integer('retry_count')->default(0)->after('status');
                $table->text('error_message')->nullable()->after('retry_count');
            });
        }
    }

    public function down()
    {
        Schema::table('notification_outbox', function (Blueprint $table) {
            $table->dropForeign(['conference_id']);
            $table->dropForeign(['template_id']);
            $table->dropColumn(['conference_id', 'template_id', 'event_type', 'retry_count', 'error_message']);
        });
    }
};
