<?php

namespace App\Http\Controllers;

use App\Models\Retur;
use App\Models\Produk;
use App\Models\PesananOnline;
use App\Models\TransaksiPos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturController extends Controller
{
    /**
     * Daftar retur
     */
    public function index(Request $request)
    {
        $query = Retur::with(['pesananOnline', 'transaksiPos', 'produk', 'produkPengganti', 'user']);

        if ($request->filled('kondisi')) {
            $query->where('kondisi_barang', $request->kondisi);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('pesananOnline', fn($p) => $p->where('no_pesanan', 'like', "%{$search}%"))
                  ->orWhereHas('transaksiPos', fn($t) => $t->where('kode_transaksi', 'like', "%{$search}%"))
                  ->orWhereHas('produk', fn($prd) => $prd->where('nama_produk', 'like', "%{$search}%"));
            });
        }

        $retur = $query->orderBy('tanggal', 'desc')->paginate(15);
        return view('retur.index', compact('retur'));
    }

    /**
     * Pilih transaksi untuk retur (Mendukung Kasir POS & Pesanan Online)
     */
    public function selectTransaksi(Request $request)
    {
        $sesiLive = \App\Models\SesiLive::orderBy('tanggal_live', 'desc')->get();

        $typeFilter = $request->input('type');
        $search = trim($request->input('search', ''));
        $idSesiLive = $request->input('id_sesi_live');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $results = collect();

        // 1. Ambil data Transaksi POS (Offline)
        if (empty($typeFilter) || $typeFilter === 'pos') {
            $posQuery = TransaksiPos::with(['detail.produk', 'user']);

            if (!empty($search)) {
                $posQuery->where(function($q) use ($search) {
                    $q->where('kode_transaksi', 'like', "%{$search}%")
                      ->orWhereHas('detail.produk', fn($p) => $p->where('nama_produk', 'like', "%{$search}%"));
                });
            }
            if (!empty($tanggalMulai)) {
                $posQuery->whereDate('tanggal', '>=', $tanggalMulai);
            }
            if (!empty($tanggalSelesai)) {
                $posQuery->whereDate('tanggal', '<=', $tanggalSelesai);
            }

            $posList = $posQuery->orderBy('tanggal', 'desc')->get()->map(function($tx) {
                return (object)[
                    'id' => $tx->id,
                    'type' => 'pos',
                    'kode_transaksi' => $tx->kode_transaksi,
                    'nama_pembeli' => $tx->user->name ?? 'Kasir POS',
                    'tanggal' => $tx->tanggal,
                    'total_harga' => $tx->total_harga,
                    'detail' => $tx->detail,
                ];
            });
            $results = $results->concat($posList);
        }

        // 2. Ambil data Pesanan Online
        if (empty($typeFilter) || $typeFilter === 'online') {
            $onlineQuery = PesananOnline::with(['detail.produk', 'user', 'sesiLive']);

            if (!empty($search)) {
                $onlineQuery->where(function($q) use ($search) {
                    $q->where('no_pesanan', 'like', "%{$search}%")
                      ->orWhere('nama_pembeli', 'like', "%{$search}%")
                      ->orWhereHas('detail.produk', fn($p) => $p->where('nama_produk', 'like', "%{$search}%"));
                });
            }
            if (!empty($idSesiLive)) {
                $onlineQuery->where('id_sesi_live', $idSesiLive);
            }
            if (!empty($tanggalMulai)) {
                $onlineQuery->whereDate('tanggal', '>=', $tanggalMulai);
            }
            if (!empty($tanggalSelesai)) {
                $onlineQuery->whereDate('tanggal', '<=', $tanggalSelesai);
            }

            $onlineList = $onlineQuery->orderBy('tanggal', 'desc')->get()->map(function($tx) {
                return (object)[
                    'id' => $tx->id,
                    'type' => 'online',
                    'kode_transaksi' => $tx->no_pesanan,
                    'nama_pembeli' => $tx->nama_pembeli ?? ($tx->user->name ?? '-'),
                    'tanggal' => $tx->tanggal,
                    'total_harga' => $tx->total_harga,
                    'detail' => $tx->detail,
                ];
            });
            $results = $results->concat($onlineList);
        }

        // Urutkan gabungan transaksi berdasarkan tanggal terbaru
        $transaksis = $results->sortByDesc('tanggal')->values();

        return view('retur.select', compact('transaksis', 'sesiLive'));
    }

    /**
     * Form buat retur (Hitung sisa Qty yang dapat diretur)
     */
    public function create(Request $request)
    {
        $transaksiId = $request->query('transaksi_id');
        $transaksiType = $request->query('type', 'online');
        
        if (!$transaksiId) {
            return redirect()->route('retur.select')
                            ->with('error', 'Pilih transaksi terlebih dahulu');
        }
        
        $transaksi = null;
        $detail = null;
        
        if ($transaksiType === 'online') {
            $transaksi = PesananOnline::with(['detail.produk', 'user'])->find($transaksiId);
        } elseif ($transaksiType === 'pos') {
            $transaksi = TransaksiPos::with(['detail.produk', 'user'])->find($transaksiId);
        }
        
        if ($transaksi) {
            $detail = $transaksi->detail;
            foreach ($detail as $d) {
                // Hitung Qty yang sudah diretur sebelumnya untuk produk & transaksi ini
                $alreadyReturned = Retur::where($transaksiType === 'online' ? 'id_pesanan_online' : 'id_transaksi', $transaksiId)
                    ->where('id_produk', $d->id_produk)
                    ->sum('qty');
                
                $d->qty_retur_sebelumnya = $alreadyReturned;
                $d->sisa_retur = max(0, $d->qty - $alreadyReturned);
            }
        } else {
            return redirect()->route('retur.select')
                            ->with('error', 'Transaksi tidak ditemukan');
        }
        
        $semuaProduk = Produk::where('status', 'aktif')->orderBy('nama_produk')->get();
        return view('retur.create', compact('transaksi', 'transaksiType', 'detail', 'semuaProduk'));
    }

    /**
     * Cari produk berdasarkan barcode (AJAX)
     */
    public function searchByBarcode(Request $request)
    {
        $barcode = $request->input('barcode');
        
        $produk = Produk::where('kode_produk', $barcode)
                        ->orWhere('nama_produk', 'like', '%' . $barcode . '%')
                        ->first();
        
        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'produk' => [
                'id' => $produk->id,
                'nama_produk' => $produk->nama_produk,
                'kode_produk' => $produk->kode_produk,
                'harga_jual' => $produk->harga_jual,
                'hpp_otomatis' => $produk->hpp_otomatis,
                'stok' => $produk->stok,
            ]
        ]);
    }

    /**
     * Simpan transaksi retur (Mendukung Retur Multi-Item)
     */
    public function store(Request $request)
    {
        $request->validate([
            'transaksi_type' => 'required|in:online,pos',
            'transaksi_id'   => 'required|integer',
            'tanggal'        => 'required|date',
            'ongkir_retur'   => 'nullable|numeric|min:0',
        ]);

        $transaksiType = $request->transaksi_type;
        $transaksiId = (int)$request->transaksi_id;
        $ongkirGlobal = (float)($request->ongkir_retur ?? 0);

        // Input items dapat berupa array `items` atau format single bawaan
        $rawItems = $request->input('items', []);

        if (empty($rawItems) && $request->filled('id_produk')) {
            $rawItems = [
                [
                    'selected'             => '1',
                    'id_produk'           => $request->id_produk,
                    'qty'                 => $request->qty,
                    'alasan'              => $request->alasan,
                    'kondisi_barang'      => $request->kondisi_barang,
                    'tipe_retur'          => $request->tipe_retur,
                    'id_produk_pengganti' => $request->id_produk_pengganti,
                    'qty_pengganti'       => $request->qty_pengganti,
                ]
            ];
        }

        // Saring hanya item yang dicentang / diproses
        $itemsToProcess = [];
        foreach ($rawItems as $item) {
            if (!empty($item['selected']) || (isset($item['selected']) && $item['selected'] == '1')) {
                $itemsToProcess[] = $item;
            }
        }

        if (empty($itemsToProcess)) {
            return back()->withInput()->with('error', 'Silakan pilih dan centang minimal satu produk yang akan di-retur.');
        }

        DB::beginTransaction();
        try {
            // Ambil detail transaksi asli untuk validasi Qty
            $detailList = null;
            if ($transaksiType === 'online') {
                $txObj = PesananOnline::with('detail')->findOrFail($transaksiId);
                $detailList = $txObj->detail;
            } else {
                $txObj = TransaksiPos::with('detail')->findOrFail($transaksiId);
                $detailList = $txObj->detail;
            }

            $processedCount = 0;
            $totalPcsReturned = 0;

            foreach ($itemsToProcess as $idx => $item) {
                $idProduk = (int)($item['id_produk'] ?? 0);
                $qtyRetur = (int)($item['qty'] ?? 0);
                $alasan = $item['alasan'] ?? 'Lainnya';
                $kondisiBarang = $item['kondisi_barang'] ?? 'layak_jual';
                $tipeRetur = $item['tipe_retur'] ?? 'kembali_barang';
                $idProdukPengganti = !empty($item['id_produk_pengganti']) ? (int)$item['id_produk_pengganti'] : null;
                $qtyPengganti = (int)($item['qty_pengganti'] ?? $qtyRetur);

                if ($idProduk <= 0 || $qtyRetur <= 0) {
                    continue;
                }

                // Cari detail transaksi original
                $origDetail = $detailList->firstWhere('id_produk', $idProduk);
                if (!$origDetail) {
                    throw new \Exception("Produk ID {$idProduk} tidak ditemukan dalam detail transaksi ini.");
                }

                // Hitung Qty yang sudah diretur sebelumnya
                $alreadyReturned = Retur::where($transaksiType === 'online' ? 'id_pesanan_online' : 'id_transaksi', $transaksiId)
                    ->where('id_produk', $idProduk)
                    ->sum('qty');

                $sisaBolehRetur = max(0, $origDetail->qty - $alreadyReturned);

                if ($qtyRetur > $sisaBolehRetur) {
                    throw new \Exception("Jumlah retur untuk produk ID {$idProduk} ({$qtyRetur} pcs) melebihi sisa yang dapat diretur ({$sisaBolehRetur} pcs).");
                }

                // Lock record produk yang di-retur
                $produkRetur = Produk::where('id', $idProduk)->lockForUpdate()->firstOrFail();

                // Proses barang pengganti jika tipe tukar_barang
                if ($tipeRetur === 'tukar_barang') {
                    if (!$idProdukPengganti) {
                        throw new \Exception("Produk pengganti wajib dipilih untuk tukar barang pada [{$produkRetur->nama_produk}].");
                    }

                    $produkPengganti = Produk::where('id', $idProdukPengganti)->lockForUpdate()->firstOrFail();
                    if ($produkPengganti->stok < $qtyPengganti) {
                        throw new \Exception("Stok barang pengganti [{$produkPengganti->nama_produk}] tidak mencukupi (Tersedia: {$produkPengganti->stok} pcs, dibutuhkan: {$qtyPengganti} pcs).");
                    }

                    $produkPengganti->decrement('stok', $qtyPengganti);
                } else {
                    $idProdukPengganti = null;
                    $qtyPengganti = 0;
                }

                // Alokasikan ongkir ke item pertama
                $itemOngkir = ($idx === 0) ? $ongkirGlobal : 0;

                // Hitung nilai kerugian jika tidak_layak
                $nilaiKerugian = 0;
                if ($kondisiBarang === 'tidak_layak') {
                    $nilaiKerugian = ($produkRetur->hpp_aktif * $qtyRetur) + $itemOngkir;
                }

                // Kembalikan stok inventaris jika layak_jual
                if ($kondisiBarang === 'layak_jual') {
                    $produkRetur->increment('stok', $qtyRetur);
                }

                // Simpan data retur
                Retur::create([
                    'id_pesanan_online'   => ($transaksiType === 'online') ? $transaksiId : null,
                    'id_transaksi'        => ($transaksiType === 'pos') ? $transaksiId : null,
                    'id_produk'           => $idProduk,
                    'id_user'             => Auth::id(),
                    'tanggal'             => $request->tanggal,
                    'alasan'              => $alasan,
                    'kondisi_barang'      => $kondisiBarang,
                    'ongkir_retur'        => $itemOngkir,
                    'nilai_kerugian'      => $nilaiKerugian,
                    'qty'                 => $qtyRetur,
                    'tipe_retur'          => $tipeRetur,
                    'id_produk_pengganti' => $idProdukPengganti,
                    'qty_pengganti'       => $qtyPengganti,
                ]);

                $processedCount++;
                $totalPcsReturned += $qtyRetur;
            }

            if ($processedCount === 0) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Tidak ada item retur valid yang diproses.');
            }

            DB::commit();

            return redirect()->route('retur.index')->with('success', "✅ Retur berhasil diproses untuk {$processedCount} item produk ({$totalPcsReturned} pcs)!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memproses retur: ' . $e->getMessage());
        }
    }

    /**
     * Detail retur
     */
    public function show(string $id)
    {
        $retur = Retur::with(['pesananOnline', 'transaksiPos', 'produk', 'produkPengganti', 'user'])->findOrFail($id);
        return view('retur.show', compact('retur'));
    }
}