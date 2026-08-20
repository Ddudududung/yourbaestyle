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

        $userRole = strtolower($user->role?->nama_role ?? '');
        $allowedRoles = array_map('strtolower', $roles);

        if (!$user || (!in_array($userRole, $allowedRoles) && !$user->isOwner())) {
            abort(403, 'Akses Ditolak — Role tidak diizinkan');
        }

        return $next($request);
    }
}
