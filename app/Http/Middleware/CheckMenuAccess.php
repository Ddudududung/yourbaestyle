<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckMenuAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Bypass Owner (Jika fungsi isOwner di Model sudah benar, Owner tidak akan pernah kena 403 lagi)
        if ($user->isOwner()) {
            return $next($request);
        }

        // 2. Ambil full path URL saat ini (contoh: '/pengaturan/role' atau '/pesanan/import')
        $currentPath = '/' . $request->path(); 

        // 3. Ambil semua target_url yang aktif di tabel ms_menu
        $allMenus = DB::table('ms_menu')->where('is_active', 1)->pluck('target_url')->toArray();

        $matchedUrl = null;
        foreach ($allMenus as $menuUrl) {
            // Cek apakah URL saat ini sama persis ATAU diawali oleh menuUrl tersebut
            // Misal: '/pengaturan/role' sama dengan '/pengaturan/role' -> COCOK
            // Misal: '/pesanan/import' diawali oleh '/pesanan/' -> COCOK
            if ($currentPath === $menuUrl || str_starts_with($currentPath, $menuUrl . '/')) {
                // Jika ada beberapa yang mirip, pilih target_url yang paling spesifik/panjang
                if (is_null($matchedUrl) || strlen($menuUrl) > strlen($matchedUrl)) {
                    $matchedUrl = $menuUrl;
                }
            }
        }

        // Jika ditemukan menu induknya di DB, gunakan itu untuk dicek ke bisaAkses()
        // Jika tidak ada di DB, biarkan menggunakan path asli saat ini
        $urlToCheck = $matchedUrl ?? $currentPath;

        // 4. Cek hak akses role user ke URL yang sudah disesuaikan
        if (!$user->bisaAkses($urlToCheck)) {
            abort(403, 'Akses Ditolak');
        }

        return $next($request);
    }
}