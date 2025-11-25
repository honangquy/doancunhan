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
        Schema::create('external_authors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paper_id');
            $table->string('name');
            $table->string('email');
            $table->string('organization')->nullable();
            $table->integer('author_order');
            $table->boolean('is_contact')->default(0);
            $table->timestamps();

            $table->foreign('paper_id')->references('paper_id')->on('baibao')->onDelete('cascade');
            $table->index(['paper_id', 'author_order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('external_authors');
    }
};
