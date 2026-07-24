<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewMappingManual extends Model
{
    protected $table = 'review_mapping_manual';
    public $timestamps = false;

    protected $fillable = [
        'tanggal_live',
        'platform',
        'kode_live_raw',
        'qty_order',
        'no_pesanan',
        'nama_pembeli',
        'id_user_uploader',
        'id_produk_mapping',
        'status_resolusi',
        'id_user_resolver',
        'tanggal_resolusi',
        'catatan',
    ];

    protected $casts = [
        'tanggal_live' => 'datetime',
        'tanggal_resolusi' => 'datetime',
    ];

    // Relasi
    public function uploader()
    {
        return $this->belongsTo(MsUser::class, 'id_user_uploader');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk_mapping');
    }

    public function resolver()
    {
        return $this->belongsTo(MsUser::class, 'id_user_resolver');
    }

    // Scope untuk filter
    public function scopePending($query)
    {
        return $query->where('status_resolusi', 'pending');
    }

    public function scopePlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeTanggalLive($query, $tanggal)
    {
        return $query->whereDate('tanggal_live', $tanggal);
    }
}