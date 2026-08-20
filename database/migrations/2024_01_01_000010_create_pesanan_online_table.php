<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pesanan_online')) {
            Schema::create('pesanan_online', function (Blueprint $table) {
                $table->id();
                $table->string('no_pesanan', 50)->unique();
                $table->unsignedBigInteger('id_user')->nullable();
                $table->string('platform', 50)->default('shopee');
                $table->string('format_csv', 50)->nullable();
                $table->string('no_resi', 50)->nullable();
                $table->string('nama_pembeli', 100)->nullable();
                $table->dateTime('tanggal')->nullable();
                $table->decimal('total_harga', 15, 2)->default(0);
                $table->decimal('total_hpp', 15, 2)->default(0);
                $table->decimal('ongkir', 15, 2)->default(0);
                $table->string('status', 50)->default('diproses');
                $table->string('status_mapping', 50)->nullable();
                $table->text('pesan_mapping')->nullable();
                $table->timestamps();

                $table->foreign('id_user')->references('id')->on('ms_user')->onDelete('restrict');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_online');
    }
};
