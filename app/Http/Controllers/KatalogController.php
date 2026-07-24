<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::where('status', 'aktif');

        // Fitur pencarian sederhana untuk pelanggan
        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Tampilkan produk, prioritaskan yang stoknya masih ada, lalu yang terbaru
        $produks = $query->orderByDesc('stok')
                         ->orderByDesc('created_at')
                         ->paginate(12);

        return view('katalog.index', compact('produks'));
    }
}