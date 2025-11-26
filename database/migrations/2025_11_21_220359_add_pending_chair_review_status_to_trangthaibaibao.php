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
        DB::table('TrangThaiBaiBao')->insert([
            'status_code' => 'PENDING_CHAIR_REVIEW',
            'status_name' => 'Chờ Chair duyệt lại'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('TrangThaiBaiBao')->where('status_code', 'PENDING_CHAIR_REVIEW')->delete();
    }
};
