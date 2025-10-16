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
        Schema::create('join_requests', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->unsignedBigInteger('conference_id')->comment('ID hội thảo');
            $table->unsignedBigInteger('user_id')->comment('ID người dùng yêu cầu tham gia');
            
            // Request details  
            $table->enum('role', ['AUTHOR', 'REVIEWER'])->comment('Vai trò muốn tham gia');
            $table->text('message')->nullable()->comment('Lời nhắn từ người dùng');
            
            // Status & processing
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING')->comment('Trạng thái xử lý');
            $table->unsignedBigInteger('processed_by')->nullable()->comment('ID người xử lý yêu cầu');
            $table->timestamp('processed_at')->nullable()->comment('Thời gian xử lý');
            $table->text('admin_notes')->nullable()->comment('Ghi chú từ admin');
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('conference_id')->references('conference_id')->on('hoithao')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('nguoidung')->onDelete('cascade');
            $table->foreign('processed_by')->references('user_id')->on('nguoidung')->onDelete('set null');
            
            // Indexes for performance
            $table->index(['conference_id', 'status']);
            $table->index(['user_id', 'conference_id']);
            $table->unique(['user_id', 'conference_id', 'role'], 'unique_user_conference_role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('join_requests');
    }
};
