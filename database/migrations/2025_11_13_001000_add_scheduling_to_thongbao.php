<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('thongbao', function (Blueprint $table) {
            $table->datetime('scheduled_at')->nullable()->after('audience')->comment('Thời gian lên lịch gửi');
            $table->datetime('sent_at')->nullable()->after('scheduled_at')->comment('Thời gian đã gửi');
            $table->enum('status', ['DRAFT', 'SCHEDULED', 'SENT', 'FAILED'])
                  ->default('DRAFT')
                  ->after('sent_at')
                  ->comment('Trạng thái: DRAFT=Nháp, SCHEDULED=Đã lên lịch, SENT=Đã gửi, FAILED=Thất bại');
            $table->text('channels')->nullable()->after('status')->comment('Các kênh gửi: EMAIL, SYSTEM (JSON array)');
            
            // Index để query hiệu quả
            $table->index(['status', 'scheduled_at'], 'idx_status_scheduled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('thongbao', function (Blueprint $table) {
            $table->dropIndex('idx_status_scheduled');
            $table->dropColumn(['scheduled_at', 'sent_at', 'status', 'channels']);
        });
    }
};
