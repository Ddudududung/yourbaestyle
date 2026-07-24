<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\TransaksiPos;
use App\Models\PesananOnline;
use App\Models\SesiLive;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today();

        // ========== STATISTIK KARTU ATAS ==========
        $transaksiHariIni = TransaksiPos::whereDate('tanggal', $hariIni)->count() 
                          + PesananOnline::whereDate('tanggal', $hariIni)->count();
                          
        $penjualanHariIni = TransaksiPos::whereDate('tanggal', $hariIni)->sum('total_harga') 
                          + PesananOnline::whereDate('tanggal', $hariIni)->sum('total_harga');

        $totalStok  = Produk::where('status', 'aktif')->sum('stok');
        $totalHabis = Produk::where('status', 'aktif')->where('stok', 0)->count();

        // ========== PRODUK STOK RENDAH ==========
        $lowStockProducts = Produk::where('status', 'aktif')
                            ->where('stok', '<=', 5)
                            ->orderBy('stok', 'asc')
                            ->take(5)
                            ->get();

        // ========== TRANSAKSI TERAKHIR (GABUNG POS & ONLINE) ==========
        
        // A. Transaksi POS
        $pos = TransaksiPos::with(['user', 'detail.produk'])
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'no_transaksi' => $item->kode_transaksi ?? '-',
                    'produk_nama'  => $item->detail->first()?->produk?->nama_produk ?? 'Beragam Produk POS',
                    'qty'          => $item->detail->sum('qty') ?: 1,
                    'total'        => $item->total_harga,
                    'created_at'   => Carbon::parse($item->tanggal),
                    'user_name'    => $item->user?->name ?? 'Kasir POS',
                ];
            });

        // B. Transaksi Online
        $online = PesananOnline::with(['user', 'detail.produk'])
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $namaProduk = $item->detail->first()?->produk?->nama_produk 
                           ?? ($item->detail->first()?->variasi ?? 'Pesanan Online');
                
                return (object) [
                    'no_transaksi' => $item->no_pesanan,
                    'produk_nama'  => $namaProduk,
                    'qty'          => $item->detail->sum('qty') ?: 1,
                    'total'        => $item->total_harga,
                    'created_at'   => Carbon::parse($item->tanggal),
                    'user_name'    => strtoupper($item->platform ?? 'ONLINE'),
                ];
            });

        // C. Gabung & urutkan
        $recentTransactions = $pos->concat($online)
            ->sortByDesc('created_at')
            ->take(6)
            ->values();

        // ========== SESI LIVE BREAKDOWN (Jika ada table sesi_lives) ==========
        $sesiHariIni = [];
        try {
            $sesiHariIni = DB::table('sesi_lives as sl')
                ->leftJoin('pesanan_online as po', 'sl.id', '=', 'po.id_sesi_live')
                ->select(
                    'sl.id',
                    'sl.nama_sesi',
                    'sl.jam_mulai',
                    'sl.status',
                    DB::raw('COUNT(DISTINCT po.id) as total_pesanan'),
                    DB::raw('COALESCE(SUM(po.total_harga), 0) as total_revenue'),
                    DB::raw('COALESCE(SUM(po.total_harga - po.total_hpp), 0) as total_profit')
                )
                ->whereDate('sl.tanggal_live', $hariIni)
                ->groupBy('sl.id', 'sl.nama_sesi', 'sl.jam_mulai', 'sl.status')
                ->orderBy('sl.jam_mulai')
                ->get();
        } catch (\Exception $e) {
            // Jika tabel sesi_lives belum ada, skip
            $sesiHariIni = [];
        }

        // ========== KIRIM KE VIEW ==========
        return view('dashboard', compact(
            'transaksiHariIni',
            'penjualanHariIni',
            'totalStok',
            'totalHabis',
            'lowStockProducts',
            'recentTransactions',
            'sesiHariIni'
        ));
    }
}