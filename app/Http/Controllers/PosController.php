<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\TransaksiPos;
use App\Models\DetailTransaksiPos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    // ============================================================
    // INDEX — Halaman kasir POS
    // ============================================================
    public function index()
    {
        $produk = Produk::where('status', 'aktif')
                         ->orderBy('nama_produk')
                         ->get();

        return view('pos.index', compact('produk'));
    }

    // ============================================================
    // PROSES — Simpan transaksi POS
    // Menerima JSON: { items: [{id_produk, qty}], metode_bayar, diskon }
    // ============================================================
    public function proses(Request $request)
    {
        $request->validate([
            'items'               => 'required|array|min:1',
            'items.*.id_produk'   => 'required|exists:produk,id',
            'items.*.qty'         => 'required|integer|min:1',
            'metode_bayar'        => 'required|in:tunai,transfer,qris',
            'diskon'              => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalHarga = 0;
            $totalHpp   = 0;
            $details    = [];

            foreach ($request->items as $item) {
                $produk = Produk::where('id', $item['id_produk'])->lockForUpdate()->firstOrFail();

                // Cek stok
                if ($produk->stok < $item['qty']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$produk->nama_produk} tidak mencukupi. Stok tersedia: {$produk->stok}",
                    ], 422);
                }

                $subtotal    = $produk->harga_jual * $item['qty'];
                // Snapshot HPP saat transaksi (pakai hpp_realisasi jika ada)
                $hppSatuan   = $produk->hpp_aktif;
                $subtotalHpp = $hppSatuan * $item['qty'];

                $totalHarga += $subtotal;
                $totalHpp   += $subtotalHpp;

                $details[] = [
                    'id_produk'   => $produk->id,
                    'qty'         => $item['qty'],
                    'harga_satuan'=> $produk->harga_jual,
                    'hpp_satuan'  => $hppSatuan,
                ];

                // Kurangi stok
                $produk->decrement('stok', $item['qty']);
            }

            $diskon     = $request->diskon ?? 0;
            if ($diskon > $totalHarga) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Diskon tidak boleh melebihi total harga transaksi.',
                ], 422);
            }
            $totalAkhir = $totalHarga - $diskon;

            // Buat transaksi induk
            $transaksi = TransaksiPos::create([
                'kode_transaksi' => TransaksiPos::generateKode(),
                'id_user'        => Auth::id(),
                'tanggal'        => now(),
                'total_harga'    => $totalAkhir,
                'total_hpp'      => $totalHpp,
                'diskon'         => $diskon,
                'metode_bayar'   => $request->metode_bayar,
            ]);

            // Simpan detail
            foreach ($details as $detail) {
                DetailTransaksiPos::create(array_merge(
                    $detail,
                    ['id_transaksi' => $transaksi->id]
                ));
            }

            DB::commit();

            return response()->json([
                'success'         => true,
                'message'         => 'Transaksi berhasil disimpan.',
                'kode_transaksi'  => $transaksi->kode_transaksi,
                'total'           => $totalAkhir,
                'transaksi_id'    => $transaksi->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ============================================================
    // RIWAYAT — Daftar semua transaksi POS
    // ============================================================
    public function riwayat(Request $request)
    {
        $query = TransaksiPos::with('user');

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        }
        if ($request->filled('search')) {
            $query->where('kode_transaksi', 'like', '%' . $request->search . '%');
        }

        $transaksi = $query->orderBy('tanggal', 'desc')->paginate(15);

        return view('pos.riwayat', compact('transaksi'));
    }

    // ============================================================
    // SHOW — Detail satu transaksi (untuk cetak struk)
    // ============================================================
    public function show(string $id)
    {
        $transaksi = TransaksiPos::with(['user', 'detail.produk'])->findOrFail($id);
        return view('pos.show', compact('transaksi'));
    }
}
