<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PesananOnlineController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\SesiLiveController;
use App\Http\Controllers\KatalogController;


Route::get('/', [KatalogController::class, 'index'])->name('katalog.publik');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/transaksi',      [PosController::class, 'riwayat'])->name('transaksi.index');
    Route::get('/transaksi/{id}', [PosController::class, 'show'])->name('transaksi.show');
    Route::get('/pos/{id}',       [PosController::class, 'show'])->name('pos.detail');
});

Route::middleware(['auth', 'menu.access'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Barang & HPP
    Route::get('/produk/hpp',        [ProdukController::class, 'hppIndex'])->name('produk.hpp');
    Route::put('/produk/{id}/hpp',   [ProdukController::class, 'hppUpdate'])->name('produk.hpp.update');
    Route::resource('/produk', ProdukController::class);

    // Pemasok
    Route::resource('/pemasok', PemasokController::class);

    // Point of Sale (Kasir)
    Route::get('/pos',               [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos',              [PosController::class, 'proses'])->name('pos.proses');

    // Mapping Sesi Live (Co-Host)
    Route::get('/sesi-live',         [SesiLiveController::class, 'index'])->name('sesi_live.index');
    Route::post('/sesi-live',        [SesiLiveController::class, 'store'])->name('sesi_live.store');
    Route::delete('/sesi-live/{id}', [SesiLiveController::class, 'destroy'])->name('sesi_live.destroy');

    // Pesanan Online & Import Excel
    Route::get('/pesanan',                        [PesananOnlineController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/import',                 [PesananOnlineController::class, 'importForm'])->name('pesanan.import');
    Route::post('/pesanan/preview',               [PesananOnlineController::class, 'preview'])->name('pesanan.preview');
    Route::get('/pesanan/preview', fn() => redirect()->route('pesanan.import'));
    Route::post('/pesanan/import/proses',         [PesananOnlineController::class, 'importProses'])->name('pesanan.import.proses');
    
    // Review & Mapping Manual Pesanan Online
    Route::get('/pesanan/review-manual',          [PesananOnlineController::class, 'reviewManual'])->name('pesanan.review_manual');
    Route::patch('/pesanan/review-manual/{id}/resolve', [PesananOnlineController::class, 'resolveManual'])->name('pesanan.resolve_manual');
    Route::get('/pesanan/{id}',                   [PesananOnlineController::class, 'show'])->name('pesanan.show');
    
    // Mapping Sesi Live (Co-Host)
    Route::get('/sesi-live', [SesiLiveController::class, 'index'])->name('sesi_live.index');
    Route::post('/sesi-live', [SesiLiveController::class, 'store'])->name('sesi_live.store');
    Route::post('/sesi-live/jadwal', [SesiLiveController::class, 'storeJadwal'])->name('sesi_live.store_jadwal');
    Route::delete('/sesi-live/jadwal/{id}', [SesiLiveController::class, 'destroyJadwal'])->name('sesi_live.destroy_jadwal');
    Route::delete('/sesi-live/{id}', [SesiLiveController::class, 'destroy'])->name('sesi_live.destroy');

    // Retur Barang
    Route::get('/retur/select', [ReturController::class, 'selectTransaksi'])->name('retur.select');
    Route::get('/retur/search-barcode', [ReturController::class, 'searchByBarcode']);
    Route::resource('/retur', ReturController::class);
    Route::get('/retur/cari',        [ReturController::class, 'cariPesanan'])->name('retur.cari');
    
    // Laporan
    Route::get('/laporan',             [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/unduh',       [LaporanController::class, 'unduh'])->name('laporan.unduh');
    Route::get('/laporan/unduh-excel', [LaporanController::class, 'unduhExcel'])->name('laporan.unduh_excel');

    // Pengaturan Sistem (Khusus Superadmin)
    Route::middleware('role:owner')->group(function () {
        Route::get('/pengaturan/role',       [PengaturanController::class, 'roleIndex'])->name('pengaturan.role');
        Route::post('/pengaturan/role',      [PengaturanController::class, 'roleStore'])->name('pengaturan.role.store');
        Route::put('/pengaturan/role/{id}',  [PengaturanController::class, 'roleUpdate'])->name('pengaturan.role.update');
        
        Route::get('/pengaturan/user',       [PengaturanController::class, 'userIndex'])->name('pengaturan.user');
        Route::post('/pengaturan/user',      [PengaturanController::class, 'userStore'])->name('pengaturan.user.store');
        Route::put('/pengaturan/user/{id}',  [PengaturanController::class, 'userUpdate'])->name('pengaturan.user.update');
    });
});