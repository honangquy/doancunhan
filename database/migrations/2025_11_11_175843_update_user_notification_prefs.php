<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Bảng đã tồn tại, chỉ thêm cột nếu chưa có
        if (!Schema::hasColumn('user_notification_prefs', 'allow_email')) {
            Schema::table('user_notification_prefs', function (Blueprint $table) {
                $table->boolean('allow_email')->default(true)->after('user_id');
                $table->boolean('allow_conference_reminders')->default(true)->after('allow_email');
            });
        }
    }

    public function down()
    {
        Schema::table('user_notification_prefs', function (Blueprint $table) {
            $table->dropColumn(['allow_email', 'allow_conference_reminders']);
        });
    }
};
