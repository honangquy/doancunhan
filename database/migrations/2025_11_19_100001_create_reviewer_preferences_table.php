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
        Schema::create('reviewer_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('conference_id');
            $table->integer('max_papers_wanted')->default(3)->comment('Số bài tối đa reviewer muốn nhận');
            $table->text('expertise')->nullable()->comment('Lĩnh vực chuyên môn');
            $table->text('note')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('user_id')->on('nguoidung')->onDelete('cascade');
            $table->foreign('conference_id')->references('conference_id')->on('hoithao')->onDelete('cascade');

            // Unique constraint: 1 reviewer chỉ có 1 preference cho 1 conference
            $table->unique(['user_id', 'conference_id'], 'unique_reviewer_conference_pref');

            // Indexes
            $table->index('conference_id', 'idx_pref_conference');
            $table->index(['user_id', 'conference_id'], 'idx_pref_user_conf');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviewer_preferences');
    }
};
