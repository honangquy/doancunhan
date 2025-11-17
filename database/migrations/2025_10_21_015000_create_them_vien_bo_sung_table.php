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
        // ThemVienBoSung - Co-chairs for conference requests
        if (!Schema::hasTable('themvienbosungng')) {
            Schema::create('themvienbosungng', function (Blueprint $table) {
                $table->id('co_chair_id');
                $table->unsignedBigInteger('request_id')->comment('ID của yêu cầu tạo hội thảo');
                $table->string('fullname', 255)->comment('Tên đầy đủ của co-chair');
                $table->string('email', 255)->comment('Email của co-chair');
                $table->string('affiliation', 255)->nullable()->comment('Cơ quan/tổ chức của co-chair');
                $table->timestamp('created_at')->useCurrent();
                
                $table->foreign('request_id')->references('request_id')->on('yeucauhoithao')
                    ->onDelete('cascade')->onUpdate('cascade');
                
                $table->index('request_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('themvienbosungng');
    }
};
