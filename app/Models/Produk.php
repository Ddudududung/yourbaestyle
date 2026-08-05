<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'id_jenis_pakaian',
        'id_warna',
        'id_model',
        'harga_jual',
        'harga_beli_per_unit',
        'hpp_otomatis',
        'hpp_realisasi',
        'stok',
        'id_pemasok',
        'foto',
        'deskripsi',
        'status',
    ];

    // HPP yang aktif digunakan saat transaksi
    // Pakai hpp_realisasi jika sudah diisi, fallback ke hpp_otomatis
    public function getHppAktifAttribute(): float
    {
        return $this->hpp_realisasi ?? $this->hpp_otomatis;
    }

    // Generate kode produk otomatis
    public static function generateKode(string $jenis): string
    {
        $prefix = $jenis === 'thrift' ? 'PRD-THFT' : 'PRD-RBRN';
        $last = self::where('kode_produk', 'like', $prefix . '%')
                    ->orderBy('id', 'desc')
                    ->first();
        $nomor = $last ? (int) substr($last->kode_produk, -4) + 1 : 1;
        return $prefix . '-' . str_pad($nomor, 4, '0', STR_PAD_LEFT);
    }
    public function pemasok()
{
    // Sesuaikan 'id_pemasok' dengan nama kolom foreign key di tabel 'produk' Anda
    return $this->belongsTo(Pemasok::class, 'id_pemasok');
}

    public function pembelian()
    {
        return $this->hasMany(PembelianBarang::class, 'id_produk');
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksiPos::class, 'id_produk');
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesananOnline::class, 'id_produk');
    }

    public function retur()
    {
        return $this->hasMany(Retur::class, 'id_produk');
    }
    // Tambahkan di dalam class Produk pada file app/Models/Produk.php:

    public function jenisPakaian()
    {
        return $this->belongsTo(MsJenisPakaian::class, 'id_jenis_pakaian');
    }

    public function warna()
    {
        return $this->belongsTo(MsWarna::class, 'id_warna');
    }

    public function model()
    {
        return $this->belongsTo(MsModel::class, 'id_model');
    }
    public function sesiLive()
    {
        return $this->hasOne(\App\Models\SesiLive::class, 'id_produk')->where('status', 'aktif');
    }
}
