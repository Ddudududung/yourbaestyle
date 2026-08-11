<?php

namespace App\Http\Controllers;

use App\Models\KatalogLive;
use App\Models\Produk;
use App\Models\SesiLive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SesiLiveController extends Controller
{
    /**
     * Menampilkan halaman kerja Co-Host (Form Input + Daftar Mapping Hari Ini)
     */
    public function index(Request $request)
    {
        // Default tanggal adalah hari ini, atau sesuai filter yang dipilih
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $sesiSelected = $request->input('filter_sesi', null);

        // Ambil daftar Sesi Live pada tanggal tersebut untuk Dropdown Form Kiri
        $sesiLives = SesiLive::whereDate('tanggal_live', $tanggal)
                        ->orderBy('jam_mulai', 'asc')
                        ->get();

        // Query daftar mapping yang sudah dibuat
        $query = KatalogLive::with(['produk', 'sesiLive'])
                    ->whereDate('tanggal_live', $tanggal);

        // Jika user memfilter berdasarkan sesi tertentu di tabel kanan
        if ($sesiSelected) {
            $query->where('id_sesi_live', $sesiSelected);
        }

        $mapped = $query->orderBy('id', 'desc')->get();

        // Ambil produk aktif yang stoknya masih ada untuk Dropdown Produk
        $produks = Produk::where('status', 'aktif')
                    ->where('stok', '>', 0)
                    ->orderBy('nama_produk', 'asc')
                    ->get();

        return view('sesi_live.index', compact('mapped', 'produks', 'tanggal', 'sesiLives', 'sesiSelected'));
    }

    /**
     * Menyimpan mapping baru secara cepat saat Host sedang live
     */
    public function store(Request $request)
    {
        if ($request->filled('harga_live')) {
            $request->merge(['harga_live' => str_replace('.', '', $request->harga_live)]);
        } else {
            $request->merge(['harga_live' => null]);
        }

        $request->validate([
            'tanggal_live' => 'required|date',
            'id_sesi_live' => 'required|exists:sesi_live,id',
            'kode_live'    => 'required|string|max:50',
            'id_produk'    => 'required|exists:produk,id',
            'harga_live'   => 'nullable|numeric|min:0'
        ], [
            'id_sesi_live.required' => 'Silakan pilih Sesi/Jam Live terlebih dahulu!',
            'kode_live.required'    => 'Kode live (misal: FIX 10) wajib diisi!',
            'id_produk.required'    => 'Silakan pilih produk fisik terlebih dahulu!'
        ]);

        $produk = Produk::findOrFail($request->id_produk);
        $hargaLive = $request->filled('harga_live') ? $request->harga_live : $produk->harga_jual;

        // UpdateOrCreate sekarang berdasarkan KODE & ID SESI
        // Kode yang sama (misal: FIX 10) bisa dipakai di Sesi Pagi dan Sesi Malam tanpa bentrok!
        KatalogLive::updateOrCreate(
            [
                'kode_live'    => strtoupper(trim($request->kode_live)),
                'id_sesi_live' => $request->id_sesi_live
            ],
            [
                'tanggal_live' => $request->tanggal_live,
                'id_produk'    => $produk->id,
                'harga_live'   => $hargaLive
            ]
        );

        return redirect()->back()->with('success', '⚡ Kode [' . strtoupper($request->kode_live) . '] berhasil dipasangkan untuk sesi tersebut!');
    }

    /**
     * Menghapus mapping jika Co-Host salah ketik
     */
    public function destroy($id)
    {
        $item = KatalogLive::findOrFail($id);
        $kode = $item->kode_live;
        $item->delete();

        return redirect()->back()->with('success', 'Mapping kode [' . $kode . '] berhasil dihapus.');
    }
    /**
     * Membuat jadwal Sesi Live baru (Master Sesi) langsung dari halaman mapping
     */
   public function storeJadwal(Request $request)
{
    $request->validate([
        'nama_sesi'    => 'required|string|max:100',
        'platform'     => 'required|in:shopee,tiktok',
        'tanggal_live' => 'required|date',
        'jam_mulai'    => 'required',
        'jam_selesai'  => 'nullable',
    ]);

    $userId = Auth::id();
    
    if (!$userId) {
        return back()->withError('Anda harus login terlebih dahulu!');
    }

    try {
        \App\Models\SesiLive::create([
            'nama_sesi'    => $request->nama_sesi,
            'platform'     => $request->platform,
            'tanggal_live' => $request->tanggal_live,
            'jam_mulai'    => $request->jam_mulai,
            'jam_selesai'  => $request->jam_selesai,
            'status'       => 'ongoing',
            'created_by'   => $userId,
        ]);
        
        return redirect()->back()
            ->with('success', 'Jadwal Sesi Live [' . $request->nama_sesi . '] berhasil dibuat!');
            
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Sesi live creation failed', [
            'error' => $e->getMessage(),
            'user_id' => $userId,
        ]);
        
        return back()->withError('Gagal membuat jadwal: ' . $e->getMessage());
    }
}
    public function destroyJadwal($id)
    {
        $sesi = \App\Models\SesiLive::findOrFail($id);
        $namaSesi = $sesi->nama_sesi;
        
        // Hapus sesi dari database
        $sesi->delete();

        return redirect()->back()->with('success', '🗑️ Jadwal Sesi Live [' . $namaSesi . '] berhasil dihapus!');
    }
}