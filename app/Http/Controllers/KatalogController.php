<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        // 1. CARI ID PRODUK YANG SEDANG LIVE (Status 'ongoing' DAN belum lewat jam selesai)
        $liveProdukIds = DB::table('katalog_lives')
            ->join('sesi_lives', 'katalog_lives.id_sesi_live', '=', 'sesi_lives.id')
            ->where('sesi_lives.status', 'ongoing')
            ->whereRaw("TIMESTAMP(sesi_lives.tanggal_live, sesi_lives.jam_selesai) >= ?", [now()])
            ->pluck('katalog_lives.id_produk')
            ->toArray();

        // Fungsi filter pencarian (nama produk / jenis)
        $filter = function ($query) use ($request) {
            if ($request->filled('search')) {
                $query->where('nama_produk', 'like', '%' . $request->search . '%');
            }
            if ($request->filled('jenis')) {
                $query->where('jenis', $request->jenis);
            }
        };

        // 2. KELOMPOK ATAS: Khusus Produk Sedang Live & Stok Ready (> 0)
        $liveProduks = Produk::where('status', 'aktif')
            ->where('stok', '>', 0)
            ->whereIn('id', $liveProdukIds) // Cocokkan dengan ID barang yang sedang live
            ->where($filter)
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        // 3. KELOMPOK BAWAH: Produk Tidak Live ATAU Stok Habis
        $otherProduks = Produk::where('status', 'aktif')
            ->where(function ($query) use ($liveProdukIds) {
                $query->where('stok', '<=', 0) // Barang habis
                      ->orWhereNotIn('id', $liveProdukIds); // ATAU barang yang tidak sedang live
            })
            ->where($filter)
            ->orderByDesc('stok') // Tampilkan yang ready terlebih dahulu
            ->orderByDesc('created_at')
            ->take(12)
            ->get();

        return view('katalog.index', compact('liveProduks', 'otherProduks'));
    }
}