<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class KatalogLive extends Model
{
    use HasFactory;
 
    protected $table = 'katalog_lives';
 
    protected $fillable = [
        'id_sesi_live',
        'kode_live',
        'tanggal_live',
        'jam_live',
        'id_produk',
        'harga_live',
    ];
 
    protected $casts = [
        'tanggal_live' => 'date',
        'jam_live' => 'datetime:H:i:s',
        'harga_live' => 'decimal:2',
    ];
 
    // ========== RELATIONSHIPS ==========
 
    /**
     * Katalog live belong to sesi
     */
    public function sesiLive(): BelongsTo
    {
        return $this->belongsTo(SesiLive::class, 'id_sesi_live');
    }
 
    /**
     * Katalog link ke produk
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
 
    // ========== SCOPES ==========
 
    /**
     * Cari by kode exact
     */
    public function scopeByKode($query, $kode)
    {
        return $query->where('kode_live', $kode);
    }
 
    /**
     * Cari by sesi
     */
    public function scopeBySesi($query, $idSesi)
    {
        return $query->where('id_sesi_live', $idSesi);
    }
 
    /**
     * Cari by tanggal dan jam
     */
    public function scopeByDateAndTime($query, $date, $time)
    {
        return $query->whereDate('tanggal_live', $date)->whereTime('jam_live', $time);
    }
 
    // ========== METHODS ==========
 
    /**
     * Get display info
     */
    public function getDisplayAttribute(): string
    {
        $produk = $this->produk?->nama_produk ?? 'Produk tidak ditemukan';
        return "{$this->kode_live} → {$produk}";
    }
}