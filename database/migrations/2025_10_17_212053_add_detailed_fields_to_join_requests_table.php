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
            // Thông tin cá nhân chung
            $table->string('full_name')->nullable()->comment('Họ và tên');
            $table->string('email_contact')->nullable()->comment('Email liên hệ');
            $table->string('country')->nullable()->comment('Quốc gia');
            $table->string('organization')->nullable()->comment('Đơn vị công tác');
            $table->string('department')->nullable()->comment('Khoa/Phòng ban');
            $table->string('phone')->nullable()->comment('Số điện thoại');
            $table->text('notes')->nullable()->comment('Ghi chú');
            
            // Dành cho tác giả (AUTHOR)
            $table->string('field_of_study')->nullable()->comment('Lĩnh vực nghiên cứu');
            $table->string('academic_title')->nullable()->comment('Chức danh/Học vị');
            
            // Dành cho reviewer (REVIEWER) 
            $table->text('expertise_keywords')->nullable()->comment('Từ khóa chuyên môn');
            $table->integer('max_papers')->nullable()->comment('Số bài tối đa có thể nhận');
            
            // Commitment flag
            $table->boolean('commitment_confirmed')->default(false)->comment('Xác nhận cam kết');
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
            $table->dropColumn([
                'full_name',
                'email_contact', 
                'country',
                'organization',
                'department',
                'phone',
                'notes',
                'field_of_study',
                'academic_title',
                'expertise_keywords',
                'max_papers',
                'commitment_confirmed'
            ]);
        });
    }
};
