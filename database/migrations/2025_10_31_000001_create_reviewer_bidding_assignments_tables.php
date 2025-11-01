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
        // Create reviewer_bidding table
        Schema::create('reviewer_bidding', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('paper_id');
            $table->unsignedBigInteger('conference_id');
            $table->tinyInteger('bidding_value')->comment('0-3: 0=No bid, 1=Willing, 2=Able, 3=Eager');
            $table->boolean('coi')->default(false)->comment('Conflict of Interest');
            $table->text('coi_reason')->nullable()->comment('Reason for COI declaration');
            $table->text('note')->nullable()->comment('Optional note from reviewer');
            $table->timestamps();

            // Composite unique key - one bid per reviewer per paper
            $table->unique(['user_id', 'paper_id'], 'unique_reviewer_paper_bid');
            
            // Foreign key constraints
            $table->foreign('user_id')->references('user_id')->on('nguoidung')->onDelete('cascade');
            $table->foreign('paper_id')->references('paper_id')->on('baibao')->onDelete('cascade');
            $table->foreign('conference_id')->references('conference_id')->on('hoithao')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index(['conference_id', 'bidding_value'], 'idx_conference_bid');
            $table->index(['user_id', 'conference_id'], 'idx_reviewer_conference');
            $table->index(['coi'], 'idx_coi_flag');
        });

        // Create reviewer_assignments table  
        Schema::create('reviewer_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Reviewer ID');
            $table->unsignedBigInteger('paper_id');
            $table->unsignedBigInteger('conference_id');
            $table->unsignedBigInteger('assigned_by')->comment('Chair user_id who made the assignment');
            $table->enum('assignment_method', ['MANUAL', 'AUTO'])->default('MANUAL');
            $table->enum('status', ['PENDING', 'ACCEPTED', 'DECLINED', 'COMPLETED'])->default('PENDING');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('review_submitted_at')->nullable();
            $table->text('decline_reason')->nullable();
            $table->json('assignment_metadata')->nullable()->comment('Store bid_value, coi_status etc at assignment time');
            $table->timestamps();

            // Composite unique key - one assignment per reviewer per paper
            $table->unique(['user_id', 'paper_id'], 'unique_reviewer_paper_assignment');
            
            // Foreign key constraints
            $table->foreign('user_id')->references('user_id')->on('nguoidung')->onDelete('cascade');
            $table->foreign('paper_id')->references('paper_id')->on('baibao')->onDelete('cascade');
            $table->foreign('conference_id')->references('conference_id')->on('hoithao')->onDelete('cascade');
            $table->foreign('assigned_by')->references('user_id')->on('nguoidung')->onDelete('cascade');
            
            // Indexes for queries
            $table->index(['conference_id', 'status'], 'idx_conference_status');
            $table->index(['user_id', 'status'], 'idx_reviewer_status');
            $table->index(['assigned_by'], 'idx_assigned_by');
            $table->index(['assignment_method'], 'idx_assignment_method');
        });

        // Create assignment_notifications table for tracking email notifications
        Schema::create('assignment_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->enum('notification_type', ['ASSIGNMENT', 'REMINDER', 'COI_ALERT']);
            $table->enum('status', ['PENDING', 'SENT', 'FAILED']);
            $table->text('email_content')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('assignment_id')->references('id')->on('reviewer_assignments')->onDelete('cascade');
            $table->index(['notification_type', 'status'], 'idx_notification_type_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assignment_notifications');
        Schema::dropIfExists('reviewer_assignments');
        Schema::dropIfExists('reviewer_bidding');
    }
};