<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsJenisPakaian extends Model
{
    use HasFactory;

    // Menegaskan nama tabel di database phpMyAdmin
    protected $table = 'ms_jenis_pakaians';

    // Kolom yang boleh diisi saat input data
    protected $fillable = [
        'kode',
        'nama',
    ];

    // Relasi: 1 Jenis Pakaian (misal: Knitwear) bisa dimiliki oleh banyak Produk
    public function produks()
    {
        return $this->hasMany(Produk::class, 'id_jenis_pakaian');
    }
}