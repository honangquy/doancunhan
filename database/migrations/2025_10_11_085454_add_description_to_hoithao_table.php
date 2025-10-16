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
        Schema::table('hoithao', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title')->comment('Mô tả ngắn về hội thảo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hoithao', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
