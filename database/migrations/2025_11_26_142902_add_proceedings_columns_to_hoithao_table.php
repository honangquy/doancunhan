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
            // Chỉ thêm cột nếu chưa tồn tại
            if (!Schema::hasColumn('hoithao', 'proceedings_file')) {
                $table->string('proceedings_file', 255)->nullable()->comment('Đường dẫn file kỷ yếu PDF');
            }
            if (!Schema::hasColumn('hoithao', 'proceedings_published_at')) {
                $table->datetime('proceedings_published_at')->nullable()->comment('Thời gian xuất bản kỷ yếu');
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
            if (Schema::hasColumn('hoithao', 'proceedings_file')) {
                $table->dropColumn('proceedings_file');
            }
            if (Schema::hasColumn('hoithao', 'proceedings_published_at')) {
                $table->dropColumn('proceedings_published_at');
            }
        });
    }
};
