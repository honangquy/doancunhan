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
            // Change score columns from boolean to integer
            $table->integer('score_novelty')->nullable()->change();
            $table->integer('score_relevance')->nullable()->change();
            $table->integer('score_technical_quality')->nullable()->change();
            $table->integer('score_presentation')->nullable()->change();
            $table->integer('score_references')->nullable()->change();
            
            // Also rename review_file column to match our form
            $table->renameColumn('review_file', 'review_file_path');
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
            // Revert back to boolean
            $table->boolean('score_novelty')->nullable()->change();
            $table->boolean('score_relevance')->nullable()->change();
            $table->boolean('score_technical_quality')->nullable()->change();
            $table->boolean('score_presentation')->nullable()->change();
            $table->boolean('score_references')->nullable()->change();
            
            $table->renameColumn('review_file_path', 'review_file');
        });
    }
};
