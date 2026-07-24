<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Penggunaan: middleware('role:owner')
     *             middleware('role:owner,admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role->nama_role, $roles)) {
            abort(403, 'Akses Ditolak — Role tidak diizinkan');
        }

        return $next($request);
    }
}
