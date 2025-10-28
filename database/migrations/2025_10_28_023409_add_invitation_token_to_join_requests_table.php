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
        Schema::table('join_requests', function (Blueprint $table) {
            $table->string('invitation_token', 64)->nullable()->after('admin_notes')->comment('Token from reviewer invitation if applicable');
            $table->index('invitation_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('join_requests', function (Blueprint $table) {
            $table->dropIndex(['invitation_token']);
            $table->dropColumn('invitation_token');
        });
    }
};
