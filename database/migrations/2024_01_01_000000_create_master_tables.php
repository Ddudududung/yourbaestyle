<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ms_jenis_pakaian')) {
            Schema::create('ms_jenis_pakaian', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 20)->nullable();
                $table->string('nama', 100);
                $table->string('nama_jenis', 100)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ms_warna')) {
            Schema::create('ms_warna', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 20)->nullable();
                $table->string('nama', 100);
                $table->string('nama_warna', 100)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ms_model')) {
            Schema::create('ms_model', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 20)->nullable();
                $table->string('nama', 100);
                $table->string('nama_model', 100)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('review_mapping_manual')) {
            Schema::create('review_mapping_manual', function (Blueprint $table) {
                $table->id();
                $table->string('no_pesanan', 100);
                $table->string('kode_live_raw', 100)->nullable();
                $table->date('tanggal_live')->nullable();
                $table->integer('qty_order')->default(1);
                $table->enum('status_resolusi', ['pending', 'mapped', 'skip', 'reject'])->default('pending');
                $table->unsignedBigInteger('id_produk_mapping')->nullable();
                $table->unsignedBigInteger('id_user_resolver')->nullable();
                $table->timestamp('tanggal_resolusi')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('review_mapping_manual');
        Schema::dropIfExists('ms_model');
        Schema::dropIfExists('ms_warna');
        Schema::dropIfExists('ms_jenis_pakaian');
    }
};
