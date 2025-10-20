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
            // Basic conference info
            if (!Schema::hasColumn('hoithao', 'conference_name')) {
                $table->string('conference_name')->nullable();
            }
            if (!Schema::hasColumn('hoithao', 'conference_date')) {
                $table->date('conference_date')->nullable();
            }
            if (!Schema::hasColumn('hoithao', 'chair_id')) {
                $table->unsignedInteger('chair_id')->nullable();
            }
            if (!Schema::hasColumn('hoithao', 'reviewers_per_paper')) {
                $table->integer('reviewers_per_paper')->default(3);
            }
            if (!Schema::hasColumn('hoithao', 'enable_coi_check')) {
                $table->boolean('enable_coi_check')->default(false);
            }
            if (!Schema::hasColumn('hoithao', 'submission_deadline')) {
                $table->date('submission_deadline')->nullable();
            }
            if (!Schema::hasColumn('hoithao', 'review_deadline')) {
                $table->date('review_deadline')->nullable();
            }
            if (!Schema::hasColumn('hoithao', 'camera_ready_deadline')) {
                $table->date('camera_ready_deadline')->nullable();
            }
            if (!Schema::hasColumn('hoithao', 'result_announcement_deadline')) {
                $table->date('result_announcement_deadline')->nullable();
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
            $table->dropColumn([
                'conference_name',
                'conference_date', 
                'chair_id',
                'reviewers_per_paper',
                'enable_coi_check',
                'submission_deadline',
                'review_deadline',
                'camera_ready_deadline',
                'result_announcement_deadline'
            ]);
        });
    }
};
