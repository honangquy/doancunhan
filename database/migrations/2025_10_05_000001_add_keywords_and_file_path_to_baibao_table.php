<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('BaiBao', function (Blueprint $table) {
            $table->string('keywords', 500)->nullable()->after('abstract');
            $table->string('file_path', 500)->nullable()->after('keywords');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('BaiBao', function (Blueprint $table) {
            $table->dropColumn(['keywords', 'file_path']);
        });
    }
};
