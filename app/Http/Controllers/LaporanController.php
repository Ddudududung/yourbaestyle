<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPos;
use App\Models\PesananOnline;
use App\Models\Retur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $dari  = $request->dari  ?? now()->startOfMonth()->toDateString();
        $sampai = $request->sampai ?? now()->toDateString();

        // Total penjualan POS
        $penjualanPos = TransaksiPos::whereBetween(DB::raw('DATE(tanggal)'), [$dari, $sampai])
                                     ->sum('total_harga');
        $hppPos       = TransaksiPos::whereBetween(DB::raw('DATE(tanggal)'), [$dari, $sampai])
                                     ->sum('total_hpp');

        // Total penjualan online
        $penjualanOnline = PesananOnline::whereBetween(DB::raw('DATE(tanggal)'), [$dari, $sampai])
                                         ->sum('total_harga');
        $hppOnline       = PesananOnline::whereBetween(DB::raw('DATE(tanggal)'), [$dari, $sampai])
                                         ->sum('total_hpp');

        // Total kerugian retur
        $kerugianRetur = Retur::whereBetween('tanggal', [$dari, $sampai])
                               ->where('kondisi_barang', 'tidak_layak')
                               ->sum('nilai_kerugian');

        // Kalkulasi
        $totalPenjualan = $penjualanPos + $penjualanOnline;
        $totalHpp       = $hppPos + $hppOnline;
        $labaKotor      = $totalPenjualan - $totalHpp;
        $labaBersih     = $labaKotor - $kerugianRetur;

        // Rincian transaksi POS
        $transaksiPos = TransaksiPos::whereBetween(DB::raw('DATE(tanggal)'), [$dari, $sampai])
                                     ->with('user')
                                     ->orderBy('tanggal', 'desc')
                                     ->get();

        // Rincian pesanan online
        $pesananOnline = PesananOnline::whereBetween(DB::raw('DATE(tanggal)'), [$dari, $sampai])
                                       ->with('user')
                                       ->orderBy('tanggal', 'desc')
                                       ->get();

        return view('laporan.index', compact(
            'dari', 'sampai',
            'totalPenjualan', 'totalHpp',
            'labaKotor', 'kerugianRetur', 'labaBersih',
            'penjualanPos', 'penjualanOnline',
            'transaksiPos', 'pesananOnline'
        ));
    }

    // Unduh laporan sebagai PDF (menggunakan Barryvdh DomPDF)
    public function unduh(Request $request)
{
    // 1. Tangkap parameter tanggal (default: awal bulan sampai hari ini)
    $tgl_mulai = $request->input('tgl_mulai', date('Y-m-01'));
    $tgl_selesai = $request->input('tgl_selesai', date('Y-m-d'));

    // 2. Ambil data rincian dari Transaksi POS
    $pos = \App\Models\TransaksiPos::whereDate('tanggal', '>=', $tgl_mulai)
        ->whereDate('tanggal', '<=', $tgl_selesai)
        ->get()
        ->map(function ($item) {
            $item->platform = 'pos'; // Penanda untuk badge di PDF
            return $item;
        });

    // 3. Ambil data rincian dari Pesanan Online
    $online = \App\Models\PesananOnline::whereDate('tanggal', '>=', $tgl_mulai)
        ->whereDate('tanggal', '<=', $tgl_selesai)
        ->get();

    // 4. Gabungkan kedua data dan urutkan dari tanggal terbaru
    $data = $pos->concat($online)->sortByDesc('tanggal')->values();

    // 5. Hitung total omset dan HPP
    $total_omset = $data->sum('total_harga');
    $total_hpp = $data->sum('total_hpp');

    // 6. Kirim semua variabel ke View PDF (Baris 73 yang memicu error)
    $pdf = Pdf::loadView('laporan.pdf', compact(
        'data', 
        'total_omset', 
        'total_hpp', 
        'tgl_mulai', 
        'tgl_selesai'
    ));

    // 7. Download file PDF
    return $pdf->download('Laporan_Penjualan_' . $tgl_mulai . '_sd_' . $tgl_selesai . '.pdf');
}

    private function dataLaporan(string $dari, string $sampai): array
    {
        $penjualanPos    = TransaksiPos::whereBetween(DB::raw('DATE(tanggal)'), [$dari, $sampai])->sum('total_harga');
        $hppPos          = TransaksiPos::whereBetween(DB::raw('DATE(tanggal)'), [$dari, $sampai])->sum('total_hpp');
        $penjualanOnline = PesananOnline::whereBetween(DB::raw('DATE(tanggal)'), [$dari, $sampai])->sum('total_harga');
        $hppOnline       = PesananOnline::whereBetween(DB::raw('DATE(tanggal)'), [$dari, $sampai])->sum('total_hpp');
        $kerugianRetur   = Retur::whereBetween('tanggal', [$dari, $sampai])->where('kondisi_barang', 'tidak_layak')->sum('nilai_kerugian');
        $totalPenjualan  = $penjualanPos + $penjualanOnline;
        $totalHpp        = $hppPos + $hppOnline;
        $labaKotor       = $totalPenjualan - $totalHpp;
        $labaBersih      = $labaKotor - $kerugianRetur;

        return compact('totalPenjualan', 'totalHpp', 'labaKotor', 'kerugianRetur', 'labaBersih');
    }
}
