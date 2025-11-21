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
        Schema::create('reviewer_paper_candidate', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paper_id');
            $table->unsignedBigInteger('reviewer_id')->comment('user_id của reviewer');
            $table->unsignedBigInteger('conference_id');
            $table->unsignedBigInteger('sent_by')->comment('Chair gửi candidate list');
            $table->integer('round_no')->default(1)->comment('Đợt gửi thứ mấy');
            $table->text('note')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('paper_id')->references('paper_id')->on('baibao')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('user_id')->on('nguoidung')->onDelete('cascade');
            $table->foreign('conference_id')->references('conference_id')->on('hoithao')->onDelete('cascade');
            $table->foreign('sent_by')->references('user_id')->on('nguoidung')->onDelete('cascade');

            // Unique constraint: 1 reviewer chỉ nhận 1 lần candidate cho 1 bài trong 1 đợt
            $table->unique(['paper_id', 'reviewer_id', 'round_no'], 'unique_paper_reviewer_round');

            // Indexes
            $table->index('reviewer_id', 'idx_candidate_reviewer');
            $table->index(['conference_id', 'round_no'], 'idx_candidate_conf_round');
            $table->index(['reviewer_id', 'conference_id'], 'idx_candidate_reviewer_conf');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviewer_paper_candidate');
    }
};
