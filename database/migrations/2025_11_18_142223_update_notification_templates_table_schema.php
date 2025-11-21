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
        // Step 1: Rename columns
        Schema::table('notification_templates', function (Blueprint $table) {
            $table->renameColumn('code', 'template_code');
            $table->renameColumn('title', 'template_name');
            $table->renameColumn('body', 'body_html');
        });
        
        // Step 2: Add new columns and drop old ones
        Schema::table('notification_templates', function (Blueprint $table) {
            // Add new columns
            $table->enum('event_type', ['deadline_submission', 'deadline_review', 'deadline_camera_ready', 'start_date', 'end_date'])
                ->after('template_name')->nullable();
            $table->integer('days_before')->nullable()->comment('Số ngày trước khi gửi nhắc')->after('event_type');
            $table->string('subject')->nullable()->after('days_before');
            $table->text('body_text')->nullable()->after('body_html');
            $table->boolean('is_active')->default(true)->after('body_text');
            
            // Drop old columns
            $table->dropColumn(['default_channels', 'variables']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reverse: Step 1 - Add back old columns, drop new ones
        Schema::table('notification_templates', function (Blueprint $table) {
            $table->dropColumn(['event_type', 'days_before', 'subject', 'body_text', 'is_active']);
            $table->text('default_channels')->nullable();
            $table->text('variables')->nullable();
        });
        
        // Reverse: Step 2 - Rename columns back
        Schema::table('notification_templates', function (Blueprint $table) {
            $table->renameColumn('template_code', 'code');
            $table->renameColumn('template_name', 'title');
            $table->renameColumn('body_html', 'body');
        });
    }
};
