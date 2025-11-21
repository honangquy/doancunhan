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
        Schema::table('thongbao', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['conference_id']);

            // Make conference_id nullable
            $table->unsignedBigInteger('conference_id')->nullable()->change();

            // Re-add foreign key with nullable support
            $table->foreign('conference_id')
                ->references('conference_id')
                ->on('hoithao')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('thongbao', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['conference_id']);

            // Make conference_id NOT nullable again
            $table->unsignedBigInteger('conference_id')->nullable(false)->change();

            // Re-add foreign key
            $table->foreign('conference_id')
                ->references('conference_id')
                ->on('hoithao')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }
};
