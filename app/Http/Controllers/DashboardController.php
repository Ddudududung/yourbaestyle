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
        $posHariIniCount = TransaksiPos::whereDate('tanggal', $hariIni)->count();
        $posHariIniOmset = TransaksiPos::whereDate('tanggal', $hariIni)->sum('total_harga');

        $onlineHariIniCount = PesananOnline::whereDate('tanggal', $hariIni)->count();
        $onlineHariIniOmset = PesananOnline::whereDate('tanggal', $hariIni)->sum('total_harga');

        $transaksiHariIni = $posHariIniCount + $onlineHariIniCount;
        $penjualanHariIni = $posHariIniOmset + $onlineHariIniOmset;

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

        // ========== SESI LIVE BREAKDOWN (Jika ada table sesi_live) ==========
        $sesiHariIni = [];
        try {
            $currentTime = Carbon::now()->format('H:i:s');
            $sesiHariIni = DB::table('sesi_live as sl')
                ->leftJoin('pesanan_online as po', 'sl.id', '=', 'po.id_sesi_live')
                ->select(
                    'sl.id',
                    'sl.nama_sesi',
                    'sl.jam_mulai',
                    'sl.jam_selesai',
                    'sl.status',
                    DB::raw('COUNT(DISTINCT po.id) as total_pesanan'),
                    DB::raw('COALESCE(SUM(po.total_harga), 0) as total_revenue'),
                    DB::raw('COALESCE(SUM(po.total_harga - po.total_hpp), 0) as total_profit')
                )
                ->whereDate('sl.tanggal_live', $hariIni)
                ->where(function ($q) use ($currentTime) {
                    $q->whereNull('sl.jam_selesai')
                      ->orWhere('sl.jam_selesai', '>=', $currentTime);
                })
                ->groupBy('sl.id', 'sl.nama_sesi', 'sl.jam_mulai', 'sl.jam_selesai', 'sl.status')
                ->orderBy('sl.jam_mulai')
                ->get();
        } catch (\Exception $e) {
            // Jika tabel sesi_live belum ada, skip
            $sesiHariIni = [];
        }

        // ========== STATISTIK 7 HARI TERAKHIR & KANAL PENJUALAN ==========
        $chartLabels = [];
        $chartDataPos = [];
        $chartDataOnline = [];

        for ($i = 6; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i);
            $chartLabels[] = $tgl->format('d M');

            $totalPos = TransaksiPos::whereDate('tanggal', $tgl)->sum('total_harga');
            $totalOnline = PesananOnline::whereDate('tanggal', $tgl)->sum('total_harga');

            $chartDataPos[] = (float) $totalPos;
            $chartDataOnline[] = (float) $totalOnline;
        }

        $totalPosOmset = array_sum($chartDataPos);
        $totalOnlineOmset = array_sum($chartDataOnline);
        $totalSemuaOmset = $totalPosOmset + $totalOnlineOmset;
        
        $persenPos = $totalSemuaOmset > 0 ? round(($totalPosOmset / $totalSemuaOmset) * 100) : 40;
        $persenOnline = $totalSemuaOmset > 0 ? round(($totalOnlineOmset / $totalSemuaOmset) * 100) : 60;

        $shopeeOmset = PesananOnline::where('platform', 'shopee')->sum('total_harga');
        $tiktokOmset = PesananOnline::where('platform', 'tiktok')->sum('total_harga');

        $hppPos7Hari = DB::table('detail_transaksi_pos as d')
            ->join('transaksi_pos as t', 'd.id_transaksi', '=', 't.id')
            ->whereDate('t.tanggal', '>=', Carbon::today()->subDays(6))
            ->sum(DB::raw('d.qty * d.hpp_satuan'));
            
        $hppOnline7Hari = DB::table('detail_pesanan_online as d')
            ->join('pesanan_online as p', 'd.id_pesanan', '=', 'p.id')
            ->whereDate('p.tanggal', '>=', Carbon::today()->subDays(6))
            ->sum(DB::raw('d.qty * d.hpp_satuan'));
            
        $totalHpp7Hari = (float) ($hppPos7Hari + $hppOnline7Hari);
        $labaKotor7Hari = (float) ($totalSemuaOmset - $totalHpp7Hari);

        // ========== KIRIM KE VIEW ==========
        return view('dashboard', compact(
            'transaksiHariIni',
            'penjualanHariIni',
            'posHariIniCount',
            'posHariIniOmset',
            'onlineHariIniCount',
            'onlineHariIniOmset',
            'totalStok',
            'totalHabis',
            'lowStockProducts',
            'recentTransactions',
            'sesiHariIni',
            'chartLabels',
            'chartDataPos',
            'chartDataOnline',
            'totalPosOmset',
            'totalOnlineOmset',
            'totalSemuaOmset',
            'shopeeOmset',
            'tiktokOmset',
            'totalHpp7Hari',
            'labaKotor7Hari',
            'persenPos',
            'persenOnline'
        ));
    }
}