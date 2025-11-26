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
        Schema::create('reviewer_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->unsignedBigInteger('conference_id');
            $table->unsignedBigInteger('invited_by');
            $table->string('token', 64)->unique();
            $table->enum('status', ['PENDING', 'ACCEPTED', 'REJECTED', 'EXPIRED'])->default('PENDING');
            $table->timestamp('expires_at');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['email', 'conference_id']);
            $table->index('token');
            $table->index('status');
            
            // Foreign keys
            $table->foreign('conference_id')->references('conference_id')->on('hoithao')->onDelete('cascade');
            $table->foreign('invited_by')->references('user_id')->on('nguoidung')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviewer_invitations');
    }
};
