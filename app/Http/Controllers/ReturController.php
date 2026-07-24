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
    // ============================================================
    // INDEX — Daftar semua retur
    // ============================================================
    public function index(Request $request)
    {
        $query = Retur::with(['pesananOnline', 'produk', 'user']);

        if ($request->filled('kondisi')) {
            $query->where('kondisi_barang', $request->kondisi);
        }
        
        if ($request->filled('search')) {
            $query->whereHas('pesananOnline', fn($q) =>
                $q->where('no_pesanan', 'like', '%' . $request->search . '%')
            );
        }

        $retur = $query->orderBy('tanggal', 'desc')->paginate(15);
        return view('retur.index', compact('retur'));
    }

    // ============================================================
    // SELECT TRANSAKSI — Halaman untuk pilih transaksi dulu
    // ============================================================
    public function selectTransaksi(Request $request)
    {
        $search = $request->input('search', '');
        
        // Ambil transaksi POS terbaru
        $pos = TransaksiPos::with('detail.produk')
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(fn($t) => (object)[
                'id' => $t->id,
                'type' => 'pos',
                'no_transaksi' => $t->kode_transaksi,
                'produk_nama' => $t->detail->first()?->produk?->nama_produk ?? 'Beragam Produk',
                'total_harga' => $t->total_harga,
                'tanggal' => $t->tanggal,
            ]);
        
        // Ambil transaksi Online terbaru
        $online = PesananOnline::with('detail.produk')
            ->where('status', '!=', 'cancelled')
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(fn($t) => (object)[
                'id' => $t->id,
                'type' => 'online',
                'no_transaksi' => $t->no_pesanan,
                'produk_nama' => $t->detail->first()?->produk?->nama_produk ?? 'Produk Online',
                'total_harga' => $t->total_harga,
                'tanggal' => $t->tanggal,
            ]);
        
        // Gabung dan urutkan
        $transaksis = $pos->concat($online)
            ->sortByDesc('tanggal')
            ->values();
        
        // Filter jika ada search
        if (!empty($search)) {
            $transaksis = $transaksis->filter(fn($t) => 
                stripos($t->no_transaksi, $search) !== false || 
                stripos($t->produk_nama, $search) !== false
            )->values();
        }
        
        return view('retur.select', compact('transaksis', 'search'));
    }

    // ============================================================
    // CREATE — Form retur (setelah pilih transaksi)
    // ============================================================
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
            $transaksi = PesananOnline::with('detail.produk')->find($transaksiId);
        } elseif ($transaksiType === 'pos') {
            $transaksi = TransaksiPos::with('detail.produk')->find($transaksiId);
        }
        
        if ($transaksi) {
            $detail = $transaksi->detail;
        } else {
            return redirect()->route('retur.select')
                            ->with('error', 'Transaksi tidak ditemukan');
        }
        
        return view('retur.create', compact('transaksi', 'transaksiType', 'detail'));
    }

    // ============================================================
    // SEARCH PRODUK BY BARCODE (AJAX untuk barcode scan)
    // ============================================================
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

    // ============================================================
    // STORE — Simpan retur
    // ============================================================
    public function store(Request $request)
    {
        $request->validate([
            'transaksi_type'        => 'required|in:online,pos',
            'transaksi_id'          => 'required|integer',
            'id_produk'             => 'required|exists:produk,id',
            'tanggal'               => 'required|date',
            'alasan'                => 'required|string',
            'kondisi_barang'        => 'required|in:layak_jual,tidak_layak',
            'qty'                   => 'required|integer|min:1',
            'ongkir_retur'          => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $produk = Produk::findOrFail($request->id_produk);
            $ongkir = $request->ongkir_retur ?? 0;
            $qty = $request->qty ?? 1;

            $nilaiKerugian = 0;
            if ($request->kondisi_barang === 'tidak_layak') {
                $nilaiKerugian = ($produk->hpp_otomatis * $qty) + $ongkir;
            }

            $idPesananOnline = ($request->transaksi_type === 'online') ? $request->transaksi_id : null;

            Retur::create([
                'id_pesanan_online' => $idPesananOnline,
                'id_produk'         => $request->id_produk,
                'id_user'           => Auth::id(),
                'tanggal'           => $request->tanggal,
                'alasan'            => $request->alasan,
                'kondisi_barang'    => $request->kondisi_barang,
                'ongkir_retur'      => $ongkir,
                'nilai_kerugian'    => $nilaiKerugian,
                'qty'               => $qty,
            ]);

            if ($request->kondisi_barang === 'layak_jual') {
                $produk->increment('stok', $qty);
            }

            DB::commit();
            return redirect()->route('retur.index')
                            ->with('success', 'Retur berhasil diproses!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ============================================================
    // SHOW — Detail retur
    // ============================================================
    public function show(string $id)
    {
        $retur = Retur::with(['pesananOnline', 'produk', 'user'])->findOrFail($id);
        return view('retur.show', compact('retur'));
    }
}