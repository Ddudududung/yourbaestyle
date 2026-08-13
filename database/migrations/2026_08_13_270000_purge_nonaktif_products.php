<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $nonaktifIds = DB::table('produk')->where('status', 'nonaktif')->pluck('id');

        foreach ($nonaktifIds as $id) {
            DB::table('pembelian_barang')->where('id_produk', $id)->delete();
            DB::table('detail_transaksi_pos')->where('id_produk', $id)->delete();
            DB::table('detail_pesanan_online')->where('id_produk', $id)->delete();
            DB::table('retur')->where('id_produk', $id)->delete();
            if (Schema::hasTable('katalog_live')) {
                DB::table('katalog_live')->where('id_produk', $id)->delete();
            }
            DB::table('produk')->where('id', $id)->delete();
        }
    }

    public function down(): void
    {
    }
};
