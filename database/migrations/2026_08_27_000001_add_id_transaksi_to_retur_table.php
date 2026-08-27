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
        if (Schema::hasTable('retur')) {
            Schema::table('retur', function (Blueprint $table) {
                if (!Schema::hasColumn('retur', 'id_transaksi')) {
                    $table->unsignedBigInteger('id_transaksi')->nullable()->after('id_pesanan_online')->index();
                    $table->foreign('id_transaksi')->references('id')->on('transaksi_pos')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('retur')) {
            Schema::table('retur', function (Blueprint $table) {
                if (Schema::hasColumn('retur', 'id_transaksi')) {
                    $table->dropForeign(['id_transaksi']);
                    $table->dropColumn('id_transaksi');
                }
            });
        }
    }
};
