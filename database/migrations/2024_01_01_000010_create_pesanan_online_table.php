<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan_online', function (Blueprint $table) {
            $table->id();
            $table->string('no_pesanan', 50)->unique();
            $table->unsignedBigInteger('id_user');
            $table->enum('platform', ['shopee', 'tiktok']);
            $table->enum('format_csv', ['shopee_standard', 'shopee_hemat_kargo', 'tiktok']);
            $table->string('no_resi', 50)->nullable();
            $table->string('nama_pembeli', 100);
            $table->dateTime('tanggal');
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->decimal('total_hpp', 15, 2)->default(0);
            $table->decimal('ongkir', 15, 2)->default(0);
            $table->enum('status', ['diproses', 'dikirim', 'selesai'])->default('diproses');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('ms_user')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_online');
    }
};
