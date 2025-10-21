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
            // Thêm cột conference_id để liên kết với bảng hoithao
            if (!Schema::hasColumn('yeucauhoithao', 'conference_id')) {
                $table->unsignedBigInteger('conference_id')->nullable()->after('status')
                    ->comment('ID của hội thảo được tạo từ yêu cầu này');
                
                // Tạo foreign key constraint
                $table->foreign('conference_id')->references('conference_id')->on('hoithao')
                    ->onDelete('set null');
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
            // Xóa foreign key constraint trước
            if (Schema::hasColumn('yeucauhoithao', 'conference_id')) {
                $table->dropForeign(['conference_id']);
                $table->dropColumn('conference_id');
            }
        });
    }
};
