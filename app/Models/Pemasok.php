<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasok extends Model
{
    protected $table = 'pemasok';
    protected $fillable = ['nama_pemasok', 'kontak', 'alamat'];

    public function pembelian()
    {
        return $this->hasMany(PembelianBarang::class, 'id_pemasok');
    }
}
