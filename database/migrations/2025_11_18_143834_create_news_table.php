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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug')->unique();
            $table->enum('type', ['news', 'event'])->default('news');
            $table->unsignedBigInteger('conference_id')->nullable();
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->string('thumbnail_path')->nullable();
            $table->string('attachment_path')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->dateTime('published_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('conference_id')->references('conference_id')->on('hoithao')->onDelete('set null');
            $table->foreign('created_by')->references('user_id')->on('nguoidung')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('nguoidung')->onDelete('set null');
            
            // Indexes
            $table->index('type');
            $table->index('status');
            $table->index('published_at');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
};
