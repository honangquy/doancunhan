<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('conference_id')->nullable();
            $table->unsignedBigInteger('announcement_id')->nullable(); // Link to thongbao table
            $table->string('type')->default('ANNOUNCEMENT'); // ANNOUNCEMENT, REVIEW, DEADLINE, etc.
            $table->string('title');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('user_id')->on('nguoidung')->onDelete('cascade');
            $table->foreign('conference_id')->references('conference_id')->on('hoithao')->onDelete('cascade');
            $table->foreign('announcement_id')->references('announcement_id')->on('thongbao')->onDelete('cascade');

            // Indexes for performance
            $table->index(['user_id', 'is_read']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
