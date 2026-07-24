<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianBarang extends Model
{
    protected $table = 'pembelian_barang';
    protected $fillable = [
        'id_produk', 'id_pemasok', 'tanggal',
        'jumlah', 'harga_beli_per_unit', 'total_modal'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'id_pemasok');
    }

    // Hitung HPP otomatis setelah pembelian disimpan
    public function hitungHPPOtomatis(): float
    {
        return $this->jumlah > 0 ? $this->total_modal / $this->jumlah : 0;
    }
}
