<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsMenu extends Model
{
    protected $table = 'ms_menu';
    protected $fillable = ['nama_menu', 'target_url', 'icon_url', 'order_menu'];

    public function roles()
    {
        return $this->belongsToMany(MsRole::class, 'role_menu', 'id_menu', 'id_role');
    }
}
