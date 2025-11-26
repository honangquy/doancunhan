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
            if (!Schema::hasColumn('hoithao', 'cfp_file_path')) {
                $table->string('cfp_file_path', 500)->nullable()->after('cfp_url')->comment('Đường dẫn file Call for Papers (PDF)');
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
        Schema::table('hoithao', function (Blueprint $table) {
            if (Schema::hasColumn('hoithao', 'cfp_file_path')) {
                $table->dropColumn('cfp_file_path');
            }
        });
    }
};
