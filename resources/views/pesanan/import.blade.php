@extends('layouts.app')

@section('title', 'Import Pesanan Omnichannel ')

@section('content')

<div class="card-yb p-4" style="max-width:680px">
    <h6 class="fw-bold mb-4" style="font-family:'Quicksand',sans-serif">Import Pesanan Omnichannel</h6>
    
    {{-- ✅ ERROR HANDLING - IMPROVED --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex gap-2">
                <i class="bi bi-exclamation-circle-fill fs-5" style="flex-shrink: 0; margin-top: 2px;"></i>
                <div style="flex: 1;">
                    <strong>Error Import File</strong>
                    <p class="mb-0 mt-1">{{ session('error') }}</p>
                    
                    {{-- Tampilkan error details kalau ada --}}
                    @if(session('error_details'))
                        <details class="mt-2 pt-2 border-top" style="font-size: 12px;">
                            <summary style="cursor: pointer; color: #666;">
                                <u>Lihat detail error</u>
                            </summary>
                            <pre style="margin-top: 10px; white-space: pre-wrap; background: rgba(0,0,0,0.05); padding: 10px; border-radius: 4px;">{{ session('error_details') }}</pre>
                        </details>
                    @endif
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ✅ SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ✅ VALIDATION ERRORS --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-1"></i>Format File Tidak Valid</strong>
            <ul class="mb-0 mt-2" style="font-size: 14px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('pesanan.preview') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <p class="text-secondary small fw-bold text-uppercase mb-3">1. Pilih Platform</p>
        <div class="row g-2 mb-3">
            <div class="col-6">
                <input type="radio" name="platform" value="shopee" id="platform_shopee" class="btn-check" checked>
                <label for="platform_shopee" class="btn btn-yb-outline w-100 py-3">🛒<br><strong>Shopee</strong></label>
            </div>
            <div class="col-6">
                <input type="radio" name="platform" value="tiktok" id="platform_tiktok" class="btn-check">
                <label for="platform_tiktok" class="btn btn-yb-outline w-100 py-3">📱<br><strong>TikTok Live</strong></label>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Format File Excel <span class="text-danger">*</span></label>
            <select name="format_csv" class="form-select" required>
                <option value="shopee_standard">Shopee — Export Standar</option>
                <option value="shopee_hemat_kargo">Shopee — Pesanan Hemat Kargo</option>
                <option value="tiktok">TikTok Shop — Export Pesanan</option>
            </select>
        </div>

        <div class="alert alert-info">
            Unduh file Excel dari Seller Center / TikTok Shop pada menu Pesanan → Export, lalu upload di sini.
            <label>Pilih Tanggal Live:</label>
            <input type="date" name="tanggal_live" class="form-control" required>
        </div>

        <p class="text-secondary small fw-bold text-uppercase mb-3 mt-3">2. Upload File Excel</p>
        <div class="mb-4">
            <input type="file" name="file_csv" class="form-control" accept=".xlsx,.xls,.csv" required>
            <div class="form-text fw-semibold">Format: Excel — maksimal 5MB</div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('pesanan.index') }}" class="btn btn-yb-outline">Batal</a>
            <button type="submit" class="btn btn-yb"><i class="bi bi-cloud-upload"></i> Lihat Preview</button>
        </div>
    </form>
</div>

@endsection