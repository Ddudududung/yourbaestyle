<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use Illuminate\Http\Request;

class PemasokController extends Controller
{
    // ============================================================
    // INDEX — Daftar semua pemasok
    // ============================================================
    public function index(Request $request)
    {
        $query = Pemasok::query();

        if ($request->filled('search')) {
            $query->where('nama_pemasok', 'like', '%' . $request->search . '%')
                  ->orWhere('kontak', 'like', '%' . $request->search . '%');
        }

        $pemasok = $query->orderBy('nama_pemasok')->paginate(10);

        return view('pemasok.index', compact('pemasok'));
    }

    // ============================================================
    // CREATE — Form tambah pemasok
    // ============================================================
    public function create()
    {
        return view('pemasok.create');
    }

    // ============================================================
    // STORE — Simpan pemasok baru
    // ============================================================
    public function store(Request $request)
    {
        $request->validate([
            'nama_pemasok' => 'required|string|max:150',
            'kontak'       => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
        ], [
            'nama_pemasok.required' => 'Nama pemasok wajib diisi.',
        ]);

        Pemasok::create([
            'nama_pemasok' => $request->nama_pemasok,
            'kontak'       => $request->kontak,
            'alamat'       => $request->alamat,
        ]);

        return redirect()->route('pemasok.index')
                         ->with('success', "Pemasok {$request->nama_pemasok} berhasil ditambahkan.");
    }

    // ============================================================
    // SHOW — Detail pemasok + riwayat pembelian dari pemasok ini
    // ============================================================
    public function show(string $id)
    {
        $pemasok  = Pemasok::findOrFail($id);
        $riwayat  = $pemasok->pembelian()->with('produk')
                             ->orderBy('tanggal', 'desc')
                             ->get();

        return view('pemasok.show', compact('pemasok', 'riwayat'));
    }

    // ============================================================
    // EDIT — Form edit pemasok
    // ============================================================
    public function edit(string $id)
    {
        $pemasok = Pemasok::findOrFail($id);
        return view('pemasok.edit', compact('pemasok'));
    }

    // ============================================================
    // UPDATE — Update data pemasok
    // ============================================================
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_pemasok' => 'required|string|max:150',
            'kontak'       => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
        ]);

        $pemasok = Pemasok::findOrFail($id);
        $pemasok->update([
            'nama_pemasok' => $request->nama_pemasok,
            'kontak'       => $request->kontak,
            'alamat'       => $request->alamat,
        ]);

        return redirect()->route('pemasok.index')
                         ->with('success', 'Data pemasok berhasil diperbarui.');
    }

    // ============================================================
    // DESTROY — Hapus pemasok (cek dulu apakah ada riwayat pembelian)
    // ============================================================
    public function destroy(string $id)
    {
        $pemasok = Pemasok::findOrFail($id);

        // Tidak bisa hapus jika masih ada riwayat pembelian
        if ($pemasok->pembelian()->exists()) {
            return back()->with('error', 'Pemasok tidak dapat dihapus karena masih memiliki riwayat pembelian barang.');
        }

        $pemasok->delete();

        return redirect()->route('pemasok.index')
                         ->with('success', 'Pemasok berhasil dihapus.');
    }
}
