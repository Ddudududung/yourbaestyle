<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('produk')) {
            Schema::create('produk', function (Blueprint $table) {
                $table->id();
                $table->string('kode_produk', 20)->unique();
                $table->string('nama_produk', 150);
                $table->enum('jenis', ['thrift', 'rebranding']);
                $table->text('deskripsi')->nullable();
                $table->string('foto', 255)->nullable();
                $table->decimal('harga_jual', 15, 2)->default(0);
                $table->decimal('hpp_otomatis', 15, 2)->default(0);
                $table->decimal('hpp_realisasi', 15, 2)->nullable();
                $table->integer('stok')->default(0);
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
