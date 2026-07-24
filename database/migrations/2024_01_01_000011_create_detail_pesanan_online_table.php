<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pesanan_online', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pesanan');
            $table->unsignedBigInteger('id_produk');
            $table->string('variasi', 100)->nullable();
            $table->integer('qty');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('hpp_satuan', 15, 2);

            $table->foreign('id_pesanan')->references('id')->on('pesanan_online')->onDelete('cascade');
            $table->foreign('id_produk')->references('id')->on('produk')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan_online');
    }
};
