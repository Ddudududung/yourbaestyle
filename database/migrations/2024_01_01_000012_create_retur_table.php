<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('retur')) {
            Schema::create('retur', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_pesanan_online')->nullable();
                $table->unsignedBigInteger('id_produk');
                $table->unsignedBigInteger('id_user')->nullable();
                $table->date('tanggal');
                $table->text('alasan');
                $table->enum('kondisi_barang', ['layak_jual', 'tidak_layak']);
                $table->integer('qty')->default(1);
                $table->decimal('ongkir_retur', 15, 2)->default(0);
                $table->decimal('nilai_kerugian', 15, 2)->default(0);
                $table->timestamps();

                $table->foreign('id_pesanan_online')->references('id')->on('pesanan_online')->onDelete('restrict');
                $table->foreign('id_produk')->references('id')->on('produk')->onDelete('restrict');
                $table->foreign('id_user')->references('id')->on('ms_user')->onDelete('restrict');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('retur');
    }
};
