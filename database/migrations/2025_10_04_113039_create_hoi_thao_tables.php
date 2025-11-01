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
        // YeuCauHoiThao
        Schema::create('yeucauhoithao', function (Blueprint $table) {
            $table->id('request_id');
            $table->unsignedBigInteger('user_id');
            $table->string('title', 255);
            $table->string('field', 255)->nullable();
            $table->enum('level_code', ['KHOA', 'TRUONG']);
            $table->date('expected_date')->nullable();
            $table->string('objective', 500)->nullable();
            $table->string('proposal_file', 255);
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->string('approval_note', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('approved_at')->nullable();
            
            $table->foreign('user_id')->references('user_id')->on('nguoidung')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('approver_id')->references('user_id')->on('nguoidung')
                ->onDelete('set null')->onUpdate('cascade');
        });

        // HoiThao
        Schema::create('hoithao', function (Blueprint $table) {
            $table->id('conference_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('level_code', 20);
            $table->unsignedBigInteger('faculty_id')->nullable();
            $table->string('title', 255);
            $table->smallInteger('year');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('deadline_submission')->nullable();
            $table->date('deadline_review')->nullable();
            $table->date('deadline_camera_ready')->nullable();
            $table->string('status', 50)->nullable();
            
            $table->foreign('parent_id')->references('conference_id')->on('hoithao')
                ->onDelete('set null')->onUpdate('cascade');
            $table->foreign('level_code')->references('level_code')->on('caphoithao')
                ->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('faculty_id')->references('faculty_id')->on('khoa')
                ->onDelete('set null')->onUpdate('cascade');
        });

        // TieuBan
        Schema::create('tieuban', function (Blueprint $table) {
            $table->id('track_id');
            $table->unsignedBigInteger('conference_id');
            $table->string('title', 200);
            $table->unsignedBigInteger('chair_id')->nullable();
            
            $table->foreign('conference_id')->references('conference_id')->on('hoithao')
                ->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('chair_id')->references('user_id')->on('nguoidung')
                ->onDelete('set null')->onUpdate('cascade');
        });

        // ThongBao
        Schema::create('thongbao', function (Blueprint $table) {
            $table->id('announcement_id');
            $table->unsignedBigInteger('conference_id');
            $table->string('title', 255);
            $table->longText('content');
            $table->enum('audience', ['ALL', 'AUTHORS', 'REVIEWERS'])->default('ALL');
            $table->unsignedBigInteger('created_by');
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('conference_id')->references('conference_id')->on('hoithao')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('created_by')->references('user_id')->on('nguoidung')
                ->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thongbao');
        Schema::dropIfExists('tieuban');
        Schema::dropIfExists('hoithao');
        Schema::dropIfExists('yeucauhoithao');
    }
};
