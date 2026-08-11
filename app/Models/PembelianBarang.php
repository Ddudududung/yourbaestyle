<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianBarang extends Model
{
    protected $table = 'pembelian_barang';

    protected $fillable = [
        'id_produk',
        'id_pemasok',
        'qty',
        'jumlah',
        'harga_beli_per_unit',
        'harga_satuan',
        'total_harga',
        'total_modal',
        'tanggal',
        'tanggal_pembelian',
        'keterangan',
        'status'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'id_pemasok');
    }
}
