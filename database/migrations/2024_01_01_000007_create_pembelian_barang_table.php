<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pembelian_barang')) {
            Schema::create('pembelian_barang', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_produk');
                $table->unsignedBigInteger('id_pemasok');
                $table->date('tanggal')->nullable();
                $table->date('tanggal_pembelian')->nullable();
                $table->integer('jumlah')->default(0);
                $table->integer('qty')->default(0);
                $table->decimal('harga_beli_per_unit', 15, 2)->default(0);
                $table->decimal('total_modal', 15, 2)->default(0);
                $table->string('keterangan', 255)->nullable();
                $table->string('status', 50)->nullable();
                $table->timestamps();

                $table->foreign('id_produk')->references('id')->on('produk')->onDelete('restrict');
                $table->foreign('id_pemasok')->references('id')->on('pemasok')->onDelete('restrict');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian_barang');
    }
};
