@extends('layouts.app')

@section('title', 'Pilih Transaksi untuk Retur — Yourbaestyle')

@section('content')
<div class="container-fluid p-0" style="max-width: 1150px; margin: 0 auto;">
    
    <!-- HEADER ACTION -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                <i class="bi bi-reply-fill me-2" style="color: var(--pink-primary);"></i>Pilih Transaksi untuk Retur
            </h4>
            <small class="text-muted font-semibold" style="font-size: 12px;">Cari dan pilih transaksi yang akan di-retur (dari Kasir POS Offline atau Pesanan Online)</small>
        </div>
        <a href="{{ route('retur.index') }}" class="btn btn-yb-outline px-3 py-2 text-decoration-none font-semibold" style="border-radius: 12px; font-size: 12.5px;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Retur
        </a>
    </div>

    <!-- ALERT NOTIFIKASI ERROR -->
    @if(session('error'))
        <div class="p-3 mb-4 d-flex align-items-center gap-2" style="background: #FFF0F2; border: 1.5px solid #F5B8C5; border-radius: 16px; color: #A6243F; font-size: 13px; font-weight: 600;">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-1" style="color: #E05270;"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- PANEL PENCARIAN & FILTER TERPADU -->
    <div class="card-yb p-3 p-md-4 mb-4">
        <div class="d-flex align-items-center gap-2 mb-3 pb-2.5" style="border-bottom: 1.5px dashed var(--border-soft);">
            <h6 class="mb-0 fw-bold" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                <i class="bi bi-funnel-fill me-2" style="color: var(--pink-primary);"></i>Filter & Cari Transaksi
            </h6>
        </div>

        <form method="GET" action="{{ route('retur.select') }}" id="mainSearchForm">
            <!-- Baris 1: Filter Parameter -->
            <div class="row g-2.5 mb-3">
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label font-semibold text-muted mb-1" style="font-size: 11.5px;">Sesi Live (Khusus Online)</label>
                    <select name="id_sesi_live" class="form-select font-semibold" style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px;">
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
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label font-semibold text-muted mb-1" style="font-size: 11.5px;">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control font-semibold" value="{{ request('tanggal_mulai') }}" style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px;">
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label font-semibold text-muted mb-1" style="font-size: 11.5px;">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control font-semibold" value="{{ request('tanggal_selesai') }}" style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px;">
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label font-semibold text-muted mb-1" style="font-size: 11.5px;">Tipe Transaksi</label>
                    <select name="type" class="form-select font-semibold" style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px;">
                        <option value="">-- Semua Tipe (POS & Online) --</option>
                        <option value="pos" {{ request('type') == 'pos' ? 'selected' : '' }}>Kasir POS (Offline)</option>
                        <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>Pesanan Online</option>
                    </select>
                </div>
            </div>

            <!-- Baris 2: Input Kata Kunci & Action Buttons -->
            <div class="row g-2.5 align-items-center">
                <div class="col-12 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0" style="border-color: var(--border-soft); border-radius: 12px 0 0 12px; color: var(--ink-soft);">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0 font-semibold" 
                               placeholder="Cari nomor transaksi, resi, pembeli, atau produk..." 
                               value="{{ request('search', '') }}"
                               id="searchInput" autofocus style="border-radius: 0 12px 12px 0; border-color: var(--border-soft); font-size: 12.5px;">
                    </div>
                </div>

                <div class="col-12 col-lg-7 d-flex flex-wrap align-items-center justify-content-lg-end gap-2">
                    <button type="submit" class="btn btn-yb px-4 py-2 font-semibold shadow-sm text-white" style="border-radius: 12px; font-size: 13px;">
                        <i class="bi bi-search me-1.5"></i> Terapkan Filter
                    </button>
                    
                    @if(request()->hasAny(['search', 'id_sesi_live', 'tanggal_mulai', 'tanggal_selesai', 'type']))
                        <a href="{{ route('retur.select') }}" class="btn btn-yb-outline px-3 py-2 text-decoration-none font-semibold" style="border-radius: 12px; font-size: 13px;">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </a>
                    @endif

                    <!-- TOMBOL SCAN BARCODE -->
                    <button type="button" class="btn font-semibold px-4 py-2 text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#scanBarcodeModal" style="background: linear-gradient(135deg, #DD7991 0%, #C43654 100%); border-radius: 12px; font-size: 13px;">
                        <i class="bi bi-upc-scan me-1.5"></i> Scan Barcode
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- INFORMASI HASIL PENCARIAN -->
    @if(request()->hasAny(['search', 'id_sesi_live', 'tanggal_mulai', 'tanggal_selesai', 'type']))
        <div class="p-3 mb-4 d-flex align-items-center gap-2" style="background: var(--pink-soft-2); border: 1.5px solid var(--border-soft); border-radius: 16px; color: var(--ink); font-size: 13px; font-weight: 600;">
            <i class="bi bi-info-circle-fill fs-5 me-1" style="color: var(--pink-primary);"></i>
            Ditemukan <strong style="color: var(--pink-primary-dark);" class="mx-1">{{ $transaksis->count() }}</strong> transaksi sesuai kriteria filter Anda.
        </div>
    @endif

    <!-- TABEL TRANSAKSI - DESKTOP -->
    <div class="card-yb p-0 overflow-hidden d-none d-lg-block">
        <div class="p-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1.5px dashed var(--border-soft); background: var(--pink-soft-2);">
            <h6 class="fw-bold mb-0" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                <i class="bi bi-list-check me-2" style="color: var(--pink-primary);"></i>Daftar Transaksi Ditemukan
            </h6>
            <span class="badge bg-white text-muted font-semibold px-3 py-1.5" style="border: 1px solid var(--border-soft); font-size: 11px;">
                Total: {{ $transaksis->count() }} Transaksi
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead style="background: var(--pink-soft-2); color: var(--ink-soft); font-size: 11.5px; font-family: 'Quicksand', sans-serif; text-transform: uppercase; font-weight: 700; border-bottom: 1.5px solid var(--border-soft);">
                    <tr>
                        <th class="py-3 ps-4">No. Transaksi</th>
                        <th class="py-3">Tipe</th>
                        <th class="py-3">Pembeli / Kasir</th>
                        <th class="py-3">Item Produk</th>
                        <th class="py-3 text-end">Total Transaction</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3 pe-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody style="border-top: 1px solid var(--border-soft);">
                    @forelse($transaksis as $tx)
                    @php
                        $type = $tx->type ?? 'pos';
                        $kode = $tx->kode_transaksi ?? '-';
                        $pembeli = $tx->nama_pembeli ?? '-';
                        $produkList = $tx->detail ? $tx->detail->map(function($d) {
                            $nama = optional($d->produk)->nama_produk ?? $d->nama_produk_history ?? 'Produk';
                            return $nama . ' (' . $d->qty . ' pcs)';
                        })->implode(', ') : '-';
                        $itemCount = $tx->detail ? $tx->detail->count() : 0;
                    @endphp
                    <tr>
                        <td class="py-3 ps-4">
                            <span class="badge" style="background: var(--pink-soft-2); color: var(--pink-primary-dark); border: 1px solid var(--border-soft); font-family: monospace; font-size: 11.5px; padding: 5px 8px;">
                                {{ $kode }}
                            </span>
                        </td>
                        <td class="py-3">
                            @if($type === 'online')
                                <span class="badge font-semibold px-2.5 py-1" style="background: #FFE6E6; color: #DC3545; font-size: 10.5px;">PESANAN ONLINE</span>
                            @else
                                <span class="badge font-semibold px-2.5 py-1" style="background: #E8F7EE; color: #52976D; font-size: 10.5px;">KASIR (POS)</span>
                            @endif
                        </td>
                        <td class="py-3 fw-semibold text-dark">
                            {{ $pembeli }}
                        </td>
                        <td class="py-3" title="{{ $produkList }}">
                            <div class="fw-bold" style="color: var(--ink);">
                                {{ \Illuminate\Support\Str::limit($produkList, 40) }}
                            </div>
                            <small class="text-muted font-semibold" style="font-size: 11px;">
                                <i class="bi bi-box-seam me-1"></i>{{ $itemCount }} jenis barang
                            </small>
                        </td>
                        <td class="py-3 text-end fw-bold" style="color: #52976D; font-size: 13.5px;">
                            Rp {{ number_format($tx->total_harga ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-muted font-semibold" style="font-size: 12px;">
                            {{ $tx->tanggal ? \Carbon\Carbon::parse($tx->tanggal)->format('d-m-Y H:i') : '-' }}
                        </td>
                        <td class="py-3 pe-4 text-center">
                            <a href="{{ route('retur.create', ['transaksi_id' => $tx->id, 'type' => $type]) }}" 
                               class="btn btn-sm btn-yb px-3 py-1.5 font-semibold text-white shadow-sm" style="border-radius: 10px; font-size: 12px;">
                                <i class="bi bi-plus-lg me-1"></i> Pilih
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--border-soft);"></i>
                                <h6 class="fw-bold mb-1" style="color: var(--ink);">Transaksi tidak ditemukan</h6>
                                <small class="text-muted font-semibold">Gunakan filter tanggal, sesi live, atau kata kunci pencarian di atas untuk menemukan transaksi</small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- KARTU MOBILE (HP) -->
    <div class="d-lg-none">
        @forelse($transaksis as $tx)
        @php
            $type = $tx->type ?? 'pos';
            $kode = $tx->kode_transaksi ?? '-';
            $pembeli = $tx->nama_pembeli ?? '-';
            $produkList = $tx->detail ? $tx->detail->map(function($d) {
                $nama = optional($d->produk)->nama_produk ?? $d->nama_produk_history ?? 'Produk';
                return $nama . ' (' . $d->qty . ' pcs)';
            })->implode(', ') : '-';
        @endphp
        <div class="p-3 mb-3 rounded-3" style="background: #ffffff; border: 1.5px solid var(--border-soft);">
            <div class="d-flex justify-content-between align-items-start mb-2.5">
                <div>
                    <span class="badge mb-1" style="background: var(--pink-soft-2); color: var(--pink-primary-dark); font-family: monospace; font-size: 11px;">
                        {{ $kode }}
                    </span>
                    <br>
                    @if($type === 'online')
                        <span class="badge font-semibold px-2 py-0.5" style="background: #FFE6E6; color: #DC3545; font-size: 10px;">PESANAN ONLINE</span>
                    @else
                        <span class="badge font-semibold px-2 py-0.5" style="background: #E8F7EE; color: #52976D; font-size: 10px;">KASIR (POS)</span>
                    @endif
                </div>
                <small class="text-muted font-semibold" style="font-size: 11px;">{{ $pembeli }}</small>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block font-semibold" style="font-size: 11px;">Produk</small>
                <strong style="color: var(--ink); font-size: 13px;">{{ $produkList }}</strong>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="p-2 rounded-2" style="background: var(--pink-soft-2); border: 1px solid var(--border-soft);">
                        <small class="text-muted d-block" style="font-size: 10px;">Total Transaksi</small>
                        <strong style="color: #52976D; font-size: 12.5px;">Rp {{ number_format($tx->total_harga ?? 0, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded-2" style="background: var(--pink-soft-2); border: 1px solid var(--border-soft);">
                        <small class="text-muted d-block" style="font-size: 10px;">Tanggal</small>
                        <strong style="color: var(--ink); font-size: 12px;">{{ $tx->tanggal ? \Carbon\Carbon::parse($tx->tanggal)->format('d-m-Y') : '-' }}</strong>
                    </div>
                </div>
            </div>

            <a href="{{ route('retur.create', ['transaksi_id' => $tx->id, 'type' => $type]) }}" 
               class="btn btn-yb w-100 font-semibold text-white shadow-sm" style="border-radius: 12px; font-size: 12.5px;">
                <i class="bi bi-plus-lg me-1"></i> Pilih Transaksi Ini
            </a>
        </div>
        @empty
        <div class="card-yb text-center py-5">
            <i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--border-soft);"></i>
            <h6 class="fw-bold mb-1" style="color: var(--ink);">Transaksi tidak ditemukan</h6>
            <small class="text-muted font-semibold">Gunakan filter di atas untuk menampilkan data transaksi</small>
        </div>
        @endforelse
    </div>
</div>

<!-- MODAL SCAN / FOTO BARCODE UNTUK SEARCH TRANSAKSI -->
<div class="modal fade" id="scanBarcodeModal" tabindex="-1" aria-labelledby="scanBarcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 20px; box-shadow: 0 15px 40px rgba(0,0,0,0.12);">
            <div class="modal-header py-3 px-4" style="border-bottom: 1.5px dashed var(--border-soft); background: var(--pink-soft-2);">
                <h6 class="modal-title fw-bold mb-0" id="scanBarcodeModalLabel" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-qr-code-scan me-2" style="color: var(--pink-primary);"></i>Scan / Foto Barcode & QR Code
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                
                <!-- Nav Tabs Pilihan Metode -->
                <ul class="nav nav-pills nav-fill mb-3 p-1" style="background: var(--pink-soft-2); border: 1px solid var(--border-soft); border-radius: 14px;" id="barcodeTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active font-semibold small py-2" id="camera-tab" data-bs-toggle="tab" data-bs-target="#camera-pane" type="button" role="tab" style="border-radius: 10px;">
                            <i class="bi bi-camera-video-fill me-1"></i> Live Kamera
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link font-semibold small py-2" id="file-tab" data-bs-toggle="tab" data-bs-target="#file-pane" type="button" role="tab" style="border-radius: 10px;">
                            <i class="bi bi-image-fill me-1"></i> Upload Foto
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mb-3" id="barcodeTabContent">
                    <!-- Tab 1: Live Kamera -->
                    <div class="tab-pane fade show active" id="camera-pane" role="tabpanel">
                        <div id="scanner" style="width: 100%; min-height: 260px; background: #f9f9f9; border-radius: 14px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1.5px solid var(--border-soft);">
                            <div class="text-center p-4">
                                <i class="bi bi-camera fs-1 text-muted mb-2"></i>
                                <p class="text-muted mb-0 font-semibold" style="font-size: 12px;">Arahkan kamera ke QR Code atau Barcode transaksi...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Upload Foto -->
                    <div class="tab-pane fade" id="file-pane" role="tabpanel">
                        <div class="text-center p-4" style="border: 2px dashed var(--pink-primary); background: var(--pink-soft-2); border-radius: 14px;">
                            <i class="bi bi-cloud-arrow-up-fill fs-1" style="color: var(--pink-primary);"></i>
                            <h6 class="fw-bold mt-2 mb-1" style="color: var(--ink);">Pilih Foto atau Ambil Jepretan</h6>
                            <p class="text-muted font-semibold mb-3" style="font-size: 11.5px;">Upload screenshot pesanan atau foto resi/QR Code yang jelas</p>
                            
                            <label for="barcodeFileInput" class="btn btn-yb px-4 py-2 font-semibold text-white shadow-sm" style="cursor: pointer; border-radius: 12px; font-size: 12.5px;">
                                <i class="bi bi-folder2-open me-1"></i> Pilih File Foto / Kamera
                            </label>
                            <input type="file" id="barcodeFileInput" accept="image/*" class="d-none">
                        </div>
                        
                        <!-- Preview Hasil Foto -->
                        <div id="imagePreviewArea" class="text-center mt-3 d-none">
                            <img id="previewImg" src="" alt="Preview Barcode" class="img-fluid mb-2" style="max-height: 180px; border-radius: 12px; border: 1px solid var(--border-soft);">
                            <div id="decodeStatus" class="font-semibold text-muted" style="font-size: 12px;">Sedang membaca kode...</div>
                        </div>
                    </div>
                </div>

                <div id="scanner-hidden" style="display: none;"></div>

                <!-- Manual Input Fallback -->
                <div class="pt-3" style="border-top: 1.5px dashed var(--border-soft);">
                    <label class="form-label font-bold text-muted mb-1" style="font-size: 11px;">ATAU KETIK MANUAL:</label>
                    <form id="manualSearchForm" method="GET" action="{{ route('retur.select') }}" class="mb-0">
                        <div class="input-group">
                            <input type="text" id="barcodeSearchInput" name="search" class="form-control font-semibold" placeholder="Ketik nomor transaksi / resi..." style="border-radius: 12px 0 0 12px; border-color: var(--border-soft); font-size: 12.5px;">
                            <button class="btn btn-yb px-3 font-semibold text-white" type="submit" id="searchBarcodeBtn" style="border-radius: 0 12px 12px 0;">
                                <i class="bi bi-search me-1"></i>Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer py-2" style="border-top: 1px solid var(--border-soft);">
                <button type="button" class="btn btn-yb-outline px-4 font-semibold" data-bs-dismiss="modal" style="border-radius: 10px; font-size: 12px;">Tutup</button>
            </div>
        </div>
    </div>
</div>

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
                if (window.notify) notify.error('Library scanner gagal dimuat.');
                return;
            }

            const imgURL = URL.createObjectURL(file);
            const previewArea = document.getElementById('imagePreviewArea');
            const previewImg = document.getElementById('previewImg');
            const decodeStatus = document.getElementById('decodeStatus');

            previewImg.src = imgURL;
            previewArea.classList.remove('d-none');
            decodeStatus.innerHTML = '<span style="color: var(--pink-primary);"><i class="bi bi-hourglass-split me-1"></i>Memindai barcode dari foto...</span>';

            const fileScanner = new Html5Qrcode("scanner-hidden");
            fileScanner.scanFile(file, true)
                .then(decodedText => {
                    decodeStatus.innerHTML = `<span style="color: #52976D;"><i class="bi bi-check-circle-fill me-1"></i>Terbaca: <strong>${decodedText}</strong></span>`;
                    setTimeout(() => { processDetectedBarcode(decodedText); }, 800);
                })
                .catch(err => {
                    decodeStatus.innerHTML = '<span style="color: #E05270;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Kode tidak terbaca.</span>';
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