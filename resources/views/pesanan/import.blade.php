@extends('layouts.app')

@section('title', 'Import Pesanan Omnichannel — Yourbaestyle')

@section('content')
<div class="container-fluid p-0" style="max-width: 720px; margin: 0 auto;">

    <!-- CARD CONTAINER UTAMA -->
    <div class="card-yb p-4">
        <!-- HEADER FORM -->
        <div class="d-flex align-items-center gap-2 mb-4 pb-3" style="border-bottom: 1.5px dashed var(--border-soft);">
            <div>
                <h5 class="fw-bold mb-1" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-cloud-arrow-up-fill me-2" style="color: var(--pink-primary);"></i>Import Pesanan Omnichannel
                </h5>
                <small class="text-muted font-semibold" style="font-size: 12px;">Unggah file pesanan dari marketplace untuk sinkronisasi otomatis dengan katalog toko</small>
            </div>
        </div>
        



        <form action="{{ route('pesanan.preview') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- STEP 1: PILIH PLATFORM -->
            <label class="form-label font-bold text-muted text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.5px;">1. Pilih Platform Marketplace</label>
            <div class="row g-2.5 mb-4">
                <div class="col-6">
                    <input type="radio" name="platform" value="shopee" id="platform_shopee" class="btn-check" checked>
                    <label for="platform_shopee" class="btn btn-yb-outline w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-1" style="border-radius: 16px;">
                        <span style="font-size: 24px;">🛒</span>
                        <strong class="font-semibold" style="font-size: 13.5px;">Shopee</strong>
                    </label>
                </div>
                <div class="col-6">
                    <input type="radio" name="platform" value="tiktok" id="platform_tiktok" class="btn-check">
                    <label for="platform_tiktok" class="btn btn-yb-outline w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-1" style="border-radius: 16px;">
                        <span style="font-size: 24px;">📱</span>
                        <strong class="font-semibold" style="font-size: 13.5px;">TikTok Live</strong>
                    </label>
                </div>
            </div>

            <!-- FORMAT FILE -->
            <div class="mb-4">
                <label class="form-label font-semibold text-muted mb-1" style="font-size: 12px;">Format File Excel <span class="text-danger">*</span></label>
                <select name="format_csv" class="form-select font-semibold" required style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px;">
                    <option value="shopee_standard">Shopee — Export Standar</option>
                    <option value="shopee_hemat_kargo">Shopee — Pesanan Hemat Kargo</option>
                    <option value="tiktok">TikTok Shop — Export Pesanan</option>
                </select>
            </div>

            <!-- PANDUAN UNDUH & TANGGAL LIVE -->
            <div class="p-3.5 mb-4" style="background: var(--pink-soft-2); border: 1.5px solid var(--border-soft); border-radius: 16px; color: var(--ink);">
                <div class="d-flex align-items-start gap-2 mb-3">
                    <i class="bi bi-info-circle-fill fs-5 mt-0.5" style="color: var(--pink-primary);"></i>
                    <div style="font-size: 12.5px;" class="font-semibold">
                        Unduh file Excel dari <strong>Seller Center / TikTok Shop</strong> pada menu <em>Pesanan → Export</em>, lalu pilih tanggal live di bawah ini:
                    </div>
                </div>
                <div>
                    <label class="form-label font-semibold mb-1" style="font-size: 11.5px; color: var(--ink-soft);">Pilih Tanggal Sesi Live <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_live" class="form-control font-semibold" required style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px; background: #ffffff;">
                </div>
            </div>

            <!-- STEP 2: UPLOAD FILE -->
            <label class="form-label font-bold text-muted text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.5px;">2. Upload File Excel</label>
            <div class="mb-4">
                <input type="file" name="file_csv" class="form-control font-semibold" accept=".xlsx,.xls,.csv" required style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px; padding: 10px 14px;">
                <div class="form-text font-semibold mt-1.5" style="font-size: 11.5px; color: var(--ink-soft);">Format pendukung: Excel (.xlsx, .xls, .csv) — Ukuran maksimal 5MB</div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="d-flex justify-content-end gap-2 pt-2" style="border-top: 1.5px dashed var(--border-soft);">
                <a href="{{ route('pesanan.index') }}" class="btn btn-yb-outline px-4 py-2 font-semibold text-decoration-none" style="border-radius: 12px; font-size: 13px;">Batal</a>
                <button type="submit" class="btn btn-yb px-4 py-2 font-semibold text-white shadow-sm" style="border-radius: 12px; font-size: 13px;">
                    <i class="bi bi-cloud-upload me-1.5"></i> Lihat Preview
                </button>
            </div>
        </form>
    </div>
</div>
@endsection