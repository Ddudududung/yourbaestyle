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
    public function getHppAktifAttribute(): float
    {
        if (!empty($this->hpp_realisasi) && (float)$this->hpp_realisasi > 0) {
            return (float) $this->hpp_realisasi;
        }
        return (float) ($this->hpp_otomatis > 0 ? $this->hpp_otomatis : $this->harga_beli_per_unit);
    }

    // Generate kode produk otomatis (guaranteed unique & non-duplicate)
    public static function generateKode(string $jenis = 'rebranding'): string
    {
        $prefix = $jenis === 'thrift' ? 'PRD-THFT' : 'PRD-RBRN';
        
        $existingCodes = self::where('kode_produk', 'like', $prefix . '-%')->pluck('kode_produk');
        $maxNomor = 0;
        foreach ($existingCodes as $c) {
            $parts = explode('-', $c);
            $num = (int) end($parts);
            if ($num > $maxNomor) {
                $maxNomor = $num;
            }
        }

        $nomor = $maxNomor + 1;
        do {
            $kode = $prefix . '-' . str_pad($nomor, 4, '0', STR_PAD_LEFT);
            $nomor++;
        } while (self::where('kode_produk', $kode)->exists());

        return $kode;
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
