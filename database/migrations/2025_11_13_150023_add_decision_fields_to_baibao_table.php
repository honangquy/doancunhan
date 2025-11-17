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
        Schema::table('baibao', function (Blueprint $table) {
            // Chair decision fields
            $table->enum('decision', ['ACCEPT', 'REJECT', 'REVISE'])->nullable()->after('status_code');
            $table->text('decision_comments')->nullable()->after('decision');
            $table->datetime('decision_date')->nullable()->after('decision_comments');
            $table->unsignedBigInteger('decision_by')->nullable()->after('decision_date');
            $table->date('revision_deadline')->nullable()->after('decision_by');
            
            // Foreign key for decision maker
            $table->foreign('decision_by')->references('user_id')->on('nguoidung')->onDelete('set null');
            
            // Indexes
            $table->index('decision');
            $table->index('decision_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('baibao', function (Blueprint $table) {
            $table->dropForeign(['decision_by']);
            $table->dropIndex(['decision']);
            $table->dropIndex(['decision_date']);
            $table->dropColumn([
                'decision',
                'decision_comments', 
                'decision_date',
                'decision_by',
                'revision_deadline'
            ]);
        });
    }
};
