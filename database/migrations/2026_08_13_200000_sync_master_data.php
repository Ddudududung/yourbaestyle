<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. JENIS PAKAIAN
        $jenisData = [
            1 => ['kode' => 'CDG', 'nama' => 'Cardigan'],
            2 => ['kode' => 'SWT', 'nama' => 'Sweater'],
            3 => ['kode' => 'VST', 'nama' => 'Vest / Rompi'],
            4 => ['kode' => 'OUT', 'nama' => 'Outer / Jaket'],
        ];

        foreach ($jenisData as $id => $d) {
            DB::table('ms_jenis_pakaian')->where('id', $id)->update([
                'kode'       => $d['kode'],
                'nama'       => $d['nama'],
                'nama_jenis' => $d['nama'],
                'updated_at' => now(),
            ]);
        }

        $newJenis = [
            ['kode' => 'BLS', 'nama' => 'Blouse'],
            ['kode' => 'TRT', 'nama' => 'Turtleneck / Hoodie'],
        ];
        foreach ($newJenis as $nj) {
            $exists = DB::table('ms_jenis_pakaian')->where('nama', $nj['nama'])->orWhere('nama_jenis', $nj['nama'])->exists();
            if (!$exists) {
                DB::table('ms_jenis_pakaian')->insert([
                    'kode'       => $nj['kode'],
                    'nama'       => $nj['nama'],
                    'nama_jenis' => $nj['nama'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 2. MODEL / MOTIF
        $modelData = [
            1 => ['kode' => 'PLS', 'nama' => 'Polos'],
            2 => ['kode' => 'SLR', 'nama' => 'Salur / Garis-Garis'],
            3 => ['kode' => 'FLR', 'nama' => 'Bunga / Floral'],
        ];
        foreach ($modelData as $id => $d) {
            DB::table('ms_model')->where('id', $id)->update([
                'kode'       => $d['kode'],
                'nama'       => $d['nama'],
                'nama_model' => $d['nama'],
                'updated_at' => now(),
            ]);
        }

        $newModels = [
            ['kode' => 'LVE', 'nama' => 'Motif Love / Heart'],
            ['kode' => 'ARG', 'nama' => 'Belah Ketupat / Argyle'],
            ['kode' => 'CRP', 'nama' => 'Crop Top'],
            ['kode' => 'OVS', 'nama' => 'Oversized'],
            ['kode' => 'TWT', 'nama' => 'Kombinasi Two-Tone'],
        ];
        foreach ($newModels as $nm) {
            $exists = DB::table('ms_model')->where('nama', $nm['nama'])->orWhere('nama_model', $nm['nama'])->exists();
            if (!$exists) {
                DB::table('ms_model')->insert([
                    'kode'       => $nm['kode'],
                    'nama'       => $nm['nama'],
                    'nama_model' => $nm['nama'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. WARNA
        $cleanWarna = [
            'BLK' => 'Hitam',
            'BW'  => 'Putih / Broken White',
            'CRM' => 'Krem / Cream',
            'BEG' => 'Beige / Khaki',
            'MCC' => 'Mocca / Brownish',
            'BRN' => 'Cokelat Tua',
            'SGV' => 'Sage Green / Mint',
            'ARM' => 'Hijau Army / Olive',
            'PNK' => 'Pink / Soft Pink',
            'LIL' => 'Ungu / Lilac / Lavender',
            'NVY' => 'Navy / Biru Dongker',
            'SKB' => 'Biru Muda / Sky Blue',
            'MRN' => 'Merah / Maroon',
            'MST' => 'Kuning / Mustard',
            'GRY' => 'Abu-Abu / Misty',
            'MUL' => 'Multicolor / Kombinasi',
        ];

        // Mapping legacy codes to new ones
        $colorMapping = [
            'RED' => 'MRN', 'MBN' => 'MRN', 'BLU' => 'SKB', 'WHT' => 'BW',
            'GRN' => 'SGV', 'YEL' => 'MST', 'PPL' => 'LIL', 'CML' => 'CRM',
        ];

        foreach ($colorMapping as $oldKode => $newKode) {
            $oldWarna = DB::table('ms_warna')->where('kode', $oldKode)->first();
            $newWarna = DB::table('ms_warna')->where('kode', $newKode)->first();

            if ($oldWarna && $newWarna) {
                DB::table('produk')->where('id_warna', $oldWarna->id)->update(['id_warna' => $newWarna->id]);
                DB::table('ms_warna')->where('id', $oldWarna->id)->delete();
            }
        }

        foreach ($cleanWarna as $kode => $nama) {
            $w = DB::table('ms_warna')->where('kode', $kode)->first();
            if ($w) {
                DB::table('ms_warna')->where('id', $w->id)->update([
                    'nama'       => $nama,
                    'nama_warna' => $nama,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('ms_warna')->insert([
                    'kode'       => $kode,
                    'nama'       => $nama,
                    'nama_warna' => $nama,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
    }
};
