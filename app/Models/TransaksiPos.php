<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPos extends Model
{
    protected $table = 'transaksi_pos';
    protected $fillable = [
        'kode_transaksi', 'id_user', 'tanggal',
        'total_harga', 'total_hpp', 'diskon', 'metode_bayar'
    ];

    // 1. TAMBAHAN PERTAMA: Mematikan auto-timestamp agar tidak error 'updated_at'
    public $timestamps = false; 

    // 2. TAMBAHAN KEDUA: Memberitahu Laravel bahwa 'tanggal' adalah format Waktu (Datetime)
    // Ini yang akan menyembuhkan error "Call to a member function format() on string"
    protected $casts = [
        'tanggal' => 'datetime',
    ];

    // Generate kode transaksi otomatis
    public static function generateKode(): string
    {
        $tanggal = now()->format('Ymd');
        $count = self::whereDate('tanggal', today())->count() + 1;
        return 'TRX-' . $tanggal . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(MsUser::class, 'id_user');
    }

    public function detail()
    {
        return $this->hasMany(DetailTransaksiPos::class, 'id_transaksi');
    }
}