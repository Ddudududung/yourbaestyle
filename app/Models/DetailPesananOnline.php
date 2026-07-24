<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPesananOnline extends Model
{
    public $timestamps = false;
    protected $table = 'detail_pesanan_online';
    protected $fillable = [
        'id_pesanan', 'id_produk', 'variasi',
        'qty', 'harga_satuan', 'hpp_satuan'
    ];

    public function pesanan()
    {
        return $this->belongsTo(PesananOnline::class, 'id_pesanan');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
