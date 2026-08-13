<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transaksi_pos')) {
            Schema::create('transaksi_pos', function (Blueprint $table) {
                $table->id();
                $table->string('kode_transaksi', 30)->unique();
                $table->unsignedBigInteger('id_user');
                $table->dateTime('tanggal');
                $table->decimal('total_harga', 15, 2)->default(0);
                $table->decimal('total_hpp', 15, 2)->default(0);
                $table->decimal('diskon', 15, 2)->default(0);
                $table->enum('metode_bayar', ['tunai', 'transfer', 'qris']);
                $table->timestamps();

                $table->foreign('id_user')->references('id')->on('ms_user')->onDelete('restrict');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_pos');
    }
};
