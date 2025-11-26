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
        // Step 1: Rename columns (only if they exist with old names)
        Schema::table('notification_templates', function (Blueprint $table) {
            // Check if old column names exist before renaming
            if (Schema::hasColumn('notification_templates', 'code')) {
                $table->renameColumn('code', 'template_code');
            }
            if (Schema::hasColumn('notification_templates', 'title')) {
                $table->renameColumn('title', 'template_name');
            }
            if (Schema::hasColumn('notification_templates', 'body')) {
                $table->renameColumn('body', 'body_html');
            }
        });
        
        // Step 2: Add new columns and drop old ones
        Schema::table('notification_templates', function (Blueprint $table) {
            // Add new columns (only if they don't exist)
            if (!Schema::hasColumn('notification_templates', 'event_type')) {
                $table->enum('event_type', ['deadline_submission', 'deadline_review', 'deadline_camera_ready', 'start_date', 'end_date'])
                    ->after('template_name')->nullable();
            }
            if (!Schema::hasColumn('notification_templates', 'days_before')) {
                $table->integer('days_before')->nullable()->comment('Số ngày trước khi gửi nhắc')->after('event_type');
            }
            if (!Schema::hasColumn('notification_templates', 'subject')) {
                $table->string('subject')->nullable()->after('days_before');
            }
            if (!Schema::hasColumn('notification_templates', 'body_text')) {
                $table->text('body_text')->nullable()->after('body_html');
            }
            if (!Schema::hasColumn('notification_templates', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('body_text');
            }
            
            // Drop old columns (only if they exist)
            if (Schema::hasColumn('notification_templates', 'default_channels')) {
                $table->dropColumn('default_channels');
            }
            if (Schema::hasColumn('notification_templates', 'variables')) {
                $table->dropColumn('variables');
            }
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
