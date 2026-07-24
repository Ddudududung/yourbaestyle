<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_menu', function (Blueprint $table) {
            $table->id();
            $table->string('nama_menu', 150);
            $table->string('target_url', 255);
            $table->string('icon_url', 255)->nullable();
            $table->integer('order_menu')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_menu');
    }
};
