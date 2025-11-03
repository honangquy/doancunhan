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
        // Drop the existing foreign key constraint
        Schema::table('phanbien', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
        });
        
        // Add new foreign key constraint to reviewer_assignments table
        Schema::table('phanbien', function (Blueprint $table) {
            $table->foreign('assignment_id')->references('id')->on('reviewer_assignments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the new foreign key constraint
        Schema::table('phanbien', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
        });
        
        // Restore the original foreign key constraint to phancongphanbien
        Schema::table('phanbien', function (Blueprint $table) {
            $table->foreign('assignment_id')->references('assignment_id')->on('phancongphanbien')->onDelete('cascade');
        });
    }
};
