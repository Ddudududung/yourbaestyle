<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB; // Pastikan import DB untuk fungsi bisaAkses

#[Fillable(['nama', 'email', 'password', 'id_role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    
    protected $table = 'ms_user';
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =========================================================
    // TAMBAHAN FUNGSI UNTUK MIDDLEWARE OTORISASI
    // =========================================================

    /**
     * Cek apakah user adalah Owner (berdasarkan id_role = 1)
     */
    public function isOwner(): bool
    {
        return $this->id_role === 1;
    }

    /**
     * Relasi ke tabel ms_role (Opsional tapi sangat direkomendasikan)
     */
    public function role()
    {
        return $this->belongsTo(MsRole::class, 'id_role'); 
    }

    /**
     * Cek hak akses menu berdasarkan relasi tabel role_menu dan ms_menu
     */
    public function bisaAkses($url): bool
    {
        $cleanUrl = trim($url, '/');
        // Mengecek apakah role user ini punya akses ke target_url yang dituju
        return DB::table('role_menu')
            ->join('ms_menu', 'role_menu.id_menu', '=', 'ms_menu.id')
            ->where('role_menu.id_role', $this->id_role)
            ->where(function($q) use ($url, $cleanUrl) {
                $q->where('ms_menu.target_url', $url)
                  ->orWhere('ms_menu.target_url', $cleanUrl)
                  ->orWhere('ms_menu.target_url', '/' . $cleanUrl);
            })
            ->where('ms_menu.is_active', 1)
            ->exists();
    }
}