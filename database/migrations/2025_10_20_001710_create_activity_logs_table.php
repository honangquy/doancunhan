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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_type'); // LOGIN, ACTION, ERROR, SYSTEM
            $table->unsignedBigInteger('user_id')->nullable(); // Người dùng thực hiện (nullable cho system logs)
            $table->string('action'); // Mô tả hành động
            $table->text('description')->nullable(); // Chi tiết mô tả
            $table->json('properties')->nullable(); // Dữ liệu JSON bổ sung
            $table->string('ip_address', 45)->nullable(); // IPv4/IPv6
            $table->text('user_agent')->nullable(); // Browser/device info
            $table->string('model_type')->nullable(); // Model class name (User, Conference, etc.)
            $table->unsignedBigInteger('model_id')->nullable(); // Model ID
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->timestamps();
            
            // Indexes
            $table->index(['log_type', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index('severity');
            
                        // Foreign key constraints  
            $table->foreign('user_id')->references('user_id')->on('NguoiDung')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};
