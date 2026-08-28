@extends('layouts.app')

@section('title', 'Preview Import — Yourbaestyle')

@section('content')
@php
    // 1. Filter out dummy rows (misal baris header deskripsi TikTok "Platform unique order ID.")
    $cleanParsed = array_values(array_filter($parsed ?? [], function($p) {
        if (!is_array($p)) return false;
        $no = strtolower(trim($p['no_pesanan'] ?? ''));
        $var = strtolower(trim($p['variasi'] ?? ''));
        if (empty($no)) return false;
        if (str_contains($no, 'platform') || str_contains($no, 'unique order') || str_contains($no, 'explain') || str_contains($var, 'sku variation') || str_contains($var, 'platform sku')) {
            return false;
        }
        return true;
    }));

    // 2. Sorting: Auto-Mapped di ATAS, Manual di BAWAH
    usort($cleanParsed, function($a, $b) {
        $aAuto = ($a['mapping_status'] ?? '') === 'auto_found' ? 0 : 1;
        $bAuto = ($b['mapping_status'] ?? '') === 'auto_found' ? 0 : 1;
        return $aAuto <=> $bAuto;
    });

    $berhasil = count(array_filter($cleanParsed, fn($p) => ($p['mapping_status'] ?? '') === 'auto_found'));
    $gagalMapping = count(array_filter($cleanParsed, fn($p) => ($p['mapping_status'] ?? '') !== 'auto_found'));
    $duplikat = count(array_filter($cleanParsed, fn($p) => !empty($p['is_duplicate'])));
    $total = count($cleanParsed);
@endphp

