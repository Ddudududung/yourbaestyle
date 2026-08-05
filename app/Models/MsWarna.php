<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsWarna extends Model
{
    use HasFactory;

    protected $table = 'ms_warnas';

    protected $fillable = [
        'kode',
        'nama',
    ];

    // Relasi: 1 Warna (misal: Kuning) bisa dimiliki oleh banyak Produk
    public function produks()
    {
        return $this->hasMany(Produk::class, 'id_warna');
    }
}