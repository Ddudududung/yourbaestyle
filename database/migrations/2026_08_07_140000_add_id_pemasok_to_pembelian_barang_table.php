<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pembelian_barang')) {
            Schema::table('pembelian_barang', function (Blueprint $table) {
                if (!Schema::hasColumn('pembelian_barang', 'id_pemasok')) {
                    $table->unsignedBigInteger('id_pemasok')->nullable()->after('id_produk');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pembelian_barang') && Schema::hasColumn('pembelian_barang', 'id_pemasok')) {
            Schema::table('pembelian_barang', function (Blueprint $table) {
                $table->dropColumn('id_pemasok');
            });
        }
    }
};
