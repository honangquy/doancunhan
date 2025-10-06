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
        // TrangThaiBaiBao
        Schema::create('TrangThaiBaiBao', function (Blueprint $table) {
            $table->string('status_code', 30)->primary();
            $table->string('status_name', 100);
        });

        // GiaTriBidding
        Schema::create('GiaTriBidding', function (Blueprint $table) {
            $table->string('bidding_code', 20)->primary();
            $table->string('bidding_name', 50);
            $table->integer('score');
        });

        // LoaiCOI
        Schema::create('LoaiCOI', function (Blueprint $table) {
            $table->string('coi_code', 30)->primary();
            $table->string('coi_name', 100);
        });

        // LoaiVaiTro
        Schema::create('LoaiVaiTro', function (Blueprint $table) {
            $table->string('role_code', 20)->primary();
            $table->string('role_name', 100);
        });

        // CapHoiThao
        Schema::create('CapHoiThao', function (Blueprint $table) {
            $table->string('level_code', 20)->primary();
            $table->string('level_name', 50);
        });

        // TrangThaiPhanCong
        Schema::create('TrangThaiPhanCong', function (Blueprint $table) {
            $table->string('status_code', 20)->primary();
        });

        // LoaiKhuyenNghi
        Schema::create('LoaiKhuyenNghi', function (Blueprint $table) {
            $table->string('recommendation_code', 20)->primary();
            $table->string('recommendation_name', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('LoaiKhuyenNghi');
        Schema::dropIfExists('TrangThaiPhanCong');
        Schema::dropIfExists('CapHoiThao');
        Schema::dropIfExists('LoaiVaiTro');
        Schema::dropIfExists('LoaiCOI');
        Schema::dropIfExists('GiaTriBidding');
        Schema::dropIfExists('TrangThaiBaiBao');
    }
};
