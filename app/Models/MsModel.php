<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsModel extends Model
{
    use HasFactory;

    protected $table = 'ms_model';

    protected $fillable = [
        'kode',
        'nama',
        'nama_model',
    ];

    public function getNamaAttribute($value)
    {
        return $value ?? $this->attributes['nama_model'] ?? '';
    }

    // Relasi: 1 Model (misal: Garis) bisa dimiliki oleh banyak Produk
    public function produks()
    {
        return $this->hasMany(Produk::class, 'id_model');
    }
}