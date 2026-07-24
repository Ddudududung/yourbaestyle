<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsRole extends Model
{
    protected $table = 'ms_role';
    protected $fillable = ['nama_role', 'is_active'];

    public function users()
    {
        return $this->hasMany(MsUser::class, 'id_role');
    }

    public function menus()
    {
        return $this->belongsToMany(MsMenu::class, 'role_menu', 'id_role', 'id_menu');
    }
}
