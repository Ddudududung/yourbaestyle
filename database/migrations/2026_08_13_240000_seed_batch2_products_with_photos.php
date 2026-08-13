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

        $newProducts = [
            [
                'kode_produk'        => 'PRD-RBRN-0011',
                'nama_produk'        => 'Polo Sweater Rajut Salur Vintage - Maroon',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('SWT'),
                'id_warna'           => $getWarna('MRN'),
                'id_model'           => $getModel('SLR'),
                'harga_jual'         => 89000,
                'harga_beli_per_unit'=> 45000,
                'hpp_otomatis'       => 45000,
                'stok'               => 9,
                'id_pemasok'         => 1,
                'foto'               => 'images/produk/photo6.jpg',
                'deskripsi'          => 'Polo sweater rajut motif salur vintage warna maroon dengan kancing dada, gaya retro yang stylish.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0012',
                'nama_produk'        => 'Jaket Rajut Zipper Kerah Salur - Cokelat Tua',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('OUT'),
                'id_warna'           => $getWarna('BRN'),
                'id_model'           => $getModel('SLR'),
                'harga_jual'         => 98000,
                'harga_beli_per_unit'=> 52000,
                'hpp_otomatis'       => 52000,
                'stok'               => 7,
                'id_pemasok'         => 2,
                'foto'               => 'images/produk/photo7.jpg',
                'deskripsi'          => 'Outer jaket rajut warna cokelat tua dengan resleting zipper aktif dan garis salur putih.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0013',
                'nama_produk'        => 'Polo Sweater Rajut Kerah Contrast - Maroon Sky Blue',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('SWT'),
                'id_warna'           => $getWarna('MRN'),
                'id_model'           => $getModel('SLR'),
                'harga_jual'         => 87000,
                'harga_beli_per_unit'=> 45000,
                'hpp_otomatis'       => 45000,
                'stok'               => 11,
                'id_pemasok'         => 1,
                'foto'               => 'images/produk/photo8.jpg',
                'deskripsi'          => 'Polo sweater rajut maroon dengan kerah kontras warna biru muda lembut yang manis.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0014',
                'nama_produk'        => 'Jaket Rajut Zipper Bordir Butterfly - Hitam',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('OUT'),
                'id_warna'           => $getWarna('BLK'),
                'id_model'           => $getModel('SLR'),
                'harga_jual'         => 99000,
                'harga_beli_per_unit'=> 55000,
                'hpp_otomatis'       => 55000,
                'stok'               => 8,
                'id_pemasok'         => 2,
                'foto'               => 'images/produk/photo9.jpg',
                'deskripsi'          => 'Jaket rajut resleting warna hitam salur putih dengan bordir kupu-kupu lucu di dada.',
                'status'             => 'aktif',
            ],
            [
                'kode_produk'        => 'PRD-RBRN-0015',
                'nama_produk'        => 'Cardigan Rajut Salur Bordir Bow - Hijau Army',
                'jenis'              => 'rebranding',
                'id_jenis_pakaian'   => $getJenis('CDG'),
                'id_warna'           => $getWarna('ARM'),
                'id_model'           => $getModel('SLR'),
                'harga_jual'         => 89000,
                'harga_beli_per_unit'=> 48000,
                'hpp_otomatis'       => 48000,
                'stok'               => 10,
                'id_pemasok'         => 1,
                'foto'               => 'images/produk/photo10.jpg',
                'deskripsi'          => 'Cardigan rajut warna hijau army salur krem dengan kancing silver & bordir pita cantik.',
                'status'             => 'aktif',
            ],
        ];

        foreach ($newProducts as $data) {
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
