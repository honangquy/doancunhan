<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('notification_templates')) {
            Schema::create('notification_templates', function (Blueprint $table) {
                $table->id('template_id');
                $table->string('template_code', 50)->unique();
                $table->string('template_name');
                $table->enum('event_type', ['deadline_submission', 'deadline_review', 'deadline_camera_ready', 'start_date', 'end_date']);
                $table->integer('days_before')->comment('Số ngày trước khi gửi nhắc');
                $table->string('subject');
                $table->text('body_html');
                $table->text('body_text')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('notification_templates');
    }
};
