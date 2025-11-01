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
        Schema::table('hoithao', function (Blueprint $table) {
            // Loại bỏ các cột deadline trùng lặp - giữ lại các cột cũ, xóa các cột mới trùng
            if (Schema::hasColumn('hoithao', 'submission_deadline')) {
                $table->dropColumn('submission_deadline');
            }
            if (Schema::hasColumn('hoithao', 'review_deadline')) {
                $table->dropColumn('review_deadline');
            }
            if (Schema::hasColumn('hoithao', 'camera_ready_deadline')) {
                $table->dropColumn('camera_ready_deadline');
            }
            
            // Loại bỏ cột conference_date vì đã có start_date và end_date
            if (Schema::hasColumn('hoithao', 'conference_date')) {
                $table->dropColumn('conference_date');
            }
            
            // Loại bỏ conference_name vì đã có title
            if (Schema::hasColumn('hoithao', 'conference_name')) {
                $table->dropColumn('conference_name');
            }
            
            // Loại bỏ cfp_url vì sẽ dùng file PDF thay thế
            if (Schema::hasColumn('hoithao', 'cfp_url')) {
                $table->dropColumn('cfp_url');
            }
            
            // Loại bỏ các cột chair thừa - chỉ giữ chair_id và contact thông tin
            if (Schema::hasColumn('hoithao', 'chair_name')) {
                $table->dropColumn('chair_name');
            }
            if (Schema::hasColumn('hoithao', 'chair_email')) {
                $table->dropColumn('chair_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hoithao', function (Blueprint $table) {
            // Thêm lại các cột đã xóa trong trường hợp rollback
            $table->date('submission_deadline')->nullable();
            $table->date('review_deadline')->nullable();
            $table->date('camera_ready_deadline')->nullable();
            $table->date('conference_date')->nullable();
            $table->string('conference_name')->nullable();
            $table->string('cfp_url', 500)->nullable();
            $table->string('chair_name', 100)->nullable();
            $table->string('chair_email', 100)->nullable();
        });
    }
};
