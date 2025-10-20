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
        Schema::table('tieuban', function (Blueprint $table) {
            // Add committee name and description fields if they don't exist
            if (!Schema::hasColumn('tieuban', 'committee_name')) {
                $table->string('committee_name')->nullable();
            }
            
            if (!Schema::hasColumn('tieuban', 'description')) {
                $table->text('description')->nullable();
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
        Schema::table('tieuban', function (Blueprint $table) {
            $table->dropColumn(['committee_name', 'description']);
        });
    }
};
