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
        Schema::create('tr_pemesanan_penjualan_header', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ms_cabang_id');
            $table->foreign('ms_cabang_id')->references('id')->on('ms_cabang')->onDelete('cascade')->onUpdate('cascade');

            $table->uuid('ms_pelanggan_id');
            $table->foreign('ms_pelanggan_id')->references('id')->on('ms_pelanggan')->onDelete('cascade')->onUpdate('cascade');

            $table->string('nama', 255);
            $table->text('memo')->nullable();

            $table->string('dibuat_oleh', 255)->nullable()->index();
            $table->string('diupdate_oleh', 255)->nullable()->index();
            $table->timestamp('tgl_dibuat');
            $table->timestamp('tgl_diupdate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_tanda_terima_service_header');
    }
};
