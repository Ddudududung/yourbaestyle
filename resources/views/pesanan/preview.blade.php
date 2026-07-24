@extends('layouts.app')

@section('title', 'Preview Import — Yourbaestyle')

@section('content')
<div class="container-fluid py-3" style="max-width: 1300px; margin: 0 auto;">
    
    <!-- 1. HEADER HALAMAN -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1 fw-bold text-dark" style="font-family: 'Quicksand', sans-serif;">
                <i class="bi bi-file-earmark-check-fill me-2" style="color: #EC95A8;"></i>Preview Import Pesanan
            </h4>
            <div class="d-flex align-items-center gap-2 flex-wrap" style="font-size: 13px;">
                <span class="text-muted">Platform: <strong class="text-dark">{{ strtoupper($platform) }}</strong></span>
                <span class="text-muted">•</span>
                <span class="text-muted">Format: <strong class="text-dark">{{ $formatCsv }}</strong></span>
            </div>
        </div>
        <div>
            <a href="{{ route('pesanan.import') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold">
                <i class="bi bi-arrow-left me-1"></i> Ganti File
            </a>
        </div>
    </div>

    <!-- 2. STATISTIK CARDS -->
    @php
        $berhasil = count(array_filter($parsed, fn($p) => $p['mapping_status'] === 'auto_found'));
        $gagalMapping = count(array_filter($parsed, fn($p) => $p['mapping_status'] !== 'auto_found'));
        $total = count($parsed);
    @endphp

    <div class="row g-2 g-md-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-4 border h-100 d-flex flex-column justify-content-between" style="background: #E8F7EE; border-color: #C3EEDB !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold text-success small"><i class="bi bi-check-circle-fill me-1"></i> Auto-Map</span>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-success" style="font-size: 1.6rem;">{{ $berhasil }}</h3>
                    <small class="text-success opacity-75" style="font-size: 11px;">Sudah ketemu produknya</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="p-3 rounded-4 border h-100 d-flex flex-column justify-content-between" style="background: #FFF9E6; border-color: #FFE699 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold text-warning-emphasis small"><i class="bi bi-exclamation-triangle-fill me-1"></i> Manual</span>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-warning-emphasis" style="font-size: 1.6rem;">{{ $gagalMapping }}</h3>
                    <small class="text-warning-emphasis opacity-75" style="font-size: 11px;">Perlu dipilih atau skip</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="p-3 rounded-4 border h-100 d-flex flex-column justify-content-between" style="background: #FFF5F7; border-color: #F7E5EA !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold small" style="color: #4D3D43;"><i class="bi bi-box-seam me-1" style="color: #EC95A8;"></i> Total Order</span>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold" style="color: #EC95A8; font-size: 1.6rem;">{{ $total }}</h3>
                    <small class="text-muted" style="font-size: 11px;">Total baris dari Excel</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="p-3 rounded-4 border h-100 d-flex flex-column justify-content-between" style="background: #E8F4F8; border-color: #B3E5FC !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold small" style="color: #0277BD;"><i class="bi bi-lightning-charge-fill me-1"></i> Ready</span>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold" style="color: #0277BD; font-size: 1.6rem;">{{ $berhasil > 0 ? '✓' : '–' }}</h3>
                    <small class="text-muted" style="font-size: 11px;">Siap dikonfirmasi</small>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. WARNING JIKA ADA YANG PERLU DIPILIH -->
    @if($gagalMapping > 0)
    <div class="alert rounded-4 mb-4 p-3 border-0 shadow-sm" style="background: #FFF9E6; color: #856404;">
        <div class="d-flex align-items-center gap-2 fw-bold mb-1">
            <i class="bi bi-info-circle-fill fs-5"></i> 
            <span>Ada {{ $gagalMapping }} Kode Variasi yang Perlu Dipilih</span>
        </div>
        <small class="d-block text-muted">Kode ini tidak ditemukan di katalog. Pilih produk yang sesuai dari dropdown, atau skip jika ingin resolve nanti.</small>
    </div>
    @endif

    <!-- 4. FORM IMPORT (PENTING: hidden input harus DALAM form) -->
    <form id="import-form" action="{{ route('pesanan.import.proses') }}" method="POST">
        @csrf
        
        <!-- 🆕 HIDDEN INPUT: Data pesanan mentah -->
        <input type="hidden" name="data_pesanan_mentah" value="{{ json_encode($parsed) }}">
        
        <!-- 🆕 HIDDEN INPUT: Mapping data dari dropdown (akan di-fill via JavaScript) -->
        <input type="hidden" name="mapping_data" id="mapping_data_input" value="{}">

        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #fff; overflow: hidden;">
            <div class="card-header bg-white py-3 px-4 border-bottom" style="border-color: #F7E5EA !important;">
                <h6 class="mb-0 fw-bold" style="color: #4D3D43;">
                    <i class="bi bi-gear-fill me-2" style="color: #EC95A8;"></i>Eksekusi Import
                </h6>
            </div>
            <div class="card-body p-3 p-md-4" style="background: #FAF8F9;">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-5">
                        <label class="form-label fw-bold mb-1 text-dark">Tanggal & Waktu Sesi Live <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal_live" class="form-control rounded-3" value="{{ date('Y-m-d\TH:i') }}" required>
                        <small class="text-muted" style="font-size: 11px;">Penting untuk cocokkan dengan katalog harian.</small>
                    </div>
                    
                    <div class="col-12 col-md-7 text-md-end mt-3 mt-md-0">
                        <button type="submit" class="btn w-100 w-md-auto rounded-pill px-4 py-2 fw-bold text-white shadow-sm" style="background: #EC95A8; font-size: 14.5px;">
                            <i class="bi bi-cloud-check-fill me-2"></i> Konfirmasi & Simpan {{ $total }} Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. TABEL DETAIL PREVIEW -->
        <div class="card border-0 shadow-sm rounded-4" style="background: #fff; overflow: hidden;">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center" style="border-color: #F7E5EA !important;">
                <h6 class="mb-0 fw-bold" style="color: #4D3D43;">
                    <i class="bi bi-list-check me-2" style="color: #EC95A8;"></i>Daftar Pratinjau Item ({{ $total }})
                </h6>
            </div>

            <div class="card-body p-0">
                <!-- TABEL DESKTOP -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                            <thead style="background-color: #FFF5F7; color: #7A686D; font-size: 11.5px; text-transform: uppercase;">
                                <tr>
                                    <th class="py-3 ps-4 border-0">No. Order</th>
                                    <th class="py-3 border-0">Pembeli</th>
                                    <th class="py-3 border-0">Kode Variasi</th>
                                    <th class="py-3 text-center border-0">Qty</th>
                                    <th class="py-3 text-end border-0">Total Harga</th>
                                    <th class="py-3 pe-4 text-end border-0">Mapping Produk</th>
                                </tr>
                            </thead>
                            <tbody style="border-top: 1px solid #F7E5EA;">
                                @foreach($parsed as $idx => $row)
                                <tr>
                                    <td class="ps-4">
                                        <code class="fw-bold" style="color: #EC95A8; background: #FFF0F3; padding: 4px 8px; border-radius: 6px;">{{ $row['no_pesanan'] ?? '-' }}</code>
                                    </td>
                                    <td class="fw-bold text-dark">{{ $row['nama_pembeli'] ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $row['variasi'] ?? '-' }}</span>
                                    </td>
                                    <td class="text-center fw-bold">{{ $row['qty'] ?? 1 }}</td>
                                    <td class="text-end fw-bold text-dark">Rp {{ number_format($row['total_harga'] ?? 0, 0, ',', '.') }}</td>
                                    
                                    <td class="pe-4">
                                        @if($row['mapping_status'] === 'auto_found')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-2" style="font-size: 11px;">
                                                <i class="bi bi-check-lg me-1"></i> {{ substr($row['nama_produk_auto'] ?? '', 0, 20) }}
                                            </span>
                                            <!-- Hidden input untuk auto_found -->
                                            <input type="hidden" class="mapping-auto" data-idx="{{ $idx }}" value="{{ $row['id_produk_auto'] }}">
                                        @else
                                            <select name="mapping_select[{{ $idx }}]" class="form-select form-select-sm rounded-2 mapping-select" data-idx="{{ $idx }}" style="font-size: 11px; max-width: 180px;">
                                                <option value="">— Pilih Produk —</option>
                                                @foreach($produkAktif as $prod)
                                                    <option value="{{ $prod->id }}">{{ substr($prod->nama_produk, 0, 25) }} (Stok: {{ $prod->stok }})</option>
                                                @endforeach
                                                <option value="skip" style="background-color: #FFE6E6; color: #dc3545;">▪ Skip Pesanan</option>
                                            </select>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- CARD MOBILE -->
                <div class="d-lg-none p-3" style="background: #FAF8F9;">
                    @foreach($parsed as $idx => $row)
                    <div class="card border-0 shadow-sm rounded-4 mb-3 p-3" style="background: #fff; border: 1px solid #F7E5EA !important;">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" style="border-color: #F7E5EA !important;">
                            <code class="fw-bold" style="color: #EC95A8; background: #FFF0F3; padding: 3px 8px; border-radius: 4px; font-size: 11px;">{{ $row['no_pesanan'] ?? '-' }}</code>
                            <span class="badge" style="font-size: 10px; background-color: {{ $row['mapping_status'] === 'auto_found' ? '#D4EDDA' : '#FFF3CD' }}; color: {{ $row['mapping_status'] === 'auto_found' ? '#155724' : '#856404' }};">
                                {{ $row['mapping_status'] === 'auto_found' ? '✓ Auto' : '⚠ Manual' }}
                            </span>
                        </div>

                        <div class="mb-2">
                            <span class="text-muted d-block" style="font-size: 11px;">Pembeli:</span>
                            <strong class="text-dark" style="font-size: 14px;">{{ $row['nama_pembeli'] ?? '-' }}</strong>
                        </div>

                        <div class="row g-2 p-2 rounded-3 text-center align-items-center mb-3" style="background: #FFF5F7; font-size: 12px;">
                            <div class="col-6 border-end" style="border-color: #FCEAEA !important;">
                                <span class="text-muted d-block" style="font-size: 10px;">Kode Variasi</span>
                                <strong class="text-dark">{{ $row['variasi'] ?? '-' }}</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 10px;">Jumlah × Harga</span>
                                <strong style="color: #EC95A8;">{{ $row['qty'] ?? 1 }} × Rp {{ number_format($row['total_harga'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        @if($row['mapping_status'] === 'auto_found')
                            <div class="p-2 rounded-3" style="background: #D4EDDA;">
                                <small class="text-success d-block" style="font-size: 10px;">✓ Sudah ter-map ke:</small>
                                <strong class="text-success" style="font-size: 12px;">{{ $row['nama_produk_auto'] ?? '-' }}</strong>
                            </div>
                            <input type="hidden" class="mapping-auto" data-idx="{{ $idx }}" value="{{ $row['id_produk_auto'] }}">
                        @else
                            <select name="mapping_select[{{ $idx }}]" class="form-select form-select-sm rounded-2 mapping-select" data-idx="{{ $idx }}" style="font-size: 11px;">
                                <option value="">— Pilih Produk atau Skip —</option>
                                @foreach($produkAktif as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->nama_produk }} (Stok: {{ $prod->stok }})</option>
                                @endforeach
                                <option value="skip" style="background-color: #FFE6E6; color: #dc3545;">▪ Skip Pesanan Ini</option>
                            </select>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </form>

</div>

<!-- JAVASCRIPT: Kumpulkan mapping sebelum submit -->
<script>
document.getElementById('import-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default submit dulu
    
    let mappingObj = {};
    
    // Collect dari auto-found (hidden inputs)
    document.querySelectorAll('input.mapping-auto').forEach(input => {
        const idx = input.getAttribute('data-idx');
        const value = input.value;
        if (idx && value) {
            mappingObj[idx] = value;
        }
    });
    
    // Collect dari dropdown pilihan
    document.querySelectorAll('select.mapping-select').forEach(select => {
        const idx = select.getAttribute('data-idx');
        const value = select.value;
        if (idx && value) {
            mappingObj[idx] = value;
        }
    });
    
    // Set ke hidden input
    document.getElementById('mapping_data_input').value = JSON.stringify(mappingObj);
    
    console.log('Mapping data:', mappingObj); // Debug log
    console.log('Form data:', {
        data_pesanan_mentah: document.querySelector('input[name="data_pesanan_mentah"]').value.substring(0, 50),
        mapping_data: document.getElementById('mapping_data_input').value,
        tanggal_live: document.querySelector('input[name="tanggal_live"]').value
    });
    
    // Submit form
    this.submit();
});
</script>

@endsection