<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('katalog_live')) {
            Schema::create('katalog_live', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_sesi_live')->nullable();
                $table->date('tanggal_live');
                $table->time('jam_live')->nullable();
                $table->string('kode_live');
                $table->unsignedBigInteger('id_produk');
                $table->decimal('harga_live', 15, 2)->nullable();
                $table->integer('stok_alokasi')->default(0);
                $table->timestamps();

                $table->foreign('id_produk')->references('id')->on('produk')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('katalog_live');
    }
};