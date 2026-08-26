<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('retur')) {
            Schema::table('retur', function (Blueprint $table) {
                if (!Schema::hasColumn('retur', 'tipe_retur')) {
                    $table->string('tipe_retur', 30)->default('tukar_barang')->after('kondisi_barang');
                }
                if (!Schema::hasColumn('retur', 'id_produk_pengganti')) {
                    $table->integer('id_produk_pengganti')->nullable()->index()->after('tipe_retur');
                }
                if (!Schema::hasColumn('retur', 'qty_pengganti')) {
                    $table->integer('qty_pengganti')->default(1)->after('id_produk_pengganti');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('retur')) {
            Schema::table('retur', function (Blueprint $table) {
                if (Schema::hasColumn('retur', 'id_produk_pengganti')) {
                    $table->dropColumn('id_produk_pengganti');
                }
                if (Schema::hasColumn('retur', 'tipe_retur')) {
                    $table->dropColumn('tipe_retur');
                }
                if (Schema::hasColumn('retur', 'qty_pengganti')) {
                    $table->dropColumn('qty_pengganti');
                }
            });
        }
    }
};
