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
        // Use raw SQL to modify the column
        DB::statement('ALTER TABLE phanbien MODIFY submitted_at TIMESTAMP NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Restore original definition with CURRENT_TIMESTAMP default
        DB::statement('ALTER TABLE phanbien MODIFY submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }
};
