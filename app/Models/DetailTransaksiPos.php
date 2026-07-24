<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksiPos extends Model
{
    public $timestamps = false;
    protected $table = 'detail_transaksi_pos';
    protected $fillable = [
        'id_transaksi', 'id_produk', 'qty', 'harga_satuan', 'hpp_satuan'
    ];

    public function transaksi()
    {
        return $this->belongsTo(TransaksiPos::class, 'id_transaksi');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function getSubtotalAttribute(): float
    {
        return $this->qty * $this->harga_satuan;
    }

    public function getSubtotalHppAttribute(): float
    {
        return $this->qty * $this->hpp_satuan;
    }
}
