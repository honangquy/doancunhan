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
        Schema::table('yeucauhoithao', function (Blueprint $table) {
            $table->string('faculty_name', 255)->nullable()->after('field');
            $table->string('affiliation', 255)->nullable()->after('objective');
            $table->string('chair_fullname', 255)->nullable()->after('affiliation');
            $table->string('chair_email', 255)->nullable()->after('chair_fullname');
            $table->string('chair_phone', 20)->nullable()->after('chair_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yeucauhoithao', function (Blueprint $table) {
            $table->dropColumn(['faculty_name', 'affiliation', 'chair_fullname', 'chair_email', 'chair_phone']);
        });
    }
};
