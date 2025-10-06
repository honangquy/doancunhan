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
        // NguoiDung table
        Schema::create('NguoiDung', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('email', 255)->unique();
            $table->string('password_hash', 255);
            $table->string('full_name', 200);
            $table->boolean('is_student')->default(false);
            $table->unsignedBigInteger('faculty_id')->nullable();
            $table->string('organization', 255)->nullable();
            $table->boolean('locked')->default(false);
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('faculty_id')->references('faculty_id')->on('Khoa')
                ->onDelete('set null')->onUpdate('cascade');
        });

        // VaiTroNguoiDung table
        Schema::create('VaiTroNguoiDung', function (Blueprint $table) {
            $table->id('user_role_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role_code', 20);
            $table->unsignedBigInteger('conference_id')->nullable();
            
            $table->unique(['user_id', 'role_code', 'conference_id'], 'uq_user_role');
            
            $table->foreign('user_id')->references('user_id')->on('NguoiDung')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('role_code')->references('role_code')->on('LoaiVaiTro')
                ->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('VaiTroNguoiDung');
        Schema::dropIfExists('NguoiDung');
    }
};
