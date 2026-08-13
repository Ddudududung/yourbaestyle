<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('detail_transaksi_pos')) {
            Schema::create('detail_transaksi_pos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_transaksi');
                $table->unsignedBigInteger('id_produk');
                $table->integer('qty');
                $table->decimal('harga_satuan', 15, 2);
                $table->decimal('hpp_satuan', 15, 2);

                $table->foreign('id_transaksi')->references('id')->on('transaksi_pos')->onDelete('cascade');
                $table->foreign('id_produk')->references('id')->on('produk')->onDelete('restrict');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi_pos');
    }
};
