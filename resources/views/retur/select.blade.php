@extends('layouts.app')

@section('title', 'Pilih Transaksi untuk Retur - Yourbaestyle')

@section('content')
<div class="container-fluid py-4" style="max-width: 1100px; margin: 0 auto;">
    
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h4 class="mb-1 fw-bold text-dark" style="font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-reply-fill me-2" style="color: #EC95A8;"></i>Pilih Transaksi untuk Retur
                </h4>
                <small class="text-muted">Cari dan pilih transaksi yang akan di-retur (dari Kasir POS atau Pesanan Online)</small>
            </div>
            <a href="{{ route('retur.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi Error/Success -->
    @if(session('error'))
        <div class="alert alert-danger rounded-4 mb-4 border-0 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- CARD PENCARIAN & FILTER TERPADU -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom" style="border-color: #F7E5EA;">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="bi bi-funnel-fill me-2" style="color: #EC95A8;"></i>Filter & Cari Transaksi
            </h6>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('retur.select') }}" id="mainSearchForm">
                <!-- Baris 1: Filter Parameter -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted">Sesi Live (Khusus Online)</label>
                        <select name="id_sesi_live" class="form-select rounded-3">
                            <option value="">-- Semua Sesi Live --</option>
                            @if(isset($sesiLive))
                                @foreach($sesiLive as $sesi)
                                    <option value="{{ $sesi->id }}" {{ request('id_sesi_live') == $sesi->id ? 'selected' : '' }}>
                                        {{ $sesi->nama_sesi }} ({{ \Carbon\Carbon::parse($sesi->tanggal_live)->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control rounded-3" value="{{ request('tanggal_mulai') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control rounded-3" value="{{ request('tanggal_selesai') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted">Tipe Transaksi</label>
                        <select name="type" class="form-select rounded-3">
                            <option value="">-- Semua Tipe --</option>
                            <option value="pos" {{ request('type') == 'pos' ? 'selected' : '' }}>Kasir (POS)</option>
                            <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>Pesanan Online</option>
                        </select>
                    </div>
                </div>

                <!-- Baris 2: Input Pencarian & Action Buttons -->
                <div class="d-flex gap-2 flex-wrap align-items-center">
                    <div class="flex-grow-1">
                        <input type="text" name="search" class="form-control rounded-3" 
                               placeholder="Cari nomor transaksi, resi, atau nama produk..." 
                               value="{{ request('search', '') }}"
                               id="searchInput" autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="bi bi-search me-1"></i> Terapkan Filter
                    </button>
                    @if(request()->hasAny(['search', 'id_sesi_live', 'tanggal_mulai', 'tanggal_selesai', 'type']))
                        <a href="{{ route('retur.select') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-x-lg me-1"></i> Reset
                        </a>
                    @endif
                    <!-- TOMBOL SCAN BARCODE -->
                    <button type="button" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#scanBarcodeModal">
                        <i class="bi bi-upc-scan me-1"></i> Scan Barcode
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- INFORMASI HASIL PENCARIAN -->
    @if(request()->hasAny(['search', 'id_sesi_live', 'tanggal_mulai', 'tanggal_selesai', 'type']))
        <div class="alert alert-info rounded-4 mb-4" style="background: #E8F4F8; border: none;">
            <i class="bi bi-info-circle me-2"></i>
            Ditemukan <strong>{{ $transaksis->count() }}</strong> transaksi sesuai kriteria filter Anda.
        </div>
    @endif

    <!-- TABEL TRANSAKSI - DESKTOP -->
    <div class="card border-0 shadow-sm rounded-4 d-none d-lg-block" style="overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                <thead style="background-color: #FFF5F7; color: #7A686D; font-size: 11.5px; text-transform: uppercase;">
                    <tr>
                        <th class="py-3 ps-4 border-0">No. Transaksi</th>
                        <th class="py-3 border-0">Tipe</th>
                        <th class="py-3 border-0">Produk</th>
                        <th class="py-3 text-end border-0">Total</th>
                        <th class="py-3 border-0">Tanggal</th>
                        <th class="py-3 pe-4 text-center border-0">Aksi</th>
                    </tr>
                </thead>
                <tbody style="border-top: 1px solid #F7E5EA;">
                    @forelse($transaksis as $tx)
                    <tr>
                        <td class="ps-4">
                            <code style="font-size: 11.5px; color: #EC95A8; background: #FFF0F3; padding: 4px 8px; border-radius: 6px;">
                                {{ $tx->no_transaksi ?? '-' }}
                            </code>
                        </td>
                        <td>
                            <span class="badge" style="background: {{ $tx->type === 'online' ? '#FFE6E6' : '#E8F7EE' }}; color: {{ $tx->type === 'online' ? '#DC3545' : '#52976D' }};">
                                {{ $tx->type === 'online' ? 'ONLINE' : 'POS' }}
                            </span>
                        </td>
                        <td class="fw-semibold text-dark" title="{{ $tx->produk_nama ?? '-' }}">
                            {{ \Illuminate\Support\Str::limit($tx->produk_nama ?? '-', 45) }}
                        </td>
                        <td class="text-end fw-bold" style="color: #4D3D43;">
                            Rp {{ number_format($tx->total_harga ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-muted small">
                            {{ $tx->tanggal ? \Carbon\Carbon::parse($tx->tanggal)->format('d-m-Y H:i') : '-' }}
                        </td>
                        <td class="pe-4 text-center">
                            <a href="{{ route('retur.create', ['transaksi_id' => $tx->id, 'type' => $tx->type]) }}" 
                               class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                                <i class="bi bi-plus-lg me-1"></i> Pilih
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-3">
                                <div style="font-size: 3rem; color: #F6C9D3; margin-bottom: 10px;">
                                    <i class="bi bi-inbox"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Transaksi tidak ditemukan</h6>
                                <small class="text-muted">
                                    Gunakan filter tanggal, sesi live, atau kata kunci pencarian di atas untuk menemukan transaksi
                                </small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- KARTU MOBILE -->
    <div class="d-lg-none">
        @forelse($transaksis as $tx)
        <div class="card border-0 shadow-sm rounded-4 mb-3" style="overflow: hidden; background: #fff;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <code style="font-size: 12px; color: #EC95A8; background: #FFF0F3; padding: 4px 8px; border-radius: 6px;">
                            {{ $tx->no_transaksi ?? '-' }}
                        </code>
                        <br>
                        <span class="badge mt-1" style="background: {{ $tx->type === 'online' ? '#FFE6E6' : '#E8F7EE' }}; color: {{ $tx->type === 'online' ? '#DC3545' : '#52976D' }}; font-size: 11px;">
                            {{ $tx->type === 'online' ? 'PESANAN ONLINE' : 'KASIR POS' }}
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block" style="font-size: 11px;">Produk</small>
                    <strong class="text-dark d-block">{{ $tx->produk_nama ?? '-' }}</strong>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block" style="font-size: 11px;">Total</small>
                        <strong style="color: #EC95A8; font-size: 15px;">Rp {{ number_format($tx->total_harga ?? 0, 0, ',', '.') }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block" style="font-size: 11px;">Tanggal</small>
                        <strong class="text-dark">{{ $tx->tanggal ? \Carbon\Carbon::parse($tx->tanggal)->format('d-m-Y') : '-' }}</strong>
                    </div>
                </div>

                <a href="{{ route('retur.create', ['transaksi_id' => $tx->id, 'type' => $tx->type]) }}" 
                   class="btn btn-primary w-100 rounded-pill fw-bold">
                    <i class="bi bi-plus-lg me-1"></i> Pilih Transaksi Ini
                </a>
            </div>
        </div>
        @empty
        <div class="card border-0 shadow-sm rounded-4 text-center py-5" style="background: #fff;">
            <div style="font-size: 3rem; color: #F6C9D3; margin-bottom: 10px;">
                <i class="bi bi-inbox"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1">Transaksi tidak ditemukan</h6>
            <small class="text-muted">
                Gunakan filter di atas untuk menampilkan data transaksi
            </small>
        </div>
        @endforelse
    </div>
</div>

<!-- MODAL SCAN / FOTO BARCODE UNTUK SEARCH TRANSAKSI -->
<div class="modal fade" id="scanBarcodeModal" tabindex="-1" aria-labelledby="scanBarcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-bottom" style="border-color: #F7E5EA;">
                <h5 class="modal-title fw-bold" id="scanBarcodeModalLabel">
                    <i class="bi bi-qr-code-scan me-2" style="color: #EC95A8;"></i>Scan / Foto Barcode & QR Code
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                
                <!-- Nav Tabs Pilihan Metode -->
                <ul class="nav nav-pills nav-fill mb-3 rounded-pill p-1" style="background: #FFF5F7; border: 1px solid #F7E5EA;" id="barcodeTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill fw-bold small py-2" id="camera-tab" data-bs-toggle="tab" data-bs-target="#camera-pane" type="button" role="tab">
                            <i class="bi bi-camera-video-fill me-1"></i> Live Kamera
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill fw-bold small py-2" id="file-tab" data-bs-toggle="tab" data-bs-target="#file-pane" type="button" role="tab">
                            <i class="bi bi-image-fill me-1"></i> Upload / Ambil Foto
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mb-3" id="barcodeTabContent">
                    <!-- Tab 1: Live Kamera -->
                    <div class="tab-pane fade show active" id="camera-pane" role="tabpanel">
                        <div id="scanner" style="width: 100%; min-height: 280px; background: #f0f0f0; border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <div class="text-center p-4">
                                <i class="bi bi-camera fs-1 text-muted mb-2"></i>
                                <p class="text-muted mb-0 small">Arahkan kamera ke QR Code atau Barcode transaksi...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Upload Foto -->
                    <div class="tab-pane fade" id="file-pane" role="tabpanel">
                        <div class="text-center p-4 rounded-3" style="border: 2px dashed #EC95A8; background: #FAF8F9;">
                            <i class="bi bi-cloud-arrow-up-fill fs-1" style="color: #EC95A8;"></i>
                            <h6 class="fw-bold mt-2 mb-1 text-dark">Pilih Foto atau Ambil Jepretan</h6>
                            <p class="text-muted small mb-3">Upload screenshot pesanan atau foto resi/QR Code yang jelas</p>
                            
                            <label for="barcodeFileInput" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="cursor: pointer;">
                                <i class="bi bi-folder2-open me-1"></i> Pilih File Foto / Kamera
                            </label>
                            <input type="file" id="barcodeFileInput" accept="image/*" class="d-none">
                        </div>
                        
                        <!-- Preview Hasil Foto -->
                        <div id="imagePreviewArea" class="text-center mt-3 d-none">
                            <img id="previewImg" src="" alt="Preview Barcode" class="img-fluid rounded-3 mb-2" style="max-height: 180px; border: 1px solid #E8D9E0;">
                            <div id="decodeStatus" class="small fw-bold text-muted">Sedang membaca kode...</div>
                        </div>
                    </div>
                </div>

                <div id="scanner-hidden" style="display: none;"></div>

                <!-- Manual Input Fallback -->
                <div class="border-top pt-3" style="border-color: #F7E5EA !important;">
                    <label class="form-label fw-bold small text-muted mb-1">ATAU KETIK MANUAL:</label>
                    <form id="manualSearchForm" method="GET" action="{{ route('retur.select') }}" class="mb-0">
                        <div class="input-group">
                            <input type="text" id="barcodeSearchInput" name="search" class="form-control" placeholder="Ketik nomor transaksi / resi...">
                            <button class="btn btn-primary" type="submit" id="searchBarcodeBtn">
                                <i class="bi bi-search me-1"></i>Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer border-top py-2" style="border-color: #F7E5EA;">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control, .form-select {
        border-color: #E8D9E0;
        border-radius: 12px;
        font-size: 13px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #EC95A8;
        box-shadow: 0 0 0 0.2rem rgba(236, 149, 168, 0.25);
    }
    .btn-primary {
        background: linear-gradient(135deg, #EC95A8, #D97B90);
        border: none;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #D97B90, #C56C7F);
    }
    #scanner video, #scanner canvas {
        max-width: 100%;
        max-height: 100%;
        border-radius: 12px;
    }
    #barcodeTab .nav-link {
        color: #7A686D;
        background: transparent;
        transition: all 0.2s ease;
        border: none;
    }
    #barcodeTab .nav-link.active {
        color: #fff;
        background: linear-gradient(135deg, #EC95A8, #D97B90);
        box-shadow: 0 2px 6px rgba(236, 149, 168, 0.4);
    }
</style>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
window.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('scanBarcodeModal');
    let html5QrCode = null;
    let isScanning = false;
    
    function startLiveCamera() {
        if (typeof Html5Qrcode === 'undefined') {
            document.querySelector('#scanner').innerHTML = '<div class="text-center text-danger p-4"><i class="bi bi-wifi-off fs-1"></i><p class="small mb-0 mt-2">Gagal memuat library pemindai. Pastikan koneksi internet aktif.</p></div>';
            return;
        }
        if (isScanning) return;
        
        try {
            html5QrCode = new Html5Qrcode("scanner");
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            html5QrCode.start(
                { facingMode: "environment" },
                config,
                function(decodedText) {
                    stopLiveCamera();
                    processDetectedBarcode(decodedText);
                },
                function(error) { }
            ).then(() => {
                isScanning = true;
            }).catch((err) => {
                console.error('Kamera error:', err);
                document.querySelector('#scanner').innerHTML = '<div class="text-center text-danger p-4"><i class="bi bi-camera-video-off fs-1"></i><p class="small mb-0 mt-2">Gagal membuka kamera. Pastikan izin kamera aktif.</p></div>';
            });
        } catch(e) {
            console.error('Fatal Scanner:', e);
        }
    }

    function stopLiveCamera() {
        if (html5QrCode && isScanning) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                isScanning = false;
            }).catch((err) => console.error(err));
        }
    }

    if (modalElement) {
        modalElement.addEventListener('show.bs.modal', function() {
            const cameraTabBtn = document.querySelector('#camera-tab');
            if (cameraTabBtn) bootstrap.Tab.getOrCreateInstance(cameraTabBtn).show();
            
            document.getElementById('imagePreviewArea').classList.add('d-none');
            document.getElementById('barcodeFileInput').value = '';
            
            setTimeout(() => { startLiveCamera(); }, 300);
        });

        modalElement.addEventListener('hidden.bs.modal', function() {
            stopLiveCamera();
        });
    }

    const cameraTab = document.getElementById('camera-tab');
    const fileTab = document.getElementById('file-tab');

    if (cameraTab) cameraTab.addEventListener('shown.bs.tab', () => startLiveCamera());
    if (fileTab) fileTab.addEventListener('shown.bs.tab', () => stopLiveCamera());

    const fileInput = document.getElementById('barcodeFileInput');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (!e.target.files || e.target.files.length === 0) return;
            const file = e.target.files[0];

            if (typeof Html5Qrcode === 'undefined') {
                alert('Library scanner gagal dimuat.');
                return;
            }

            const imgURL = URL.createObjectURL(file);
            const previewArea = document.getElementById('imagePreviewArea');
            const previewImg = document.getElementById('previewImg');
            const decodeStatus = document.getElementById('decodeStatus');

            previewImg.src = imgURL;
            previewArea.classList.remove('d-none');
            decodeStatus.innerHTML = '<span class="text-primary"><i class="bi bi-hourglass-split me-1"></i>Memindai barcode dari foto...</span>';

            const fileScanner = new Html5Qrcode("scanner-hidden");
            fileScanner.scanFile(file, true)
                .then(decodedText => {
                    decodeStatus.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Terbaca: <strong>${decodedText}</strong></span>`;
                    setTimeout(() => { processDetectedBarcode(decodedText); }, 800);
                })
                .catch(err => {
                    decodeStatus.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Kode tidak terbaca.</span>';
                });
        });
    }

    function processDetectedBarcode(barcode) {
        document.getElementById('barcodeSearchInput').value = barcode;
        document.getElementById('searchInput').value = barcode;
        
        if (modalElement) {
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) modalInstance.hide();
        }
        
        const mainForm = document.getElementById('mainSearchForm');
        if (mainForm) {
            mainForm.submit();
        } else {
            window.location.href = `{{ route('retur.select') }}?search=${encodeURIComponent(barcode)}`;
        }
    }

    const manualSearchForm = document.getElementById('manualSearchForm');
    if (manualSearchForm) {
        manualSearchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const barcode = document.getElementById('barcodeSearchInput').value;
            if (barcode.trim()) processDetectedBarcode(barcode);
        });
    }
});
</script>
@endpush