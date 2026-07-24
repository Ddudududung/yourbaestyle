<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MsUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'ms_user';
    protected $fillable = ['id_role', 'nama', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];

    public function role()
    {
        return $this->belongsTo(MsRole::class, 'id_role');
    }

    // Cek apakah user punya akses ke menu tertentu
    public function bisaAkses(string $url): bool
    {
        return $this->role->menus()->where('target_url', $url)->exists();
    }

    // Ambil semua menu yang boleh diakses user ini (untuk sidebar)
    public function menuAktif()
    {
        return $this->role->menus()->where('is_active', true)->orderBy('order_menu')->get();
    }

    // Shortcut cek role
    public function isOwner(): bool { return $this->role->nama_role === 'owner'; }
    public function isAdmin(): bool  { return $this->role->nama_role === 'admin'; }
    public function isStaf(): bool   { return $this->role->nama_role === 'staf'; }
}
