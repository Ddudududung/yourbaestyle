<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class SesiLive extends Model
{
    use HasFactory;
 
    protected $table = 'sesi_live';
    
    protected $fillable = [
        'platform',
        'nama_sesi',
        'tanggal_live',
        'jam_mulai',
        'jam_selesai',
        'status',
        'total_pesanan',
        'total_revenue',
        'notes',
        'created_by',
    ];
 
    protected $casts = [
        'tanggal_live' => 'date',
        'jam_mulai' => 'datetime:H:i:s',
        'jam_selesai' => 'datetime:H:i:s',
        'total_revenue' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
 
    // ========== RELATIONSHIPS ==========
 
    /**
     * Sesi milik user tertentu
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
 
    /**
     * Sesi punya banyak katalog live
     */
    public function katalogLives(): HasMany
    {
        return $this->hasMany(KatalogLive::class, 'id_sesi_live');
    }
 
    /**
     * Sesi punya banyak pesanan
     */
    public function pesananOnlines(): HasMany
    {
        return $this->hasMany(PesananOnline::class, 'id_sesi_live');
    }
 
    // ========== ACCESSORS & MUTATORS ==========
 
    /**
     * Get display format: "Live Pagi (08:00 - 10:30)"
     */
    public function getDisplayNameAttribute(): string
    {
        $time = $this->jam_mulai->format('H:i');
        if ($this->jam_selesai) {
            $time .= ' - ' . $this->jam_selesai->format('H:i');
        }
        return "{$this->nama_sesi} ({$time})";
    }
 
    /**
     * Status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'secondary',
            'ongoing' => 'success',
            'completed' => 'info',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }
 
    // ========== SCOPES ==========
 
    /**
     * Scope: Sesi hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_live', today());
    }
 
    /**
     * Scope: Sesi upcoming (scheduled)
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')->orderBy('tanggal_live')->orderBy('jam_mulai');
    }
 
    /**
     * Scope: Sesi sedang berlangsung (ongoing)
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }
 
    /**
     * Scope: Filter by platform
     */
    public function scopePlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }
 
    /**
     * Scope: Filter by tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal_live', $date);
    }
 
    // ========== METHODS ==========
 
    /**
     * Mulai sesi (ubah status ke ongoing)
     */
    public function start(): bool
    {
        return $this->update(['status' => 'ongoing']);
    }
 
    /**
     * Selesaikan sesi (ubah status ke completed)
     */
    public function complete(): bool
    {
        $this->jam_selesai = now();
        return $this->update([
            'status' => 'completed',
            'jam_selesai' => now()->toTimeString(),
        ]);
    }
 
    /**
     * Cancel sesi
     */
    public function cancel($reason = null): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'notes' => $reason,
        ]);
    }
 
    /**
     * Update counter total pesanan
     */
    public function updatePesananCount(): void
    {
        $total = $this->pesananOnlines()->count();
        $this->update(['total_pesanan' => $total]);
    }
 
    /**
     * Update total revenue
     */
    public function updateRevenue(): void
    {
        $revenue = $this->pesananOnlines()
            ->whereNotIn('status', ['cancelled'])
            ->sum('total_harga');
        $this->update(['total_revenue' => $revenue]);
    }
 
    /**
     * Get statistik sesi
     */
    public function getStatistics(): array
    {
        return [
            'total_pesanan' => $this->pesananOnlines()->count(),
            'total_revenue' => $this->pesananOnlines()->sum('total_harga'),
            'total_hpp' => $this->pesananOnlines()->sum('total_hpp'),
            'profit' => $this->pesananOnlines()->sum('total_harga') - $this->pesananOnlines()->sum('total_hpp'),
            'avg_order_value' => $this->pesananOnlines()->avg('total_harga'),
            'status_count' => $this->pesananOnlines()->pluck('status')->countBy(),
        ];
    }
}