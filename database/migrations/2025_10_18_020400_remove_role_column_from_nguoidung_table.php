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
        Schema::table('nguoidung', function (Blueprint $table) {
            // Remove the role column since we use VaiTroNguoiDung table for roles
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nguoidung', function (Blueprint $table) {
            // Add back the role column if we need to rollback
            $table->string('role', 20)->default('AUTHOR')->after('email');
        });
    }
};
