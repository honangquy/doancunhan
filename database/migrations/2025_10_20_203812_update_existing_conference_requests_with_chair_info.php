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
        // Update existing records where chair info is null
        DB::statement("
            UPDATE yeucauhoithao ycht
            JOIN nguoidung nd ON ycht.user_id = nd.user_id
            SET ycht.chair_fullname = nd.full_name,
                ycht.chair_email = nd.email
            WHERE ycht.chair_fullname IS NULL 
               OR ycht.chair_email IS NULL
               OR ycht.chair_fullname = ''
               OR ycht.chair_email = ''
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No need to revert this data migration
    }
};
