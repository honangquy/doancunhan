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
        Schema::table('reviewer_bidding', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('coi_reason')
                ->comment('Reviewer đã gửi kết quả bidding cho Chair, không thể chỉnh sửa nữa');
            $table->timestamp('locked_at')->nullable()->after('is_locked')
                ->comment('Thời điểm gửi kết quả bidding');
            $table->integer('round_no')->default(1)->after('locked_at')
                ->comment('Đợt bidding thứ mấy (tương ứng với round_no trong reviewer_paper_candidate)');

            // Index để query nhanh các bidding đã lock
            $table->index(['is_locked', 'conference_id'], 'idx_locked_conference');
            $table->index(['user_id', 'is_locked', 'round_no'], 'idx_reviewer_locked_round');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reviewer_bidding', function (Blueprint $table) {
            $table->dropIndex('idx_locked_conference');
            $table->dropIndex('idx_reviewer_locked_round');
            $table->dropColumn(['is_locked', 'locked_at', 'round_no']);
        });
    }
};
