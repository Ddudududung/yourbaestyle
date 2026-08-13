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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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

        $statusFilter = $request->get('status', 'aktif');
        if ($statusFilter !== 'semua') {
            $query->where('status', $statusFilter);
        }

        if ($request->filled('stok')) {
            if ($request->stok === 'habis') {
                $query->where('stok', 0);
            } elseif ($request->stok === 'ada') {
                $query->where('stok', '>', 0);
            }
        }

        $produk = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        $jenisPakaian = MsJenisPakaian::orderBy('nama')->get();
        $warna = MsWarna::orderBy('nama')->get();
        $model = MsModel::orderBy('nama')->get();

        return view('produk.index', compact('produk', 'jenisPakaian', 'warna', 'model'));
    }

    // ============================================================
    // CREATE
    // ============================================================
    public function create()
    {
        $pemasok = Pemasok::orderBy('nama_pemasok')->get();
        $jenisPakaian = MsJenisPakaian::orderBy('nama')->get();
        $warna = MsWarna::orderBy('nama')->get();
        $model = MsModel::orderBy('nama')->get();

        return view('produk.create', compact('pemasok', 'jenisPakaian', 'warna', 'model'));
    }

    // ============================================================
    // HELPER ON-THE-FLY MASTER DATA
    // ============================================================
    /**
     * Helper untuk membuat 3-letter master code secara otomatis & unik
     * Contoh: "Cardigan" -> "CAR", "Kaos Polos Pria" -> "KPP", "Abu-Abu" -> "ABU"
     */
    private function generateMasterKode(string $nama, string $modelClass): string
    {
        $clean = strtoupper(trim(preg_replace('/[^A-Za-z0-9\s]/', '', $nama)));
        $words = array_values(array_filter(explode(' ', $clean)));

        if (count($words) >= 3) {
            $baseKode = substr($words[0], 0, 1) . substr($words[1], 0, 1) . substr($words[2], 0, 1);
        } elseif (count($words) === 2) {
            if (strlen($words[0]) >= 2) {
                $baseKode = substr($words[0], 0, 2) . substr($words[1], 0, 1);
            } else {
                $baseKode = substr($words[0], 0, 1) . substr($words[1], 0, 2);
            }
        } else {
            $baseKode = substr($clean, 0, 3);
        }

        $baseKode = strtoupper(str_pad($baseKode, 3, 'X'));
        $kode = $baseKode;
        $counter = 1;

        // Pastikan unik di tabel model terkait
        while ($modelClass::where('kode', $kode)->exists()) {
            $counter++;
            $suffix = (string) $counter;
            $kode = substr($baseKode, 0, max(1, 3 - strlen($suffix))) . $suffix;
        }

        return $kode;
    }

    private function resolveMasterData(Request $request)
    {
        // 1. Jenis Pakaian
        $idJenisPakaian = $request->id_jenis_pakaian;
        if ($idJenisPakaian === '__NEW__' || !empty($request->nama_jenis_pakaian_baru)) {
            $nama = trim($request->nama_jenis_pakaian_baru ?? '');
            if (!empty($nama)) {
                $userKode = trim($request->kode_jenis_pakaian_baru ?? '');
                $kode = !empty($userKode) 
                    ? strtoupper($userKode) 
                    : $this->generateMasterKode($nama, MsJenisPakaian::class);

                $jenisObj = MsJenisPakaian::where('nama', $nama)
                    ->orWhere('nama_jenis', $nama)
                    ->first();

                if (!$jenisObj) {
                    $jenisObj = MsJenisPakaian::create([
                        'nama'       => $nama,
                        'nama_jenis' => $nama,
                        'kode'       => $kode,
                    ]);
                }
                $idJenisPakaian = $jenisObj->id;
            }
        }

        // 2. Warna
        $idWarna = $request->id_warna;
        if ($idWarna === '__NEW__' || !empty($request->nama_warna_baru)) {
            $nama = trim($request->nama_warna_baru ?? '');
            if (!empty($nama)) {
                $userKode = trim($request->kode_warna_baru ?? '');
                $kode = !empty($userKode) 
                    ? strtoupper($userKode) 
                    : $this->generateMasterKode($nama, MsWarna::class);

                $warnaObj = MsWarna::where('nama', $nama)
                    ->orWhere('nama_warna', $nama)
                    ->first();

                if (!$warnaObj) {
                    $warnaObj = MsWarna::create([
                        'nama'       => $nama,
                        'nama_warna' => $nama,
                        'kode'       => $kode,
                    ]);
                }
                $idWarna = $warnaObj->id;
            }
        }

        // 3. Model
        $idModel = $request->id_model;
        if ($idModel === '__NEW__' || !empty($request->nama_model_baru)) {
            $nama = trim($request->nama_model_baru ?? '');
            if (!empty($nama)) {
                $userKode = trim($request->kode_model_baru ?? '');
                $kode = !empty($userKode) 
                    ? strtoupper($userKode) 
                    : $this->generateMasterKode($nama, MsModel::class);

                $modelObj = MsModel::where('nama', $nama)
                    ->orWhere('nama_model', $nama)
                    ->first();

                if (!$modelObj) {
                    $modelObj = MsModel::create([
                        'nama'       => $nama,
                        'nama_model' => $nama,
                        'kode'       => $kode,
                    ]);
                }
                $idModel = $modelObj->id;
            }
        }

        return [
            'id_jenis_pakaian' => $idJenisPakaian,
            'id_warna'         => $idWarna,
            'id_model'         => $idModel,
        ];
    }

    // ============================================================
    // STORE
    // ============================================================
    public function store(Request $request)
    {
        try {
            // BERSIHKAN TITIK DARI VALUE RUPIAH SEBELUM VALIDASI
            if ($request->filled('harga_jual')) {
                $request->merge(['harga_jual' => str_replace('.', '', $request->harga_jual)]);
            }
            if ($request->filled('harga_beli_per_unit')) {
                $request->merge(['harga_beli_per_unit' => str_replace('.', '', $request->harga_beli_per_unit)]);
            }
            if ($request->filled('hpp_realisasi')) {
                $request->merge(['hpp_realisasi' => str_replace('.', '', $request->hpp_realisasi)]);
            } else {
                $request->merge(['hpp_realisasi' => null]);
            }

            // VALIDASI
            $request->validate([
                'nama_produk'             => 'required|string|max:150',
                'id_jenis_pakaian'        => 'required',
                'nama_jenis_pakaian_baru' => 'required_if:id_jenis_pakaian,__NEW__|nullable|string|max:100',
                'kode_jenis_pakaian_baru' => 'nullable|string|max:10',
                'id_warna'                => 'required',
                'nama_warna_baru'         => 'required_if:id_warna,__NEW__|nullable|string|max:100',
                'kode_warna_baru'         => 'nullable|string|max:10',
                'id_model'                => 'required',
                'nama_model_baru'         => 'required_if:id_model,__NEW__|nullable|string|max:100',
                'kode_model_baru'         => 'nullable|string|max:10',
                'harga_jual'              => 'required|numeric|min:0',
                'id_pemasok'              => 'required|exists:pemasok,id',
                'jumlah'                  => 'required|integer|min:1',
                'harga_beli_per_unit'     => 'required|numeric|min:0',
                'hpp_realisasi'           => 'nullable|numeric|min:0',
                'foto'                    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5000',
                'deskripsi'               => 'nullable|string',
            ]);

            DB::beginTransaction();

            // PROSES OTOMATIS TAMBAH/RESOLVE MASTER DATA (JENIS, WARNA, MODEL)
            $masterData = $this->resolveMasterData($request);

            // HITUNG HPP OTOMATIS
            $totalModal  = $request->jumlah * $request->harga_beli_per_unit;
            $hppOtomatis = $totalModal / $request->jumlah;

            // UPLOAD FOTO
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('produk', 'public');
            }

            // GENERATE KODE PRODUK OTOMATIS (TERJAMIN UNIK)
            $kodeProduk = Produk::generateKode('rebranding');

            // CREATE PRODUK
            $produk = Produk::create([
                'kode_produk'        => $kodeProduk,
                'nama_produk'        => $request->nama_produk,
                'id_jenis_pakaian'   => $masterData['id_jenis_pakaian'],
                'id_warna'           => $masterData['id_warna'],
                'id_model'           => $masterData['id_model'],
                'harga_jual'         => $request->harga_jual,
                'harga_beli_per_unit'=> $request->harga_beli_per_unit,
                'hpp_otomatis'       => $hppOtomatis,
                'hpp_realisasi'      => $request->filled('hpp_realisasi') ? $request->hpp_realisasi : $hppOtomatis,
                'stok'               => $request->jumlah,
                'id_pemasok'         => $request->id_pemasok,
                'foto'               => $fotoPath,
                'deskripsi'          => $request->deskripsi,
                'status'             => 'aktif',
            ]);

            // CREATE PEMBELIAN AWAL
            $jumlahAwal = (int) $request->jumlah;
            $hargaBeliUnit = (float) $request->harga_beli_per_unit;
            PembelianBarang::create([
                'id_produk'          => $produk->id,
                'id_pemasok'         => $request->id_pemasok,
                'qty'                => $jumlahAwal,
                'harga_beli_per_unit'=> $hargaBeliUnit,
                'tanggal_pembelian' => now()->toDateString(),
                'keterangan'        => 'Pembelian awal produk',
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

        // Hanya ambil produk dengan status AKTIF untuk navigasi Sebelumnya & Selanjutnya
        $prevProduk = Produk::where('status', 'aktif')->where('id', '<', $produk->id)->orderBy('id', 'desc')->first();
        $nextProduk = Produk::where('status', 'aktif')->where('id', '>', $produk->id)->orderBy('id', 'asc')->first();

        $riwayat = PembelianBarang::with('pemasok')
                    ->where('id_produk', $id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('produk.show', compact('produk', 'riwayat', 'prevProduk', 'nextProduk'));
    }

    // ============================================================
    // EDIT
    // ============================================================
    public function edit(string $id)
    {
        $produk = Produk::findOrFail($id);
        $pemasok = Pemasok::orderBy('nama_pemasok')->get();
        $jenisPakaian = MsJenisPakaian::orderBy('nama')->get();
        $warna = MsWarna::orderBy('nama')->get();
        $model = MsModel::orderBy('nama')->get();
        
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
            if ($request->filled('harga_jual')) {
                $request->merge(['harga_jual' => str_replace('.', '', $request->harga_jual)]);
            }
            if ($request->filled('harga_beli_per_unit')) {
                $request->merge(['harga_beli_per_unit' => str_replace('.', '', $request->harga_beli_per_unit)]);
            }
            if ($request->filled('hpp_realisasi')) {
                $request->merge(['hpp_realisasi' => str_replace('.', '', $request->hpp_realisasi)]);
            } else {
                $request->merge(['hpp_realisasi' => null]);
            }

            $request->validate([
                'nama_produk'             => 'required|string|max:150',
                'id_jenis_pakaian'        => 'required',
                'nama_jenis_pakaian_baru' => 'required_if:id_jenis_pakaian,__NEW__|nullable|string|max:100',
                'kode_jenis_pakaian_baru' => 'nullable|string|max:10',
                'id_warna'                => 'required',
                'nama_warna_baru'         => 'required_if:id_warna,__NEW__|nullable|string|max:100',
                'kode_warna_baru'         => 'nullable|string|max:10',
                'id_model'                => 'required',
                'nama_model_baru'         => 'required_if:id_model,__NEW__|nullable|string|max:100',
                'kode_model_baru'         => 'nullable|string|max:10',
                'harga_jual'              => 'required|numeric|min:0',
                'id_pemasok'              => 'required|exists:pemasok,id',
                'jenis_koreksi_stok'      => 'nullable|in:tetap,tambah,kurang,set_total',
                'qty_koreksi'             => 'nullable|integer|min:0',
                'stok_total_baru'         => 'nullable|integer|min:0',
                'qty_tambah'              => 'nullable|integer|min:0',
                'harga_beli_per_unit'     => 'nullable|numeric|min:0',
                'hpp_realisasi'           => 'nullable|numeric|min:0',
                'foto'                    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5000',
                'deskripsi'               => 'nullable|string',
                'status'                  => 'nullable|in:aktif,nonaktif',
            ]);

            DB::beginTransaction();

            // PROSES OTOMATIS TAMBAH/RESOLVE MASTER DATA (JENIS, WARNA, MODEL)
            $masterData = $this->resolveMasterData($request);

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
                $hargaBaru = (float) ($request->harga_beli_per_unit ?? $produk->harga_beli_per_unit);
                $idPemasok = $request->id_pemasok ?? $produk->id_pemasok;
                
                // Hitung HPP baru (rata-rata tertimbang)
                $nilaiStokLama = $stokLama * $produk->hpp_otomatis;
                $nilaiStokBaru = $qtyTambah * $hargaBaru;
                $hppBaruOtomatis = ($stokLama + $qtyTambah) > 0 ? ($nilaiStokLama + $nilaiStokBaru) / ($stokLama + $qtyTambah) : $produk->hpp_otomatis;
                
                $produk->hpp_otomatis = $hppBaruOtomatis;
                $produk->harga_beli_per_unit = $hargaBaru;
                
                PembelianBarang::create([
                    'id_produk'          => $produk->id,
                    'id_pemasok'         => $idPemasok,
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
            $produk->id_jenis_pakaian = $masterData['id_jenis_pakaian'];
            $produk->id_warna = $masterData['id_warna'];
            $produk->id_model = $masterData['id_model'];
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

            // PROSES UPLOAD FOTO BARU ATAU HAPUS FOTO LAMA
            if ($request->hasFile('foto')) {
                if ($produk->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($produk->foto)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($produk->foto);
                }
                $produk->foto = $request->file('foto')->store('produk', 'public');
            } elseif ($request->boolean('hapus_foto')) {
                if ($produk->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($produk->foto)) {
                if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
                    Storage::disk('public')->delete($produk->foto);
                }
                $fotoPath = null;
            }

            // Update stok jika ada perubahan stok manual
            $stokLama = (int) $produk->stok;
            $stokBaru = (int) $request->stok;
            $selisihStok = $stokBaru - $stokLama;
            $pesanStok = '';

            if ($selisihStok !== 0 && $cleanHargaBeliUnit !== null) {
                if ($selisihStok > 0) {
                    PembelianBarang::create([
                        'id_produk'           => $produk->id,
                        'id_pemasok'          => $request->id_pemasok,
                        'qty'                 => $selisihStok,
                        'harga_beli_per_unit' => $cleanHargaBeliUnit,
                        'tanggal_pembelian'  => now()->toDateString(),
                        'keterangan'         => 'Penambahan stok manual via Edit Produk',
                    ]);
                    $pesanStok = "Stok bertambah +{$selisihStok} unit.";
                } else {
                    $pesanStok = "Stok disesuaikan berkurang " . abs($selisihStok) . " unit.";
                }
            }

            $produk->update([
                'nama_produk'         => $request->nama_produk,
                'id_jenis_pakaian'    => $idJenis,
                'id_warna'            => $idWarna,
                'id_model'            => $idModel,
                'harga_jual'          => $cleanHargaJual,
                'harga_beli_per_unit' => $cleanHargaBeliUnit ?? $produk->harga_beli_per_unit,
                'hpp_realisasi'       => $cleanHppRealisasi,
                'stok'                => $stokBaru,
                'id_pemasok'          => $request->id_pemasok,
                'foto'                => $fotoPath,
                'deskripsi'           => $request->deskripsi,
                'status'              => $request->status,
            ]);

            DB::commit();

            $message = !empty($pesanStok) 
                ? "✅ Produk [{$produk->nama_produk}] berhasil diupdate! {$pesanStok}"
                : "✅ Data produk [{$produk->nama_produk}] berhasil diupdate!";

            Log::info('Produk updated', [
                'produk_id' => $produk->id,
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
    // DELETE (HAPUS PERMANEN / HAPUS TOTAL)
    // ============================================================
    public function destroy(string $id)
    {
        try {
            $produk = Produk::findOrFail($id);
            $nama = $produk->nama_produk;
            $kode = $produk->kode_produk;

            DB::beginTransaction();

            // Hapus record relasi terkait agar Hapus Total berjalan bersih tanpa Foreign Key Violation
            DB::table('pembelian_barang')->where('id_produk', $produk->id)->delete();
            DB::table('detail_transaksi_pos')->where('id_produk', $produk->id)->delete();
            DB::table('detail_pesanan_online')->where('id_produk', $produk->id)->delete();
            DB::table('retur')->where('id_produk', $produk->id)->delete();
            if (Schema::hasTable('katalog_live')) {
                DB::table('katalog_live')->where('id_produk', $produk->id)->delete();
            }

            // Hapus foto file jika ada
            if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
                Storage::disk('public')->delete($produk->foto);
            }

            // Hapus produk secara permanen dari database
            $produk->delete();

            DB::commit();

            Log::info('Produk deleted permanently', [
                'kode' => $kode,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('produk.index')
                             ->with('success', "✅ Produk [{$kode} - {$nama}] telah BERHASIL DIHAPUS TOTAL dari database!");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Produk delete failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', '❌ Gagal menghapus produk: ' . $e->getMessage());
        }
    }