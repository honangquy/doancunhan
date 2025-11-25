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
        // Use raw SQL to modify enum because Laravel Blueprint doesn't support enum modification
        DB::statement("ALTER TABLE baibao MODIFY COLUMN decision ENUM('ACCEPT', 'REJECT', 'REVISE', 'PUBLISHED') NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE baibao MODIFY COLUMN decision ENUM('ACCEPT', 'REJECT', 'REVISE') NULL");
    }
};
