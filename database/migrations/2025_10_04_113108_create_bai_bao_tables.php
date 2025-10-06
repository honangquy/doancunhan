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
        // BaiBao
        Schema::create('BaiBao', function (Blueprint $table) {
            $table->id('paper_id');
            $table->unsignedBigInteger('conference_id');
            $table->unsignedBigInteger('track_id')->nullable();
            $table->unsignedBigInteger('submitter_id');
            $table->string('title', 500);
            $table->longText('abstract')->nullable();
            $table->unsignedBigInteger('current_version_id')->nullable();
            $table->string('status_code', 30)->default('SUBMITTED');
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('conference_id')->references('conference_id')->on('HoiThao')
                ->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('track_id')->references('track_id')->on('TieuBan')
                ->onDelete('set null')->onUpdate('cascade');
            $table->foreign('submitter_id')->references('user_id')->on('NguoiDung')
                ->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('status_code')->references('status_code')->on('TrangThaiBaiBao')
                ->onDelete('restrict')->onUpdate('cascade');
        });

        // PhienBanBaiBao
        Schema::create('PhienBanBaiBao', function (Blueprint $table) {
            $table->id('version_id');
            $table->unsignedBigInteger('paper_id');
            $table->integer('version_no');
            $table->string('file_path', 500);
            $table->timestamp('submitted_at')->useCurrent();
            $table->string('note', 255)->nullable();
            
            $table->unique(['paper_id', 'version_no'], 'uk_version');
            
            $table->foreign('paper_id')->references('paper_id')->on('BaiBao')
                ->onDelete('cascade')->onUpdate('cascade');
        });

        // TacGiaBaiBao
        Schema::create('TacGiaBaiBao', function (Blueprint $table) {
            $table->unsignedBigInteger('paper_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('author_order');
            $table->boolean('is_contact')->default(false);
            $table->string('organization', 255)->nullable();
            
            $table->primary(['paper_id', 'user_id']);
            
            $table->foreign('paper_id')->references('paper_id')->on('BaiBao')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('user_id')->on('NguoiDung')
                ->onDelete('restrict')->onUpdate('cascade');
        });

        // LichSuTrangThai
        Schema::create('LichSuTrangThai', function (Blueprint $table) {
            $table->id('history_id');
            $table->unsignedBigInteger('paper_id');
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamp('changed_at')->useCurrent();
            $table->string('note', 255)->nullable();
            
            $table->foreign('paper_id')->references('paper_id')->on('BaiBao')
                ->onDelete('cascade')->onUpdate('cascade');
        });

        // YeuCauChinhSua
        Schema::create('YeuCauChinhSua', function (Blueprint $table) {
            $table->id('request_id');
            $table->unsignedBigInteger('paper_id');
            $table->unsignedBigInteger('requester_id');
            $table->timestamp('requested_at')->useCurrent();
            $table->date('deadline')->nullable();
            $table->string('note', 255)->nullable();
            
            $table->foreign('paper_id')->references('paper_id')->on('BaiBao')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('requester_id')->references('user_id')->on('NguoiDung')
                ->onDelete('restrict')->onUpdate('cascade');
        });

        // RutBaiBao
        Schema::create('RutBaiBao', function (Blueprint $table) {
            $table->id('withdrawal_id');
            $table->unsignedBigInteger('paper_id');
            $table->unsignedBigInteger('author_id');
            $table->timestamp('withdrawn_at')->useCurrent();
            $table->string('reason', 255)->nullable();
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('status', 20)->default('PENDING');
            
            $table->foreign('paper_id')->references('paper_id')->on('BaiBao')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('author_id')->references('user_id')->on('NguoiDung')
                ->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('approver_id')->references('user_id')->on('NguoiDung')
                ->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('RutBaiBao');
        Schema::dropIfExists('YeuCauChinhSua');
        Schema::dropIfExists('LichSuTrangThai');
        Schema::dropIfExists('TacGiaBaiBao');
        Schema::dropIfExists('PhienBanBaiBao');
        Schema::dropIfExists('BaiBao');
    }
};
