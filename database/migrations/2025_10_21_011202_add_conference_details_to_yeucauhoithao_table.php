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
            // Chỉ thêm các cột chưa có
            if (!Schema::hasColumn('yeucauhoithao', 'year')) {
                $table->year('year')->nullable()->after('acronym');
            }
            if (!Schema::hasColumn('yeucauhoithao', 'description')) {
                $table->text('description')->nullable()->after('objective');
            }
            if (!Schema::hasColumn('yeucauhoithao', 'detailed_description')) {
                $table->text('detailed_description')->nullable()->after('description');
            }
            if (!Schema::hasColumn('yeucauhoithao', 'submission_guidelines')) {
                $table->text('submission_guidelines')->nullable()->after('detailed_description');
            }
            if (!Schema::hasColumn('yeucauhoithao', 'cfp_url')) {
                $table->string('cfp_url', 500)->nullable()->after('submission_guidelines');
            }
            if (!Schema::hasColumn('yeucauhoithao', 'start_date')) {
                $table->date('start_date')->nullable()->after('keywords');
            }
            if (!Schema::hasColumn('yeucauhoithao', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
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
        Schema::table('yeucauhoithao', function (Blueprint $table) {
            // Chỉ drop các cột mà migration này tạo ra
            if (Schema::hasColumn('yeucauhoithao', 'year')) {
                $table->dropColumn('year');
            }
            if (Schema::hasColumn('yeucauhoithao', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('yeucauhoithao', 'detailed_description')) {
                $table->dropColumn('detailed_description');
            }
            if (Schema::hasColumn('yeucauhoithao', 'submission_guidelines')) {
                $table->dropColumn('submission_guidelines');
            }
            if (Schema::hasColumn('yeucauhoithao', 'cfp_url')) {
                $table->dropColumn('cfp_url');
            }
            if (Schema::hasColumn('yeucauhoithao', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('yeucauhoithao', 'end_date')) {
                $table->dropColumn('end_date');
            }
        });
    }
};
