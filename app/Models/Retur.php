<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    protected $table = 'retur';
    protected $fillable = [
        'id_pesanan_online', 'id_produk', 'id_user', 'tanggal',
        'alasan', 'kondisi_barang', 'ongkir_retur', 'nilai_kerugian', 'qty',
        'tipe_retur', 'id_produk_pengganti', 'qty_pengganti'
    ];

    public function pesananOnline()
    {
        return $this->belongsTo(PesananOnline::class, 'id_pesanan_online');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function produkPengganti()
    {
        return $this->belongsTo(Produk::class, 'id_produk_pengganti');
    }

    public function user()
    {
        return $this->belongsTo(MsUser::class, 'id_user');
    }
}
