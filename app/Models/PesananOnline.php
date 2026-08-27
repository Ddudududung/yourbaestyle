<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
 
class PesananOnline extends Model
{
    use HasFactory;
 
    protected $table = 'pesanan_online';
 
    protected $fillable = [
        'no_pesanan',
        'id_user',
        'platform',
        'format_csv',
        'no_resi',
        'id_sesi_live',
        'nama_pembeli',
        'tanggal',
        'total_harga',
        'total_hpp',
        'ongkir',
        'status',
        'status_mapping',
        'pesan_mapping',
        'kode_live_raw',
    ];
 
    protected $casts = [
        'tanggal' => 'datetime',
        'total_harga' => 'decimal:2',
        'total_hpp' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
 
    // ========== RELATIONSHIPS ==========
 
    /**
     * Pesanan milik user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(MsUser::class, 'id_user');
    }
 
    /**
     * Pesanan belong to sesi live
     */
    public function sesiLive(): BelongsTo
    {
        return $this->belongsTo(SesiLive::class, 'id_sesi_live');
    }
 
    /**
     * Pesanan punya banyak detail
     */
    public function detail(): HasMany
    {
        return $this->hasMany(DetailPesananOnline::class, 'id_pesanan');
    }
 
    // ========== SCOPES ==========
 
    /**
     * Filter by sesi
     */
    public function scopeBySesi($query, $idSesi)
    {
        return $query->where('id_sesi_live', $idSesi);
    }
 
    /**
     * Filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
 
    /**
     * Get profit
     */
    public function getProfitAttribute(): float
    {
        return $this->total_harga - $this->total_hpp;
    }
}