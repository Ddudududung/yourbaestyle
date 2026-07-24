<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Pemasok;
use App\Models\PembelianBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    // ============================================================
    // INDEX — Daftar semua produk
    // ============================================================
    public function index(Request $request)
    {
        $query = Produk::query();

        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_produk', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $produk = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('produk.index', compact('produk'));
    }

    // ============================================================
    // CREATE — Form tambah produk
    // ============================================================
    public function create()
    {
        $pemasok = Pemasok::orderBy('nama_pemasok')->get();
        return view('produk.create', compact('pemasok'));
    }

    // ============================================================
    // STORE — Simpan produk baru + hitung HPP otomatis
    // ============================================================
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk'       => 'required|string|max:150',
            'jenis'             => 'required|in:thrift,rebranding',
            'harga_jual'        => 'required|numeric|min:0',
            'id_pemasok'        => 'required|exists:pemasok,id',
            'jumlah'            => 'required|integer|min:1',
            'harga_beli_per_unit'=> 'required|numeric|min:0',
            'hpp_realisasi'     => 'nullable|numeric|min:0',
            'foto'              => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'deskripsi'         => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $totalModal  = $request->jumlah * $request->harga_beli_per_unit;
            $hppOtomatis = $totalModal / $request->jumlah;

            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('produk', 'public');
            }

            $kodeProduk = Produk::generateKode($request->jenis);

            $produk = Produk::create([
                'kode_produk'       => $kodeProduk,
                'nama_produk'       => $request->nama_produk,
                'jenis'             => $request->jenis,
                'harga_jual'        => $request->harga_jual,
                'harga_beli_per_unit'=> $request->harga_beli_per_unit,
                'hpp_otomatis'      => $hppOtomatis,
                'hpp_realisasi'     => $request->hpp_realisasi ?? $hppOtomatis,
                'stok'              => $request->jumlah,
                'id_pemasok'        => $request->id_pemasok,
                'foto'              => $fotoPath,
                'deskripsi'         => $request->deskripsi,
                'status'            => 'aktif',
            ]);

            PembelianBarang::create([
                'id_produk'         => $produk->id,
                'qty'               => $request->jumlah,
                'harga_beli_per_unit'=> $request->harga_beli_per_unit,
                'keterangan'        => 'Pembelian awal produk',
            ]);

            DB::commit();
            return redirect()->route('produk.show', $produk)
                            ->with('success', 'Produk berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ============================================================
    // SHOW — Detail produk
    // ============================================================
    public function show(string $id)
{
    // 1. Ambil produk beserta relasinya
    $produk = Produk::with(['pemasok', 'retur'])->findOrFail($id);

    // 2. Ambil data riwayat pembelian berdasarkan id_produk
    // Pastikan model PembelianBarang sudah di-use di atas
    $riwayat = \App\Models\PembelianBarang::where('id_produk', $id)
                ->with('pemasok') // Pastikan ada relasi ke pemasok di model PembelianBarang
                ->orderBy('tanggal', 'desc')
                ->get();

    // 3. Kirim kedua variabel ke view
    return view('produk.show', compact('produk', 'riwayat'));
}

    // ============================================================
    // EDIT — Form edit produk dengan konveksi + add stok
    // ============================================================
    public function edit(string $id)
    {
        $produk = Produk::findOrFail($id);
        $pemasok = Pemasok::orderBy('nama_pemasok')->get();
        
        return view('produk.edit', compact('produk', 'pemasok'));
    }

    // ============================================================
    // UPDATE — Update produk dengan PENAMBAHAN STOK (bukan replace)
    // ============================================================
    public function update(Request $request, string $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'nama_produk'           => 'required|string|max:150',
            'harga_jual'            => 'required|numeric|min:0',
            'id_pemasok'            => 'required|exists:pemasok,id',
            'qty_tambah'            => 'nullable|integer|min:0',
            'harga_beli_per_unit'   => 'nullable|numeric|min:0',
            'hpp_realisasi'         => 'nullable|numeric|min:0',
            'deskripsi'             => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // LOGIC PENTING: Jika ada qty_tambah, INCREMENT stok (jangan REPLACE)
            if ($request->filled('qty_tambah') && $request->qty_tambah > 0) {
                $qtyTambah = $request->qty_tambah;
                $hargaBaru = $request->harga_beli_per_unit ?? $produk->harga_beli_per_unit;
                
                // Hitung HPP baru (rata-rata dari stok lama + stok baru)
                $nilaiStokLama = $produk->stok * $produk->hpp_otomatis;
                $nilaiStokBaru = $qtyTambah * $hargaBaru;
                $hppBaruOtomatis = ($nilaiStokLama + $nilaiStokBaru) / ($produk->stok + $qtyTambah);
                
                // Increment stok
                $produk->increment('stok', $qtyTambah);
                
                // Update produk
                $produk->update([
                    'nama_produk' => $request->nama_produk,
                    'harga_jual' => $request->harga_jual,
                    'harga_beli_per_unit' => $hargaBaru,
                    'hpp_otomatis' => $hppBaruOtomatis,
                    'hpp_realisasi' => $request->hpp_realisasi ?? $hppBaruOtomatis,
                    'id_pemasok' => $request->id_pemasok,
                    'deskripsi' => $request->deskripsi,
                ]);
                
                // Log history perubahan di PembelianBarang
                PembelianBarang::create([
                    'id_produk' => $produk->id,
                    'qty' => $qtyTambah,
                    'harga_beli_per_unit' => $hargaBaru,
                    'keterangan' => 'Penambahan stok via edit produk',
                ]);
            } else {
                // Update biasa (tidak ada qty_tambah atau 0)
                $produk->update([
                    'nama_produk' => $request->nama_produk,
                    'harga_jual' => $request->harga_jual,
                    'id_pemasok' => $request->id_pemasok,
                    'hpp_realisasi' => $request->hpp_realisasi,
                    'deskripsi' => $request->deskripsi,
                ]);
            }

            DB::commit();
            return redirect()->route('produk.show', $produk)
                            ->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ============================================================
    // DELETE — Soft delete produk
    // ============================================================
    public function destroy(string $id)
    {
        $produk = Produk::findOrFail($id);
        $produk->update(['status' => 'nonaktif']);

        return redirect()->route('produk.index')
                        ->with('success', 'Produk berhasil dinonaktifkan!');
    }
}