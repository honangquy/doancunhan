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
            $table->bigIncrements('news_id');
            $table->string('title', 255);
            $table->string('slug', 255)->unique()->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('cover_image', 255)->nullable();
            $table->enum('category', ['NEWS', 'ANNOUNCEMENT', 'EVENT', 'GUIDE'])->default('NEWS');
            $table->unsignedBigInteger('conference_id')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['DRAFT', 'PENDING', 'PUBLISHED', 'ARCHIVED'])->default('PUBLISHED');
            $table->dateTime('published_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('conference_id')->references('conference_id')->on('hoithao')->onDelete('set null');
            $table->foreign('created_by')->references('user_id')->on('nguoidung')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('nguoidung')->onDelete('set null');

            // Indexes
            $table->index('category');
            $table->index('status');
            $table->index('conference_id');
            $table->index('created_by');
            $table->index('published_at');
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
