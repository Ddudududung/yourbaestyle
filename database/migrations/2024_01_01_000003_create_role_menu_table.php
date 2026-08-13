<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('role_menu')) {
            Schema::create('role_menu', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_role');
                $table->unsignedBigInteger('id_menu');
                $table->timestamps();

                $table->unique(['id_role', 'id_menu']);
                $table->foreign('id_role')->references('id')->on('ms_role')->onDelete('cascade');
                $table->foreign('id_menu')->references('id')->on('ms_menu')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_menu');
    }
};
