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
        Schema::table('phanbien', function (Blueprint $table) {
            // Detailed scoring criteria (1-10 scale)
            $table->tinyInteger('score_novelty')->nullable()->comment('Tính mới của đề tài (1-10)');
            $table->tinyInteger('score_relevance')->nullable()->comment('Tính phù hợp với chủ đề hội thảo (1-10)');
            $table->tinyInteger('score_technical_quality')->nullable()->comment('Chất lượng kỹ thuật/độ tin cậy (1-10)');
            $table->tinyInteger('score_presentation')->nullable()->comment('Cách trình bày (1-10)');
            $table->tinyInteger('score_references')->nullable()->comment('Tài liệu tham khảo (1-10)');
            
            // Calculated average score
            $table->decimal('total_score', 3, 1)->nullable()->comment('Tổng điểm trung bình');
            
            // Detailed comments
            $table->text('detailed_comments')->nullable()->comment('Nhận xét chi tiết từ reviewer');
            
            // Additional review file
            $table->string('review_file')->nullable()->comment('File phản biện bổ sung (Word/PDF)');
            
            // Draft status
            $table->boolean('is_draft')->default(true)->comment('Trạng thái bản nháp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('phanbien', function (Blueprint $table) {
            $table->dropColumn([
                'score_novelty',
                'score_relevance', 
                'score_technical_quality',
                'score_presentation',
                'score_references',
                'total_score',
                'detailed_comments',
                'review_file',
                'is_draft'
            ]);
        });
    }
};
