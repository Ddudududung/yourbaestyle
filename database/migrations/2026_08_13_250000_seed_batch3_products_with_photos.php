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

        $batch3Products = [
            [
                'kode_produk'        => 'PRD-RBRN-0016',
                'nama_produk'        => 'Cardigan Rajut Motif Ribbon Bow - Cokelat Tua',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('CDG'),
                'id_warna'           => $getWarna('BRN'),
                'id_model'           => $getModel('PLS'),
                'harga_jual'         => 92000,
                'harga_beli_per_unit'=> 49000,
                'hpp_otomatis'       => 49000,
                'stok'               => 8,
                'id_pemasok'         => 1,
                'foto'               => 'images/produk/photo11.jpg',
                'deskripsi'          => 'Cardigan rajut warna cokelat tua manis dengan motif rajut simpul pita / ribbon bow.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0017',
                'nama_produk'        => 'Cardigan Rajut Motif Heart Love Outline - Cokelat Tua',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('CDG'),
                'id_warna'           => $getWarna('BRN'),
                'id_model'           => $getModel('LVE'),
                'harga_jual'         => 92000,
                'harga_beli_per_unit'=> 49000,
                'hpp_otomatis'       => 49000,
                'stok'               => 7,
                'id_pemasok'         => 1,
                'foto'               => 'images/produk/photo11.jpg',
                'deskripsi'          => 'Cardigan rajut V-neck cokelat tua dengan motif garis berbentuk hati / heart outline putih.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0018',
                'nama_produk'        => 'Cardigan Rajut Kerah Sailor Contrast - Maroon',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('CDG'),
                'id_warna'           => $getWarna('MRN'),
                'id_model'           => $getModel('PLS'),
                'harga_jual'         => 95000,
                'harga_beli_per_unit'=> 50000,
                'hpp_otomatis'       => 50000,
                'stok'               => 10,
                'id_pemasok'         => 2,
                'foto'               => 'images/produk/photo12.jpg',
                'deskripsi'          => 'Cardigan rajut warna maroon dengan kerah lebar gaya Sailor warna putih kontras dan kancing silver.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0019',
                'nama_produk'        => 'Cardigan Rajut Motif Daun Leaf Vintage - Krem Mocca',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('CDG'),
                'id_warna'           => $getWarna('CRM'),
                'id_model'           => $getModel('FLR'),
                'harga_jual'         => 89000,
                'harga_beli_per_unit'=> 46000,
                'hpp_otomatis'       => 46000,
                'stok'               => 9,
                'id_pemasok'         => 1,
                'foto'               => 'images/produk/photo13.jpg',
                'deskripsi'          => 'Cardigan rajut warna krem lembut dengan motif tangkai daun cokelat mocca berkesan vintage aesthetic.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0020',
                'nama_produk'        => 'Polo Sweater Rajut Salur Pastel - Kuning Biru',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('SWT'),
                'id_warna'           => $getWarna('MST'),
                'id_model'           => $getModel('SLR'),
                'harga_jual'         => 85000,
                'harga_beli_per_unit'=> 44000,
                'hpp_otomatis'       => 44000,
                'stok'               => 12,
                'id_pemasok'         => 2,
                'foto'               => 'images/produk/photo14.jpg',
                'deskripsi'          => 'Polo sweater rajut warna kuning pastel cerah dengan kombinasi garis salur biru muda.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0021',
                'nama_produk'        => 'Cardigan Kerah Contrast Polkadot - Dusty Pink Maroon',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('CDG'),
                'id_warna'           => $getWarna('PNK'),
                'id_model'           => $getModel('SLR'),
                'harga_jual'         => 89000,
                'harga_beli_per_unit'=> 47000,
                'hpp_otomatis'       => 47000,
                'stok'               => 8,
                'id_pemasok'         => 1,
                'foto'               => 'images/produk/photo15.jpg',
                'deskripsi'          => 'Cardigan rajut warna dusty pink manis dengan kerah kontras maroon dan bintik polkadot.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0022',
                'nama_produk'        => 'Cardigan Kerah Contrast Polkadot - Putih BW Soft Pink',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('CDG'),
                'id_warna'           => $getWarna('BW'),
                'id_model'           => $getModel('SLR'),
                'harga_jual'         => 89000,
                'harga_beli_per_unit'=> 47000,
                'hpp_otomatis'       => 47000,
                'stok'               => 10,
                'id_pemasok'         => 1,
                'foto'               => 'images/produk/photo15.jpg',
                'deskripsi'          => 'Cardigan rajut warna putih broken white dengan kerah kontras pink muda dan bintik polkadot.',
                'status'             => 'aktif',
            ],
        ];

        foreach ($batch3Products as $data) {
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
