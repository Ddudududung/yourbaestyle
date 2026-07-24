<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('katalog_lives', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_live'); // Sesuai kata Nizar: Relasi berdasarkan tanggal
            $table->string('kode_live');  // Contoh: "102", "YB 39"
            $table->unsignedBigInteger('id_produk'); // Relasi ke tabel produk asli
            $table->integer('stok_alokasi')->default(0); // Contoh: (7), (60)
            $table->timestamps();

            $table->foreign('id_produk')->references('id')->on('produk')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('katalog_lives');
    }
};