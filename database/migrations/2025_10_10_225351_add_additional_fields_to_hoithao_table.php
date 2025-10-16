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
            // Add description field first
            $table->text('description')->nullable()->after('title')->comment('Mô tả ngắn về hội thảo');
            
            // CFP & submission fields
            $table->string('cfp_url', 500)->nullable()->after('description')->comment('URL của file Call for Papers (PDF)');
            $table->text('submission_guidelines')->nullable()->after('cfp_url')->comment('Hướng dẫn nộp bài chi tiết');
            $table->text('detailed_description')->nullable()->after('submission_guidelines')->comment('Mô tả chi tiết về hội thảo');
            
            // Location & contact fields  
            $table->string('location', 255)->nullable()->after('detailed_description')->comment('Địa điểm tổ chức hội thảo');
            $table->string('contact_email', 100)->nullable()->after('location')->comment('Email liên hệ');
            $table->string('contact_phone', 20)->nullable()->after('contact_email')->comment('Số điện thoại liên hệ');
            
            // Chair information
            $table->string('chair_name', 100)->nullable()->after('contact_phone')->comment('Tên chủ tịch hội thảo');
            $table->string('chair_email', 100)->nullable()->after('chair_name')->comment('Email chủ tịch');
            
            // Additional metadata
                        // Additional metadata
            $table->text('keywords')->nullable()->after('chair_email')->comment('Từ khóa, chủ đề (phân cách bởi dấu phẩy)');
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
                        $table->dropColumn([
                'cfp_url',
                'submission_guidelines', 
                'detailed_description',
                'location',
                'contact_email',
                'contact_phone',
                'chair_name',
                'chair_email',
                'keywords'
            ]);
        });
    }
};
