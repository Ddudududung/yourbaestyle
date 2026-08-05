<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Pemasok;
use App\Models\PembelianBarang;
use App\Models\MsJenisPakaian;
use App\Models\MsWarna;
use App\Models\MsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProdukController extends Controller
{
    // ============================================================
    // INDEX
    // ============================================================
    public function index(Request $request)
    {
        $query = Produk::with(['jenisPakaian', 'warna', 'model', 'pemasok']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_produk', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_produk', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('id_jenis_pakaian')) {
            $query->where('id_jenis_pakaian', $request->id_jenis_pakaian);
        }
        if ($request->filled('id_warna')) {
            $query->where('id_warna', $request->id_warna);
        }
        if ($request->filled('id_model')) {
            $query->where('id_model', $request->id_model);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $produk = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        $jenisPakaian = MsJenisPakaian::orderBy('nama_jenis')->get();
        $warna = MsWarna::orderBy('nama_warna')->get();
        $model = MsModel::orderBy('nama_model')->get();

        return view('produk.index', compact('produk', 'jenisPakaian', 'warna', 'model'));
    }

    // ============================================================
    // CREATE
    // ============================================================
    public function create()
    {
        $pemasok = Pemasok::orderBy('nama_pemasok')->get();
        $jenisPakaian = MsJenisPakaian::orderBy('nama_jenis')->get();
        $warna = MsWarna::orderBy('nama_warna')->get();
        $model = MsModel::orderBy('nama_model')->get();

        return view('produk.create', compact('pemasok', 'jenisPakaian', 'warna', 'model'));
    }

    // ============================================================
    // STORE
    // ============================================================
    public function store(Request $request)
    {
        try {
            // BERSIHKAN TITIK DARI VALUE RUPIAH SEBELUM VALIDASI
            if ($request->has('harga_jual')) {
                $request->merge(['harga_jual' => str_replace('.', '', $request->harga_jual)]);
            }
            if ($request->has('harga_beli_per_unit')) {
                $request->merge(['harga_beli_per_unit' => str_replace('.', '', $request->harga_beli_per_unit)]);
            }
            if ($request->has('hpp_realisasi')) {
                $request->merge(['hpp_realisasi' => str_replace('.', '', $request->hpp_realisasi)]);
            }

            // VALIDASI
            $request->validate([
                'nama_produk'        => 'required|string|max:150',
                'id_jenis_pakaian'   => 'required|exists:ms_jenis_pakaian,id',
                'id_warna'           => 'required|exists:ms_warna,id',
                'id_model'           => 'required|exists:ms_model,id',
                'harga_jual'         => 'required|numeric|min:0',
                'id_pemasok'         => 'required|exists:pemasok,id',
                'jumlah'             => 'required|integer|min:1',
                'harga_beli_per_unit'=> 'required|numeric|min:0',
                'hpp_realisasi'      => 'nullable|numeric|min:0',
                'foto'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'deskripsi'          => 'nullable|string',
            ]);

            DB::beginTransaction();

            // HITUNG HPP OTOMATIS
            $totalModal  = $request->jumlah * $request->harga_beli_per_unit;
            $hppOtomatis = $totalModal / $request->jumlah;

            // UPLOAD FOTO
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('produk', 'public');
            }

            // GENERATE KODE PRODUK OTOMATIS
            $kodeProduk = Produk::generateKode('rebranding');

            // CREATE PRODUK
            $produk = Produk::create([
                'kode_produk'        => $kodeProduk,
                'nama_produk'        => $request->nama_produk,
                'id_jenis_pakaian'   => $request->id_jenis_pakaian,
                'id_warna'           => $request->id_warna,
                'id_model'           => $request->id_model,
                'harga_jual'         => $request->harga_jual,
                'harga_beli_per_unit'=> $request->harga_beli_per_unit,
                'hpp_otomatis'       => $hppOtomatis,
                'hpp_realisasi'      => $request->hpp_realisasi ?? $hppOtomatis,
                'stok'               => $request->jumlah,
                'id_pemasok'         => $request->id_pemasok,
                'foto'               => $fotoPath,
                'deskripsi'          => $request->deskripsi,
                'status'             => 'aktif',
            ]);

            // CREATE PEMBELIAN AWAL
            PembelianBarang::create([
                'id_produk'          => $produk->id,
                'qty'                => $request->jumlah,
                'harga_beli_per_unit'=> $request->harga_beli_per_unit,
                'tanggal_pembelian'  => now()->toDateString(),
                'keterangan'         => 'Pembelian awal produk',
            ]);

            DB::commit();

            Log::info('Produk created', [
                'nama' => $request->nama_produk,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('produk.show', $produk->id)
                             ->with('success', '✅ Produk [' . $produk->nama_produk . '] berhasil ditambahkan! Stok: ' . $request->jumlah . ' unit');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Produk creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()
                         ->with('error', '❌ Gagal menambah produk: ' . $e->getMessage());
        }
    }

    // ============================================================
    // SHOW
    // ============================================================
    public function show(string $id)
    {
        $produk = Produk::with(['pemasok', 'retur', 'jenisPakaian', 'warna', 'model'])->findOrFail($id);

        $riwayat = PembelianBarang::where('id_produk', $id)
                    ->orderBy('tanggal_pembelian', 'desc')
                    ->get();

        return view('produk.show', compact('produk', 'riwayat'));
    }

    // ============================================================
    // EDIT
    // ============================================================
    public function edit(string $id)
    {
        $produk = Produk::findOrFail($id);
        $pemasok = Pemasok::orderBy('nama_pemasok')->get();
        $jenisPakaian = MsJenisPakaian::orderBy('nama_jenis')->get();
        $warna = MsWarna::orderBy('nama_warna')->get();
        $model = MsModel::orderBy('nama_model')->get();
        
        return view('produk.edit', compact('produk', 'pemasok', 'jenisPakaian', 'warna', 'model'));
    }

    // ============================================================
    // UPDATE
    // ============================================================
    public function update(Request $request, string $id)
    {
        try {
            $produk = Produk::findOrFail($id);

            // BERSIHKAN TITIK DARI VALUE RUPIAH SEBELUM VALIDASI
            if ($request->has('harga_jual')) {
                $request->merge(['harga_jual' => str_replace('.', '', $request->harga_jual)]);
            }
            if ($request->has('harga_beli_per_unit')) {
                $request->merge(['harga_beli_per_unit' => str_replace('.', '', $request->harga_beli_per_unit)]);
            }
            if ($request->has('hpp_realisasi')) {
                $request->merge(['hpp_realisasi' => str_replace('.', '', $request->hpp_realisasi)]);
            }

            $request->validate([
                'nama_produk'           => 'required|string|max:150',
                'id_jenis_pakaian'      => 'required|exists:ms_jenis_pakaian,id',
                'id_warna'              => 'required|exists:ms_warna,id',
                'id_model'              => 'required|exists:ms_model,id',
                'harga_jual'            => 'required|numeric|min:0',
                'id_pemasok'            => 'required|exists:pemasok,id',
                'jenis_koreksi_stok'    => 'nullable|in:tetap,tambah,kurang,set_total',
                'qty_koreksi'           => 'nullable|integer|min:0',
                'stok_total_baru'       => 'nullable|integer|min:0',
                'qty_tambah'            => 'nullable|integer|min:0',
                'harga_beli_per_unit'   => 'nullable|numeric|min:0',
                'hpp_realisasi'         => 'nullable|numeric|min:0',
                'deskripsi'             => 'nullable|string',
                'status'                => 'nullable|in:aktif,nonaktif',
            ]);

            DB::beginTransaction();

            $stokLama = $produk->stok;
            $stokBaru = $stokLama;
            $pesanStok = "";

            // Backward compatibility jika form lama mengirim qty_tambah
            $jenisKoreksi = $request->jenis_koreksi_stok;
            if (!$jenisKoreksi && $request->filled('qty_tambah') && $request->qty_tambah > 0) {
                $jenisKoreksi = 'tambah';
                $request->merge(['qty_koreksi' => $request->qty_tambah]);
            }

            // PROSES LOGIK PENYESUAIAN STOK (TAMBAH / KURANG / SET TOTAL)
            if ($jenisKoreksi === 'tambah' && $request->filled('qty_koreksi') && $request->qty_koreksi > 0) {
                $qtyTambah = (int) $request->qty_koreksi;
                $stokBaru = $stokLama + $qtyTambah;
                $hargaBaru = $request->harga_beli_per_unit ?? $produk->harga_beli_per_unit;
                
                // Hitung HPP baru (rata-rata tertimbang)
                $nilaiStokLama = $stokLama * $produk->hpp_otomatis;
                $nilaiStokBaru = $qtyTambah * $hargaBaru;
                $hppBaruOtomatis = ($stokLama + $qtyTambah) > 0 ? ($nilaiStokLama + $nilaiStokBaru) / ($stokLama + $qtyTambah) : $produk->hpp_otomatis;
                
                $produk->hpp_otomatis = $hppBaruOtomatis;
                $produk->harga_beli_per_unit = $hargaBaru;
                
                PembelianBarang::create([
                    'id_produk'          => $produk->id,
                    'qty'                => $qtyTambah,
                    'harga_beli_per_unit'=> $hargaBaru,
                    'tanggal_pembelian'  => now()->toDateString(),
                    'keterangan'         => 'Penambahan stok via edit produk',
                ]);

                $pesanStok = "Stok bertambah {$qtyTambah} unit (stok baru: {$stokBaru} pcs).";

            } elseif ($jenisKoreksi === 'kurang' && $request->filled('qty_koreksi') && $request->qty_koreksi > 0) {
                $qtyKurang = (int) $request->qty_koreksi;
                $stokBaru = max(0, $stokLama - $qtyKurang);
                $pesanStok = "Stok berkurang {$qtyKurang} unit (stok baru: {$stokBaru} pcs).";

            } elseif ($jenisKoreksi === 'set_total' && $request->filled('stok_total_baru')) {
                $stokBaru = max(0, (int) $request->stok_total_baru);
                $pesanStok = "Total stok diperbarui menjadi {$stokBaru} pcs.";
            }

            // SIMPAN DATA PRODUK
            $produk->stok = $stokBaru;
            $produk->nama_produk = $request->nama_produk;
            $produk->id_jenis_pakaian = $request->id_jenis_pakaian;
            $produk->id_warna = $request->id_warna;
            $produk->id_model = $request->id_model;
            $produk->harga_jual = $request->harga_jual;
            $produk->id_pemasok = $request->id_pemasok;

            if ($request->filled('harga_beli_per_unit')) {
                $hargaBeliVal = (float) $request->harga_beli_per_unit;
                $produk->harga_beli_per_unit = $hargaBeliVal;
                // Jika HPP otomatis belum ada, set awal sesuai harga beli
                if (empty($produk->hpp_otomatis) || $produk->hpp_otomatis == 0) {
                    $produk->hpp_otomatis = $hargaBeliVal;
                }
            }

            if ($request->filled('status')) {
                $produk->status = $request->status;
            }
            if ($request->filled('hpp_realisasi')) {
                $produk->hpp_realisasi = $request->hpp_realisasi;
            }
            $produk->deskripsi = $request->deskripsi;
            $produk->save();

            DB::commit();

            $message = !empty($pesanStok) 
                ? "✅ Produk [{$produk->nama_produk}] berhasil diupdate! {$pesanStok}"
                : "✅ Data produk [{$produk->nama_produk}] berhasil diupdate!";

            Log::info('Produk updated', [
                'produk_id' => $produk->id,
                'stok_lama' => $stokLama,
                'stok_baru' => $stokBaru,
                'user_id'   => Auth::id(),
            ]);

            return redirect()->route('produk.show', $produk->id)
                             ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Produk update failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()
                         ->with('error', '❌ Gagal update produk: ' . $e->getMessage());
        }
    }

    // ============================================================
    // DELETE
    // ============================================================
    public function destroy(string $id)
    {
        try {
            $produk = Produk::findOrFail($id);
            $nama = $produk->nama_produk;
            $kode = $produk->kode_produk;

            $produk->update(['status' => 'nonaktif']);

            Log::info('Produk deactivated', [
                'kode' => $kode,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('produk.index')
                             ->with('success', "✅ Produk [{$kode} - {$nama}] berhasil dinonaktifkan!");

        } catch (\Exception $e) {
            Log::error('Produk delete failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', '❌ Gagal nonaktifkan produk: ' . $e->getMessage());
        }
    }
}