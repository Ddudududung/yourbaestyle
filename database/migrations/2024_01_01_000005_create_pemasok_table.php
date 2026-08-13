<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pemasok')) {
            Schema::create('pemasok', function (Blueprint $table) {
                $table->id();
                $table->string('nama_pemasok', 150);
                $table->string('kontak', 20)->nullable();
                $table->text('alamat')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasok');
    }
};
