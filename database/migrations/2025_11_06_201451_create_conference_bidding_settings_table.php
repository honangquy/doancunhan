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
        Schema::create('conference_bidding_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained('hoithao', 'conference_id')->onDelete('cascade');
            $table->boolean('enable_keyword_matching')->default(false); // false = show all papers, true = only matching keywords
            $table->decimal('keyword_similarity_threshold', 3, 2)->default(0.5); // 0.0 to 1.0 for fuzzy matching
            $table->boolean('allow_partial_keyword_match')->default(true); // allow partial matches
            $table->text('excluded_keywords')->nullable(); // keywords to exclude from matching (comma-separated)
            $table->timestamps();
            
            $table->unique('conference_id'); // One setting per conference
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conference_bidding_settings');
    }
};