<div class="container-fluid p-0" style="max-width: 1250px; margin: 0 auto;">
    
    <!-- 1. HEADER HALAMAN -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1 fw-bold" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                <i class="bi bi-file-earmark-check-fill me-2" style="color: var(--pink-primary);"></i>Preview Import Pesanan Omnichannel
            </h4>
            <div class="d-flex align-items-center gap-2 flex-wrap font-semibold" style="font-size: 12.5px;">
                <span class="text-muted">Platform: <strong style="color: var(--ink);">{{ strtoupper($platform) }}</strong></span>
                <span class="text-muted">•</span>
                <span class="text-muted">Format: <strong style="color: var(--ink);">{{ $formatCsv }}</strong></span>
                @if($duplikat > 0)
                    <span class="text-muted">•</span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 font-semibold" style="font-size: 11px;">
                        🚨 Terdeteksi {{ $duplikat }} Order Duplikat di Database
                    </span>
                @endif
            </div>
        </div>
        <div>
            <a href="{{ route('pesanan.import') }}" class="btn btn-yb-outline px-3 py-2 text-decoration-none font-semibold shadow-sm" style="border-radius: 12px; font-size: 12.5px;">
                <i class="bi bi-arrow-left me-1"></i> Ganti File
            </a>
        </div>
    </div>

    <!-- 2. STATISTIK CARDS -->
    <div class="row g-2.5 g-md-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="p-3.5 rounded-4 h-100 text-center d-flex flex-column justify-content-center align-items-center" style="background: #E8F7EE; border: 1.5px solid #C3EEDB; box-shadow: 0 4px 12px rgba(46, 175, 108, 0.08);">
                <div class="d-flex align-items-center justify-content-center gap-1.5 mb-1">
                    <span class="fw-bold text-success small"><i class="bi bi-check-circle-fill me-1"></i> Auto-Map</span>
                </div>
                <h3 class="mb-1 fw-bold text-success" style="font-size: 1.7rem; font-family: 'Quicksand', sans-serif;">{{ $berhasil }}</h3>
                <small class="text-success font-semibold opacity-85" style="font-size: 11px;">Terpetakan otomatis</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="p-3.5 rounded-4 h-100 text-center d-flex flex-column justify-content-center align-items-center" style="background: #FFF9E6; border: 1.5px solid #FFE699; box-shadow: 0 4px 12px rgba(230, 138, 0, 0.08); cursor: pointer;" onclick="filterTab('manual')" title="Klik untuk lihat yang perlu manual">
                <div class="d-flex align-items-center justify-content-center gap-1.5 mb-1">
                    <span class="fw-bold text-warning-emphasis small"><i class="bi bi-exclamation-triangle-fill me-1"></i> Perlu Manual</span>
                </div>
                <h3 class="mb-1 fw-bold text-warning-emphasis" style="font-size: 1.7rem; font-family: 'Quicksand', sans-serif;">{{ $gagalMapping }}</h3>
                <small class="text-warning-emphasis font-semibold opacity-85" style="font-size: 11px;">Perlu dipilih / skip</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="p-3.5 rounded-4 h-100 text-center d-flex flex-column justify-content-center align-items-center" style="background: {{ $duplikat > 0 ? '#FFF0F2' : '#F8FAFC' }}; border: 1.5px solid {{ $duplikat > 0 ? '#F5B8C5' : '#E2E8F0' }}; box-shadow: 0 4px 12px rgba(224, 82, 112, 0.08);">
                <div class="d-flex align-items-center justify-content-center gap-1.5 mb-1">
                    <span class="fw-bold {{ $duplikat > 0 ? 'text-danger' : 'text-muted' }} small"><i class="bi bi-shield-exclamation me-1"></i> Order Duplikat</span>
                </div>
                <h3 class="mb-1 fw-bold {{ $duplikat > 0 ? 'text-danger' : 'text-dark' }}" style="font-size: 1.7rem; font-family: 'Quicksand', sans-serif;">{{ $duplikat }}</h3>
                <small class="text-muted font-semibold" style="font-size: 11px;">{{ $duplikat > 0 ? 'Sudah ada di database' : 'Tidak ada duplikat' }}</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="p-3.5 rounded-4 h-100 text-center d-flex flex-column justify-content-center align-items-center" style="background: var(--pink-soft-2); border: 1.5px solid var(--border-soft); box-shadow: 0 4px 12px rgba(236, 149, 168, 0.08);">
                <div class="d-flex align-items-center justify-content-center gap-1.5 mb-1">
                    <span class="fw-bold small" style="color: var(--ink);"><i class="bi bi-box-seam me-1" style="color: var(--pink-primary);"></i> Total Order</span>
                </div>
                <h3 class="mb-1 fw-bold" style="color: var(--pink-primary-dark); font-size: 1.7rem; font-family: 'Quicksand', sans-serif;">{{ $total }}</h3>
                <small class="text-muted font-semibold" style="font-size: 11px;">Item dari Excel</small>
            </div>
        </div>
    </div>

    <!-- 4. FORM IMPORT & EKSEKUSI PANEL -->
    <form id="import-form" action="{{ route('pesanan.import.proses') }}" method="POST">
        @csrf
        
        <!-- HIDDEN INPUTS -->
        <input type="hidden" name="data_pesanan_mentah" value="{{ json_encode($cleanParsed) }}">
        <input type="hidden" name="mapping_data" id="mapping_data_input" value="{}">

        <!-- EKSEKUSI IMPORT PANEL -->
        <div class="card-yb p-3 p-md-4 mb-4 shadow-sm" style="border-radius: 18px; border: 1.5px solid var(--border-soft);">
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
                    <button type="submit" class="btn btn-yb px-4 py-2.5 font-semibold text-white shadow-sm w-100 w-md-auto" style="border-radius: 12px; font-size: 13.5px; background: linear-gradient(135deg, #EC95A8, #D86B85); border: none;">
                        <i class="bi bi-cloud-check-fill me-1.5"></i> Konfirmasi & Simpan {{ $total }} Pesanan
                    </button>
                </div>
            </div>
        </div>

        <!-- 5. TABEL DETAIL PREVIEW WITH FILTER TABS & SEARCH -->
        <div class="card-yb p-0 overflow-hidden shadow-sm" style="border-radius: 20px; border: 1.5px solid var(--border-soft);">
            
            <!-- HEADER TOOLBAR (TAB FILTER & SEARCH) -->
            <div class="p-3 px-4" style="border-bottom: 1.5px dashed var(--border-soft); background: #FFF5F7;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-2">
                    <h6 class="fw-bold mb-0" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                        <i class="bi bi-list-stars me-2" style="color: var(--pink-primary);"></i>Daftar Pratinjau Item ({{ $total }})
                    </h6>
                    <span class="badge bg-white text-muted font-semibold px-3 py-1.5 shadow-sm" style="border: 1px solid var(--border-soft); font-size: 11.5px; border-radius: 10px;">
                        Total Valid: {{ $total }} Items
                    </span>
                </div>

                <!-- TAB FILTERS & INSTANT SEARCH -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2">
                    <!-- FILTER TABS -->
                    <div class="d-flex align-items-center gap-1.5 p-1 rounded-3" style="background: rgba(236, 149, 168, 0.12); border: 1px solid var(--border-soft);">
                        <button type="button" class="btn btn-sm tab-btn active-tab px-3 py-1.5 font-semibold" data-filter="all" onclick="filterTab('all')" style="border-radius: 8px; font-size: 12px; transition: all 0.2s ease;">
                            Semua ({{ $total }})
                        </button>
                        <button type="button" class="btn btn-sm tab-btn px-3 py-1.5 font-semibold" data-filter="auto" onclick="filterTab('auto')" style="border-radius: 8px; font-size: 12px; color: #1e7e34; transition: all 0.2s ease;">
                            <i class="bi bi-check-circle-fill me-1"></i> Auto-Mapped ({{ $berhasil }})
                        </button>
                        <button type="button" class="btn btn-sm tab-btn px-3 py-1.5 font-semibold" data-filter="manual" onclick="filterTab('manual')" style="border-radius: 8px; font-size: 12px; color: #b35118; transition: all 0.2s ease;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Perlu Manual ({{ $gagalMapping }})
                        </button>
                    </div>

                    <!-- SEARCH INPUT -->
                    <div class="position-relative" style="min-width: 250px;">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 12px;"></i>
                        <input type="text" id="preview-search" onkeyup="applyFilterAndSearch()" class="form-control form-control-sm ps-5 font-semibold shadow-sm" placeholder="Cari Order / Pembeli / Variasi..." style="border-radius: 10px; border-color: var(--border-soft); font-size: 12px; background: #ffffff;">
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <!-- TABEL DESKTOP -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                            <thead style="background: #FFF0F3; color: var(--ink-soft); font-size: 11px; font-family: 'Quicksand', sans-serif; text-transform: uppercase; font-weight: 700; border-bottom: 1.5px solid var(--border-soft);">
                                <tr>
                                    <th class="py-3 ps-4" style="width: 190px;">No. Order</th>
                                    <th class="py-3">Pembeli</th>
                                    <th class="py-3 text-center" style="width: 130px;">Kode Variasi</th>
                                    <th class="py-3 text-center" style="width: 60px;">Qty</th>
                                    <th class="py-3 text-end" style="width: 120px;">Total Harga</th>
                                    <th class="py-3 pe-4 text-end">Mapping Produk</th>
                                </tr>
                            </thead>
                            <tbody id="preview-table-body" style="border-top: 1px solid var(--border-soft);">
                                @foreach($cleanParsed as $idx => $row)
                                @php
                                    $isAuto = ($row['mapping_status'] ?? '') === 'auto_found';
                                @endphp
                                <tr class="preview-row {{ $isAuto ? 'status-auto' : 'status-manual' }}" 
                                    data-status="{{ $isAuto ? 'auto' : 'manual' }}"
                                    data-search="{{ strtolower(($row['no_pesanan']??'').' '.($row['nama_pembeli']??'').' '.($row['variasi']??'')) }}"
                                    style="border-left: 4px solid {{ $isAuto ? '#2EAF6C' : '#EE9A68' }};">
                                    
                                    <td class="py-3 ps-4">
                                        <span class="badge" style="background: var(--pink-soft-2); color: var(--pink-primary-dark); border: 1px solid var(--border-soft); font-family: monospace; font-size: 11.5px; padding: 5px 8px;">
                                            {{ $row['no_pesanan'] ?? '-' }}
                                        </span>
                                        @if(!empty($row['is_duplicate']))
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1 font-semibold" style="font-size: 10px;" title="Nomor pesanan ini sudah ada di database pesanan online!">
                                                ⚠️ Duplikat
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 fw-bold" style="color: var(--ink);">{{ $row['nama_pembeli'] ?? '-' }}</td>
                                    <td class="py-3 text-center">
                                        <span class="badge font-semibold px-2.5 py-1" style="font-size: 11px; background: #FFF0F3; color: var(--pink-primary-dark); border: 1px solid var(--border-soft); border-radius: 8px;">
                                            {{ $row['variasi'] ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center fw-bold">{{ $row['qty'] ?? 1 }}</td>
                                    <td class="py-3 text-end fw-bold" style="color: #2EAF6C;">Rp {{ number_format($row['total_harga'] ?? 0, 0, ',', '.') }}</td>
                                    
                                    <td class="py-3 pe-4 text-end">
                                        @if($isAuto)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill font-semibold shadow-2xs" style="font-size: 11.5px;">
                                                <i class="bi bi-check-lg me-1"></i> {{ substr($row['nama_produk_auto'] ?? '', 0, 26) }}
                                            </span>
                                            @if(!empty($row['is_duplicate']))
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle ms-1 font-semibold" style="font-size: 10px;" title="Order sudah ada di database, akan diperbarui">
                                                    🔄 Update
                                                </span>
                                            @endif
                                            <input type="hidden" class="mapping-auto" data-idx="{{ $idx }}" value="{{ $row['id_produk_auto'] }}">
                                        @else
                                            <select name="mapping_select[{{ $idx }}]" class="form-select form-select-sm rounded-3 mapping-select font-semibold d-inline-block shadow-2xs" data-idx="{{ $idx }}" style="font-size: 11.5px; max-width: 230px; border-color: {{ !empty($row['is_duplicate']) ? '#F7C9D3' : '#F8C3A4' }}; background-color: {{ !empty($row['is_duplicate']) ? '#FFF0F2' : '#FFF9F5' }};">
                                                <option value="">— Pilih Produk Katalog —</option>
                                                @foreach($produkAktif as $prod)
                                                    <option value="{{ $prod->id }}">{{ substr($prod->nama_produk, 0, 25) }} (Stok: {{ $prod->stok }})</option>
                                                @endforeach
                                                <option value="skip" {{ !empty($row['is_duplicate']) ? 'selected' : '' }} style="background-color: #FFE6E6; color: #dc3545;">▪ Skip {{ !empty($row['is_duplicate']) ? '(Order Duplikat)' : 'Pesanan Ini' }}</option>
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
                <div class="d-lg-none p-3" id="preview-mobile-container" style="background: var(--pink-soft-2);">
                    @foreach($cleanParsed as $idx => $row)
                    @php
                        $isAuto = ($row['mapping_status'] ?? '') === 'auto_found';
                    @endphp
                    <div class="p-3 mb-3 rounded-4 preview-row {{ $isAuto ? 'status-auto' : 'status-manual' }}" 
                         data-status="{{ $isAuto ? 'auto' : 'manual' }}"
                         data-search="{{ strtolower(($row['no_pesanan']??'').' '.($row['nama_pembeli']??'').' '.($row['variasi']??'')) }}"
                         style="background: #ffffff; border: 1.5px solid var(--border-soft); border-left: 5px solid {{ $isAuto ? '#2EAF6C' : '#EE9A68' }};">
                        
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom: 1px dashed var(--border-soft);">
                            <span class="badge" style="background: var(--pink-soft-2); color: var(--pink-primary-dark); font-family: monospace; font-size: 11px;">
                                {{ $row['no_pesanan'] ?? '-' }}
                            </span>
                            <span class="badge font-semibold rounded-pill px-2.5 py-1" style="font-size: 10.5px; background-color: {{ $isAuto ? '#E8F7EE' : '#FFF9E6' }}; color: {{ $isAuto ? '#2EAF6C' : '#B35118' }}; border: 1px solid {{ $isAuto ? '#C3EEDB' : '#FFE699' }};">
                                {{ $isAuto ? '✓ Auto-Mapped' : '⚠ Perlu Manual' }}
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
                                <strong style="color: #2EAF6C;">{{ $row['qty'] ?? 1 }} × Rp {{ number_format($row['total_harga'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        @if($isAuto)
                            <div class="p-2.5 rounded-3" style="background: #E8F7EE; border: 1px solid #C3EEDB;">
                                <small class="text-success d-block font-semibold" style="font-size: 10px;">✓ Ter-map ke Produk:</small>
                                <strong class="text-success" style="font-size: 12px;">{{ $row['nama_produk_auto'] ?? '-' }}</strong>
                                @if(!empty($row['is_duplicate']))
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle ms-1 font-semibold" style="font-size: 10px;">🔄 Update</span>
                                @endif
                            </div>
                            <input type="hidden" class="mapping-auto" data-idx="{{ $idx }}" value="{{ $row['id_produk_auto'] }}">
                        @else
                            <select name="mapping_select[{{ $idx }}]" class="form-select form-select-sm rounded-3 mapping-select font-semibold" data-idx="{{ $idx }}" style="font-size: 11.5px; border-color: #F8C3A4; background-color: #FFF9F5;">
                                <option value="">— Pilih Produk Katalog —</option>
                                @foreach($produkAktif as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->nama_produk }} (Stok: {{ $prod->stok }})</option>
                                @endforeach
                                <option value="skip" {{ !empty($row['is_duplicate']) ? 'selected' : '' }} style="background-color: #FFE6E6; color: #dc3545;">▪ Skip {{ !empty($row['is_duplicate']) ? '(Order Duplikat)' : 'Pesanan Ini' }}</option>
                            </select>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </form>
</div>

<!-- STYLES & JAVASCRIPT FOR TABS & SEARCH -->
<style>
.tab-btn.active-tab {
    background: #ffffff !important;
    color: var(--ink) !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
}
.tab-btn:hover:not(.active-tab) {
    background: rgba(255,255,255,0.6);
}
</style>

<script>
let currentFilter = 'all';

function filterTab(filterName) {
    currentFilter = filterName;
    
    document.querySelectorAll('.tab-btn').forEach(btn => {
        if (btn.getAttribute('data-filter') === filterName) {
            btn.classList.add('active-tab');
        } else {
            btn.classList.remove('active-tab');
        }
    });

    applyFilterAndSearch();
}

function applyFilterAndSearch() {
    const searchVal = document.getElementById('preview-search').value.toLowerCase().trim();
    
    document.querySelectorAll('.preview-row').forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        const rowSearch = row.getAttribute('data-search') || '';

        const matchTab = (currentFilter === 'all') || (currentFilter === rowStatus);
        const matchSearch = (searchVal === '') || rowSearch.includes(searchVal);

        if (matchTab && matchSearch) {
            row.style.setProperty('display', '', 'important');
        } else {
            row.style.setProperty('display', 'none', 'important');
        }
    });
}

// Handler Submit Form
document.getElementById('import-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let mappingObj = {};
    
    document.querySelectorAll('input.mapping-auto').forEach(input => {
        const idx = input.getAttribute('data-idx');
        const value = input.value;
        if (idx !== null && idx !== undefined && value) {
            mappingObj[idx] = value;
        }
    });
    
    document.querySelectorAll('select.mapping-select').forEach(select => {
        const idx = select.getAttribute('data-idx');
        const value = select.value;
        if (idx !== null && idx !== undefined && value !== '') {
            if (!mappingObj[idx] || value === 'skip') {
                mappingObj[idx] = value;
            }
        }
    });
    
    document.getElementById('mapping_data_input').value = JSON.stringify(mappingObj);
    this.submit();
});
</script>
@endsection