<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $getJenis = fn($kode) => DB::table('ms_jenis_pakaian')->where('kode', $kode)->value('id') ?? 2;
        $getWarna = fn($kode) => DB::table('ms_warna')->where('kode', $kode)->value('id') ?? 21;
        $getModel = fn($kode) => DB::table('ms_model')->where('kode', $kode)->value('id') ?? 1;

        $products = [
            [
                'kode_produk'        => 'PRD-RBRN-0001',
                'nama_produk'        => 'Polo Sweater Rajut Button - Krem Soft',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('SWT'),
                'id_warna'           => $getWarna('CRM'),
                'id_model'           => $getModel('PLS'),
                'harga_jual'         => 85000,
                'harga_beli_per_unit'=> 45000,
                'hpp_otomatis'       => 45000,
                'stok'               => 12,
                'id_pemasok'         => 1,
                'foto'               => 'images/produk/photo1.jpg',
                'deskripsi'          => 'Sweater rajut wanita model kerah polo dengan kancing aktif. Bahan rajut lembut, adem, dan menyerap keringat.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0002',
                'nama_produk'        => 'Polo Sweater Rajut Button - Maroon',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('SWT'),
                'id_warna'           => $getWarna('MRN'),
                'id_model'           => $getModel('PLS'),
                'harga_jual'         => 85000,
                'harga_beli_per_unit'=> 45000,
                'hpp_otomatis'       => 45000,
                'stok'               => 10,
                'id_pemasok'         => 1,
                'foto'               => 'images/produk/photo1.jpg',
                'deskripsi'          => 'Sweater rajut berkerah warna maroon elegan dengan kancing dada.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0003',
                'nama_produk'        => 'Polo Sweater Rajut Button - Sage Green',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('SWT'),
                'id_warna'           => $getWarna('SGV'),
                'id_model'           => $getModel('PLS'),
                'harga_jual'         => 85000,
                'harga_beli_per_unit'=> 45000,
                'hpp_otomatis'       => 45000,
                'stok'               => 8,
                'id_pemasok'         => 1,
                'foto'               => 'images/produk/photo1.jpg',
                'deskripsi'          => 'Sweater rajut warna sage green kekinian dengan gaya polo casual.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0004',
                'nama_produk'        => 'Cardigan Rajut Polkadot CC - Abu Misty',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('CDG'),
                'id_warna'           => $getWarna('GRY'),
                'id_model'           => $getModel('SLR'),
                'harga_jual'         => 95000,
                'harga_beli_per_unit'=> 50000,
                'hpp_otomatis'       => 50000,
                'stok'               => 6,
                'id_pemasok'         => 2,
                'foto'               => 'images/produk/photo2.jpg',
                'deskripsi'          => 'Cardigan rajut kancing hitam besar dengan motif polkadot bintik hitam vintage.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0005',
                'nama_produk'        => 'Cardigan Kerah Contrast Polkadot - Hijau Army',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('CDG'),
                'id_warna'           => $getWarna('ARM'),
                'id_model'           => $getModel('SLR'),
                'harga_jual'         => 89000,
                'harga_beli_per_unit'=> 48000,
                'hpp_otomatis'       => 48000,
                'stok'               => 7,
                'id_pemasok'         => 1,
                'foto'               => 'images/produk/photo3.jpg',
                'deskripsi'          => 'Cardigan rajut warna hijau army dengan kerah kontras cokelat dan bintik manis.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0006',
                'nama_produk'        => 'Cardigan Kerah Contrast Polkadot - Krem Soft Pink',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('CDG'),
                'id_warna'           => $getWarna('CRM'),
                'id_model'           => $getModel('SLR'),
                'harga_jual'         => 89000,
                'harga_beli_per_unit'=> 48000,
                'hpp_otomatis'       => 48000,
                'stok'               => 9,
                'id_pemasok'         => 1,
                'foto'               => 'images/produk/photo3.jpg',
                'deskripsi'          => 'Cardigan rajut warna krem kombinasi kerah dan bintik pink feminin.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0007',
                'nama_produk'        => 'Cardigan Rajut Bordir Bunga - Maroon',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('CDG'),
                'id_warna'           => $getWarna('MRN'),
                'id_model'           => $getModel('FLR'),
                'harga_jual'         => 79000,
                'harga_beli_per_unit'=> 42000,
                'hpp_otomatis'       => 42000,
                'stok'               => 15,
                'id_pemasok'         => 2,
                'foto'               => 'images/produk/photo4.jpg',
                'deskripsi'          => 'Cardigan rajut minimalis warna maroon dengan aksen bordir bunga cantik di dada.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0008',
                'nama_produk'        => 'Cardigan Rajut Bordir Bunga - Soft Pink',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('CDG'),
                'id_warna'           => $getWarna('PNK'),
                'id_model'           => $getModel('FLR'),
                'harga_jual'         => 79000,
                'harga_beli_per_unit'=> 42000,
                'hpp_otomatis'       => 42000,
                'stok'               => 12,
                'id_pemasok'         => 2,
                'foto'               => 'images/produk/photo4.jpg',
                'deskripsi'          => 'Cardigan rajut warna pink lembut dengan aksen bordir bunga di dada.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0009',
                'nama_produk'        => 'Polo Sweater Rajut Premium - Cokelat Tua',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('SWT'),
                'id_warna'           => $getWarna('BRN'),
                'id_model'           => $getModel('PLS'),
                'harga_jual'         => 85000,
                'harga_beli_per_unit'=> 45000,
                'hpp_otomatis'       => 45000,
                'stok'               => 10,
                'id_pemasok'         => 1,
                'foto'               => 'images/produk/photo5.jpg',
                'deskripsi'          => 'Polo sweater rajut warna cokelat tua earth tone dengan bahan tebal & hangat.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0010',
                'nama_produk'        => 'Polo Sweater Rajut Premium - Navy',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('SWT'),
                'id_warna'           => $getWarna('NVY'),
                'id_model'           => $getModel('PLS'),
                'harga_jual'         => 85000,
                'harga_beli_per_unit'=> 45000,
                'hpp_otomatis'       => 45000,
                'stok'               => 11,
                'id_pemasok'         => 1,
                'foto'               => 'images/produk/photo5.jpg',
                'deskripsi'          => 'Polo sweater rajut warna navy deep blue yang stylish untuk daily wear.',
                'status'             => 'aktif',
            ],
        ];

        foreach ($products as $data) {
            $existing = DB::table('produk')->where('nama_produk', $data['nama_produk'])->first();
            if ($existing) {
                DB::table('produk')->where('id', $existing->id)->update(array_merge($data, ['updated_at' => now()]));
            } else {
                DB::table('produk')->insert(array_merge($data, ['created_at' => now(), 'updated_at' => now()]));
            }
        }
    }

    public function down(): void
    {
    }
};
