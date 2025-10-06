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
        // ChuyenMonReviewer
        Schema::create('ChuyenMonReviewer', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('track_id');
            $table->tinyInteger('expertise_level');
            
            $table->primary(['user_id', 'track_id']);
            
            $table->foreign('user_id')->references('user_id')->on('NguoiDung')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('track_id')->references('track_id')->on('TieuBan')
                ->onDelete('cascade')->onUpdate('cascade');
        });

        // Bidding
        Schema::create('Bidding', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('paper_id');
            $table->string('bidding_code', 20);
            $table->string('note', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->primary(['user_id', 'paper_id']);
            
            $table->foreign('user_id')->references('user_id')->on('NguoiDung')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('paper_id')->references('paper_id')->on('BaiBao')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('bidding_code')->references('bidding_code')->on('GiaTriBidding')
                ->onDelete('restrict')->onUpdate('cascade');
        });

        // COI
        Schema::create('COI', function (Blueprint $table) {
            $table->id('coi_id');
            $table->unsignedBigInteger('paper_id');
            $table->unsignedBigInteger('reviewer_id');
            $table->string('coi_code', 30);
            $table->enum('source_type', ['DECLARED', 'DETECTED']);
            $table->string('evidence', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('paper_id')->references('paper_id')->on('BaiBao')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('reviewer_id')->references('user_id')->on('NguoiDung')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('coi_code')->references('coi_code')->on('LoaiCOI')
                ->onDelete('restrict')->onUpdate('cascade');
        });

        // XuLyCOI
        Schema::create('XuLyCOI', function (Blueprint $table) {
            $table->id('decision_id');
            $table->unsignedBigInteger('coi_id');
            $table->unsignedBigInteger('chair_id');
            $table->enum('decision', ['CONFIRMED', 'REJECTED']);
            $table->string('note', 255)->nullable();
            $table->timestamp('decided_at')->useCurrent();
            
            $table->foreign('coi_id')->references('coi_id')->on('COI')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('chair_id')->references('user_id')->on('NguoiDung')
                ->onDelete('restrict')->onUpdate('cascade');
        });

        // PhanCongPhanBien
        Schema::create('PhanCongPhanBien', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->unsignedBigInteger('paper_id');
            $table->unsignedBigInteger('reviewer_id');
            $table->unsignedBigInteger('chair_id')->nullable();
            $table->string('status_code', 20)->default('INVITED');
            $table->char('token', 36);
            $table->timestamp('assigned_at')->useCurrent();
            $table->date('deadline')->nullable();
            
            $table->unique('token', 'uq_token');
            $table->unique(['paper_id', 'reviewer_id'], 'uq_assignment');
            
            $table->foreign('paper_id')->references('paper_id')->on('BaiBao')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('reviewer_id')->references('user_id')->on('NguoiDung')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('status_code')->references('status_code')->on('TrangThaiPhanCong')
                ->onDelete('restrict')->onUpdate('cascade');
        });

        // PhanBien
        Schema::create('PhanBien', function (Blueprint $table) {
            $table->id('review_id');
            $table->unsignedBigInteger('assignment_id');
            $table->string('recommendation_code', 20);
            $table->tinyInteger('score')->nullable();
            $table->longText('comment_author')->nullable();
            $table->longText('comment_chair')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            
            $table->foreign('assignment_id')->references('assignment_id')->on('PhanCongPhanBien')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('recommendation_code')->references('recommendation_code')->on('LoaiKhuyenNghi')
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
        Schema::dropIfExists('PhanBien');
        Schema::dropIfExists('PhanCongPhanBien');
        Schema::dropIfExists('XuLyCOI');
        Schema::dropIfExists('COI');
        Schema::dropIfExists('Bidding');
        Schema::dropIfExists('ChuyenMonReviewer');
    }
};
