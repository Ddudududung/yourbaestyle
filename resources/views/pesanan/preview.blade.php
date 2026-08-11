@extends('layouts.app')

@section('title', 'Preview Import — Yourbaestyle')

@section('content')
<div class="container-fluid p-0" style="max-width: 1250px; margin: 0 auto;">
    
    <!-- 1. HEADER HALAMAN -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1 fw-bold" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                <i class="bi bi-file-earmark-check-fill me-2" style="color: var(--pink-primary);"></i>Preview Import Pesanan
            </h4>
            <div class="d-flex align-items-center gap-2 flex-wrap font-semibold" style="font-size: 12.5px;">
                <span class="text-muted">Platform: <strong style="color: var(--ink);">{{ strtoupper($platform) }}</strong></span>
                <span class="text-muted">•</span>
                <span class="text-muted">Format: <strong style="color: var(--ink);">{{ $formatCsv }}</strong></span>
            </div>
        </div>
        <div>
            <a href="{{ route('pesanan.import') }}" class="btn btn-yb-outline px-3 py-2 text-decoration-none font-semibold" style="border-radius: 12px; font-size: 12.5px;">
                <i class="bi bi-arrow-left me-1"></i> Ganti File
            </a>
        </div>
        @php
        // Tambahkan pengecekan is_array() dan isset() agar halaman tidak crash jika data kosong/error
        $berhasil = count(array_filter($parsed, fn($p) => is_array($p) && isset($p['mapping_status']) && $p['mapping_status'] === 'auto_found'));
        $gagalMapping = count(array_filter($parsed, fn($p) => is_array($p) && isset($p['mapping_status']) && $p['mapping_status'] !== 'auto_found'));
        $total = is_array($parsed) ? count($parsed) : 0;
    @endphp
    </div>

    <!-- 2. STATISTIK CARDS -->
    @php
        $berhasil = count(array_filter($parsed, fn($p) => $p['mapping_status'] === 'auto_found'));
        $gagalMapping = count(array_filter($parsed, fn($p) => $p['mapping_status'] !== 'auto_found'));
        $total = count($parsed);
    @endphp

    <div class="row g-2.5 g-md-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="p-3.5 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: #E8F7EE; border: 1.5px solid #C3EEDB;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold text-success small"><i class="bi bi-check-circle-fill me-1"></i> Auto-Map</span>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-success" style="font-size: 1.6rem;">{{ $berhasil }}</h3>
                    <small class="text-success font-semibold opacity-85" style="font-size: 11px;">Sudah ketemu produknya</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="p-3.5 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: #FFF9E6; border: 1.5px solid #FFE699;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold text-warning-emphasis small"><i class="bi bi-exclamation-triangle-fill me-1"></i> Manual</span>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-warning-emphasis" style="font-size: 1.6rem;">{{ $gagalMapping }}</h3>
                    <small class="text-warning-emphasis font-semibold opacity-85" style="font-size: 11px;">Perlu dipilih atau skip</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="p-3.5 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: var(--pink-soft-2); border: 1.5px solid var(--border-soft);">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold small" style="color: var(--ink);"><i class="bi bi-box-seam me-1" style="color: var(--pink-primary);"></i> Total Order</span>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold" style="color: var(--pink-primary-dark); font-size: 1.6rem;">{{ $total }}</h3>
                    <small class="text-muted font-semibold" style="font-size: 11px;">Total baris dari Excel</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="p-3.5 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: #FAF0F7; border: 1.5px solid #EBC6E3;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold small" style="color: #7C355A;"><i class="bi bi-lightning-charge-fill me-1"></i> Ready</span>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold" style="color: #7C355A; font-size: 1.6rem;">{{ $berhasil > 0 ? '✓' : '–' }}</h3>
                    <small class="text-muted font-semibold" style="font-size: 11px;">Siap dikonfirmasi</small>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. WARNING JIKA ADA YANG PERLU DIPILIH -->
    @if($gagalMapping > 0)
    <div class="p-3.5 mb-4 d-flex align-items-start gap-2.5" style="background: #FFF7F2; border: 1.5px solid #F8D6C2; border-radius: 16px; color: #6E3B1C;">
        <i class="bi bi-exclamation-triangle-fill fs-5 mt-0.5" style="color: #EE9A68; flex-shrink: 0;"></i>
        <div>
            <strong style="font-size: 13.5px;" class="d-block mb-0.5">Ada {{ $gagalMapping }} Kode Variasi yang Perlu Dipilih</strong>
            <small class="font-semibold text-muted" style="font-size: 12px;">Kode variasi ini tidak ditemukan di katalog. Pilih produk yang sesuai dari dropdown, atau pilih skip jika ingin diproses nanti.</small>
        </div>
    </div>
    @endif

    <!-- 4. FORM IMPORT -->
    <form id="import-form" action="{{ route('pesanan.import.proses') }}" method="POST">
        @csrf
        
        <!-- HIDDEN INPUTS -->
        <input type="hidden" name="data_pesanan_mentah" value="{{ json_encode($parsed) }}">
        <input type="hidden" name="mapping_data" id="mapping_data_input" value="{}">

        <!-- EKSEKUSI IMPORT PANEL -->
        <div class="card-yb p-3 p-md-4 mb-4">
            <div class="d-flex align-items-center gap-2 mb-3 pb-2.5" style="border-bottom: 1.5px dashed var(--border-soft);">
                <h6 class="mb-0 fw-bold" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-gear-fill me-2" style="color: var(--pink-primary);"></i>Eksekusi Import
                </h6>
            </div>
            
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label font-semibold text-muted mb-1" style="font-size: 12px;">Tanggal & Waktu Sesi Live <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="tanggal_live" class="form-control font-semibold" value="{{ date('Y-m-d\TH:i') }}" required style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px;">
                    <small class="text-muted font-semibold mt-1 d-block" style="font-size: 11px;">Penting untuk mencocokkan pesanan dengan katalog harian.</small>
                </div>
                
                <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                    <button type="submit" class="btn btn-yb px-4 py-2.5 font-semibold text-white shadow-sm w-100 w-md-auto" style="border-radius: 12px; font-size: 13.5px;">
                        <i class="bi bi-cloud-check-fill me-1.5"></i> Konfirmasi & Simpan {{ $total }} Pesanan
                    </button>
                </div>
            </div>
        </div>

        <!-- 5. TABEL DETAIL PREVIEW -->
        <div class="card-yb p-0 overflow-hidden">
            <div class="p-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1.5px dashed var(--border-soft); background: var(--pink-soft-2);">
                <h6 class="fw-bold mb-0" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-list-stars me-2" style="color: var(--pink-primary);"></i>Daftar Pratinjau Item ({{ $total }})
                </h6>
                <span class="badge bg-white text-muted font-semibold px-3 py-1.5" style="border: 1px solid var(--border-soft); font-size: 11px;">
                    Total: {{ $total }} Items
                </span>
            </div>

            <div class="card-body p-0">
                <!-- TABEL DESKTOP -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                            <thead style="background: var(--pink-soft-2); color: var(--ink-soft); font-size: 11.5px; font-family: 'Quicksand', sans-serif; text-transform: uppercase; font-weight: 700; border-bottom: 1.5px solid var(--border-soft);">
                                <tr>
                                    <th class="py-3 ps-4">No. Order</th>
                                    <th class="py-3">Pembeli</th>
                                    <th class="py-3">Kode Variasi</th>
                                    <th class="py-3 text-center">Qty</th>
                                    <th class="py-3 text-end">Total Harga</th>
                                    <th class="py-3 pe-4 text-end">Mapping Produk</th>
                                </tr>
                            </thead>
                            <tbody style="border-top: 1px solid var(--border-soft);">
                                @foreach($parsed as $idx => $row)
                                <tr>
                                    <td class="py-3 ps-4">
                                        <span class="badge" style="background: var(--pink-soft-2); color: var(--pink-primary-dark); border: 1px solid var(--border-soft); font-family: monospace; font-size: 11.5px; padding: 5px 8px;">
                                            {{ $row['no_pesanan'] ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3 fw-bold" style="color: var(--ink);">{{ $row['nama_pembeli'] ?? '-' }}</td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-dark font-semibold border px-2 py-1" style="font-size: 11px;">{{ $row['variasi'] ?? '-' }}</span>
                                    </td>
                                    <td class="py-3 text-center fw-bold">{{ $row['qty'] ?? 1 }}</td>
                                    <td class="py-3 text-end fw-bold" style="color: #52976D;">Rp {{ number_format($row['total_harga'] ?? 0, 0, ',', '.') }}</td>
                                    
                                    <td class="py-3 pe-4 text-end">
                                        @if($row['mapping_status'] === 'auto_found')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-2 font-semibold" style="font-size: 11px;">
                                                <i class="bi bi-check-lg me-1"></i> {{ substr($row['nama_produk_auto'] ?? '', 0, 22) }}
                                            </span>
                                            <input type="hidden" class="mapping-auto" data-idx="{{ $idx }}" value="{{ $row['id_produk_auto'] }}">
                                        @else
                                            <select name="mapping_select[{{ $idx }}]" class="form-select form-select-sm rounded-2 mapping-select font-semibold d-inline-block" data-idx="{{ $idx }}" style="font-size: 11.5px; max-width: 200px; border-color: var(--border-soft);">
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

                <!-- CARD MOBILE (HP) -->
                <div class="d-lg-none p-3" style="background: var(--pink-soft-2);">
                    @foreach($parsed as $idx => $row)
                    <div class="p-3 mb-3 rounded-3" style="background: #ffffff; border: 1.5px solid var(--border-soft);">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom: 1px dashed var(--border-soft);">
                            <span class="badge" style="background: var(--pink-soft-2); color: var(--pink-primary-dark); font-family: monospace; font-size: 11px;">
                                {{ $row['no_pesanan'] ?? '-' }}
                            </span>
                            <span class="badge font-semibold" style="font-size: 10px; background-color: {{ $row['mapping_status'] === 'auto_found' ? '#D4EDDA' : '#FFF3CD' }}; color: {{ $row['mapping_status'] === 'auto_found' ? '#155724' : '#856404' }};">
                                {{ $row['mapping_status'] === 'auto_found' ? '✓ Auto' : '⚠ Manual' }}
                            </span>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted d-block font-semibold" style="font-size: 11px;">Pembeli:</small>
                            <strong style="color: var(--ink); font-size: 13.5px;">{{ $row['nama_pembeli'] ?? '-' }}</strong>
                        </div>

                        <div class="row g-2 p-2.5 rounded-3 text-center align-items-center mb-3" style="background: var(--pink-soft-2); border: 1px solid var(--border-soft); font-size: 12px;">
                            <div class="col-6 border-end" style="border-color: var(--border-soft) !important;">
                                <small class="text-muted d-block" style="font-size: 10px;">Kode Variasi</small>
                                <strong style="color: var(--ink);">{{ $row['variasi'] ?? '-' }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block" style="font-size: 10px;">Jumlah × Harga</small>
                                <strong style="color: #52976D;">{{ $row['qty'] ?? 1 }} × Rp {{ number_format($row['total_harga'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        @if($row['mapping_status'] === 'auto_found')
                            <div class="p-2 rounded-3" style="background: #D4EDDA;">
                                <small class="text-success d-block font-semibold" style="font-size: 10px;">✓ Sudah ter-map ke:</small>
                                <strong class="text-success" style="font-size: 12px;">{{ $row['nama_produk_auto'] ?? '-' }}</strong>
                            </div>
                            <input type="hidden" class="mapping-auto" data-idx="{{ $idx }}" value="{{ $row['id_produk_auto'] }}">
                        @else
                            <select name="mapping_select[{{ $idx }}]" class="form-select form-select-sm rounded-2 mapping-select font-semibold" data-idx="{{ $idx }}" style="font-size: 11.5px; border-color: var(--border-soft);">
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
    e.preventDefault();
    
    let mappingObj = {};
    
    document.querySelectorAll('input.mapping-auto').forEach(input => {
        const idx = input.getAttribute('data-idx');
        const value = input.value;
        if (idx && value) {
            mappingObj[idx] = value;
        }
    });
    
    document.querySelectorAll('select.mapping-select').forEach(select => {
        const idx = select.getAttribute('data-idx');
        const value = select.value;
        if (idx && value) {
            mappingObj[idx] = value;
        }
    });
    
    document.getElementById('mapping_data_input').value = JSON.stringify(mappingObj);
    this.submit();
});
</script>
@endsection