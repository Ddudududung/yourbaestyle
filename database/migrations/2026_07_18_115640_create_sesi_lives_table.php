<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('sesi_live')) {
            Schema::create('sesi_live', function (Blueprint $table) {
                $table->id();
                $table->string('platform', 50)->default('shopee');
                $table->string('nama_sesi', 100);
                $table->date('tanggal_live');
                $table->time('jam_mulai')->nullable();
                $table->time('jam_selesai')->nullable();
                $table->enum('status', ['upcoming', 'ongoing', 'selesai'])->default('upcoming');
                $table->integer('total_pesanan')->default(0);
                $table->decimal('total_revenue', 15, 2)->default(0);
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_live');
    }
};
