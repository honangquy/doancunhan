<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Add notifiable columns for Laravel standard compatibility
            $table->string('notifiable_type')->default('App\\Models\\User')->after('id');
            $table->unsignedBigInteger('notifiable_id')->after('notifiable_type');

            // Add index for better performance
            $table->index(['notifiable_type', 'notifiable_id']);
        });

        // Update existing records to set notifiable_id = user_id
        DB::statement("UPDATE notifications SET notifiable_id = user_id WHERE notifiable_id IS NULL OR notifiable_id = 0");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['notifiable_type', 'notifiable_id']);
            $table->dropColumn(['notifiable_type', 'notifiable_id']);
        });
    }
};
