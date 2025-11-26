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
        // Insert USER role into loaivaitro table
        DB::table('loaivaitro')->insert([
            'role_code' => 'USER',
            'role_name' => 'Người dùng'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove USER role from loaivaitro table
        DB::table('loaivaitro')->where('role_code', 'USER')->delete();
    }
};
