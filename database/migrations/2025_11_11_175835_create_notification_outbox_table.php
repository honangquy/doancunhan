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
        if (!Schema::hasTable('notification_outbox')) {
            Schema::create('notification_outbox', function (Blueprint $table) {
                $table->id();
                $table->string('recipient_email');
                $table->string('subject');
                $table->text('body_html');
                $table->text('body_text')->nullable();
                $table->enum('status', ['PENDING', 'SENT', 'FAILED'])->default('PENDING');
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_outbox');
    }
};
