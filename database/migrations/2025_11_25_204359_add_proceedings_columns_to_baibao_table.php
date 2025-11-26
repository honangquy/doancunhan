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
            $table->integer('start_page')->nullable()->after('decision');
            $table->integer('end_page')->nullable()->after('start_page');
            $table->timestamp('published_at')->nullable()->after('end_page');
            $table->integer('published_by')->nullable()->after('published_at');
            
            // Add index for performance
            $table->index(['conference_id', 'decision']);
            $table->index(['published_at']);
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
            $table->dropIndex(['conference_id', 'decision']);
            $table->dropIndex(['published_at']);
            $table->dropColumn(['start_page', 'end_page', 'published_at', 'published_by']);
        });
    }
};
