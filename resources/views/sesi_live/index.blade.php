@extends('layouts.app')

@section('title', 'Mapping Sesi Live — Yourbaestyle')

@section('content')
<!-- TAMBAHKAN CSS SELECT2 VIA CDN -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<div class="container-fluid py-3" style="max-width: 1300px; margin: 0 auto;">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="font-weight-bold mb-1 text-dark" style="font-family: 'Quicksand', sans-serif;">
                <i class="bi bi-broadcast text-danger me-2"></i>Setting Mapping Kode Sesi Live
            </h4>
            <p class="text-muted small mb-0">Halaman kerja Co-Host untuk memetakan kode variasi secara <em>real-time</em> saat live streaming berlangsung.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-4">
        <!-- KOLOM KIRI: FORM INPUT CEPAT CO-HOST -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4" style="background: #fff; overflow: hidden;">
                <div class="card-header bg-white py-3 px-4 border-bottom" style="border-color: #F7E5EA !important;">
                    <h6 class="mb-0 font-weight-bold text-dark"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Input Kode Live Baru</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ url('/sesi-live') }}" method="POST" autocomplete="off">
                        @csrf
                        
                        <!-- 1. INPUT TANGGAL -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Tanggal Sesi Live <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_live" class="form-control rounded-3" value="{{ $tanggal }}" onchange="window.location.href='?tanggal='+this.value;" required>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">Ganti tanggal untuk melihat jadwal sesi di hari lain.</small>
                        </div>

                        <!-- 2. DROPDOWN PILIH SESI / JAM LIVE -->
<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <label class="form-label fw-bold text-dark small mb-0">Pilih Sesi / Jam Live <span class="text-danger">*</span></label>
        <!-- TOMBOL BIKIN SESI BARU -->
        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0 fw-bold shadow-sm" style="font-size: 11px;" data-bs-toggle="modal" data-bs-target="#modalBuatSesi">
            <i class="bi bi-plus-lg"></i> Bikin Sesi Baru
        </button>
    </div>
    <select name="id_sesi_live" class="form-select rounded-3 font-weight-bold" required style="border: 2px solid #EC95A8; background: #FFF5F7;">
        <option value="">-- Pilih Sesi Live --</option>
        @foreach($sesiLives as $sesi)
            <option value="{{ $sesi->id }}">
                {{ $sesi->nama_sesi }} ({{ \Carbon\Carbon::parse($sesi->jam_mulai)->format('H:i') }} - {{ $sesi->jam_selesai ? \Carbon\Carbon::parse($sesi->jam_selesai)->format('H:i') : 'Selesai' }}) 
                [{{ strtoupper($sesi->platform) }}]
            </option>
        @endforeach
    </select>
    @if(count($sesiLives) == 0)
        <small class="text-danger d-block mt-1 fw-bold" style="font-size: 11px;">⚠️ Belum ada jadwal di tanggal ini. Klik tombol <strong>"+ Bikin Sesi Baru"</strong> di atas!</small>
    @else
        <small class="text-success d-block mt-1 fw-bold" style="font-size: 11px;">✓ Pilih sesi streaming yang sedang berlangsung.</small>
    @endif
</div>

                        <!-- 3. INPUT KODE VARIATION -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Kode Live / Variasi <span class="text-danger">*</span></label>
                            <input type="text" name="kode_live" class="form-control form-control-lg rounded-3 text-uppercase font-weight-bold" style="border: 2px solid #EC95A8; background: #FFF5F7;" placeholder="CONTOH: FIX 10 / A1 / KUNING" required autofocus>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">Ketik kode persis seperti yang akan diketik pembeli saat checkout.</small>
                        </div>

                        <!-- 4. DROP DOWN PRODUK FISIK -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Pilih Produk Fisik <span class="text-danger">*</span></label>
                            <select name="id_produk" id="selectProdukSearch" class="form-select" required>
                                <option value="">-- Ketik Nama / Kode Barang --</option>
                                @foreach($produks as $p)
                                    <option value="{{ $p->id }}">
                                        {{ $p->nama_produk }} — (Stok: {{ $p->stok }} )
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">💡 Tips: Bisa ketik nama barang atau kode produknya langsung!</small>
                        </div>

                        <!-- 5. HARGA LIVE -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small">Harga Khusus Live (Opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text rounded-start-3 bg-light">Rp</span>
                                <input type="number" name="harga_live" class="form-control rounded-end-3" placeholder="Kosongkan jika harga normal">
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-lg font-weight-bold text-white rounded-pill shadow-sm" style="background: #0d6efd; font-size: 15px;">
                                <i class="bi bi-plus-circle me-1"></i> Simpan Mapping (Enter)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: DAFTAR MAPPING AKTIF HARI INI -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4" style="background: #fff; overflow: hidden;">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color: #F7E5EA !important;">
                    <h6 class="mb-0 font-weight-bold text-dark">
                        <i class="bi bi-list-check me-2" style="color: #EC95A8;"></i>Daftar Mapping Terpasang ({{ count($mapped) }} Kode)
                    </h6>
                    
                    <!-- Filter Tanggal & Sesi di Tabel Kanan -->
                    <form action="{{ url('/sesi-live') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="date" name="tanggal" class="form-control form-control-sm rounded-pill px-3" value="{{ $tanggal }}" onchange="this.form.submit()" style="border-color: #EC95A8;">
                        
                        <select name="filter_sesi" class="form-select form-select-sm rounded-pill px-3" onchange="this.form.submit()" style="border-color: #EC95A8; min-width: 150px;">
                            <option value="">Semua Sesi</option>
                            @foreach($sesiLives as $sesi)
                                <option value="{{ $sesi->id }}" {{ $sesiSelected == $sesi->id ? 'selected' : '' }}>
                                    {{ $sesi->nama_sesi }} ({{ \Carbon\Carbon::parse($sesi->jam_mulai)->format('H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap" style="font-size: 13.5px;">
                            <thead style="background-color: #FFF5F7; color: #7A686D; font-size: 11.5px; text-transform: uppercase;">
                                <tr>
                                    <th class="py-3 ps-4 text-center border-0" style="width: 15%;">Kode Live</th>
                                    <th class="py-3 border-0" style="width: 45%;">Terhubung ke Produk</th>
                                    <th class="py-3 text-end border-0" style="width: 20%;">Harga Live</th>
                                    <th class="py-3 pe-4 text-center border-0" style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody style="border-top: 1px solid #F7E5EA;">
                                @forelse($mapped as $row)
                                <tr>
                                    <td class="ps-4 text-center">
                                        <span class="badge bg-danger fs-6 px-3 py-2 shadow-sm rounded-3">
                                            {{ $row->kode_live }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-dark d-block" style="font-size: 14px;">{{ $row->produk->nama_produk ?? 'PRODUK TERHAPUS' }}</strong>
                                        
                                        <!-- BADGE NAMA SESI LIVE -->
                                        @if($row->sesiLive)
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-0 me-1" style="font-size: 10px;">
                                                <i class="bi bi-camera-video me-1"></i>{{ $row->sesiLive->nama_sesi }} ({{ \Carbon\Carbon::parse($row->sesiLive->jam_mulai)->format('H:i') }})
                                            </span>
                                        @endif
                                        
                                        <small class="text-muted" style="font-size: 11.5px;">Kode: <code>{{ $row->produk->kode_produk ?? '-' }}</code> | Sisa Stok: <strong class="text-primary">{{ $row->produk->stok ?? 0 }} pcs</strong></small>
                                    </td>
                                    <td class="text-end fw-bold text-success" style="font-size: 14px;">
                                        Rp {{ number_format($row->harga_live > 0 ? $row->harga_live : ($row->produk->harga_jual ?? 0), 0, ',', '.') }}
                                    </td>
                                    <td class="pe-4 text-center">
                                        <form action="{{ url('/sesi-live/' . $row->id) }}" method="POST" onsubmit="return confirm('Hapus mapping kode ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3" title="Hapus jika salah ketik">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="py-4">
                                            <div class="mb-3" style="font-size: 3rem; color: #F6C9D3;">
                                                <i class="bi bi-inbox"></i>
                                            </div>
                                            <h6 class="fw-bold mb-1 text-dark">Belum ada kode live yang didaftarkan</h6>
                                            <p class="text-muted small mb-0">Ketik kode variasi di form sebelah kiri untuk mulai memasangkan barang!</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TAMBAHKAN SCRIPT JQUERY & SELECT2 VIA CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#selectProdukSearch').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Ketik Nama / Kode Barang --',
            allowClear: true,
            width: '100%'
        });
    });
</script>

<style>
    .select2-container--bootstrap-5 .select2-selection {
        border: 1px solid #ced4da !important;
        border-radius: 0.5rem !important;
        padding: 0.375rem 0.75rem !important;
        min-height: calc(3.5rem + 2px) !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: #212529 !important;
        font-size: 13.5px !important;
        font-weight: 600 !important;
    }
    .select2-container--bootstrap-5 .select2-selection:focus,
    .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: #EC95A8 !important;
        box-shadow: 0 0 0 0.25rem rgba(236, 149, 168, 0.25) !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-color: #EC95A8 !important;
        border-radius: 0.5rem !important;
        overflow: hidden !important;
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #EC95A8 !important;
        color: white !important;
    }
</style>
<!-- MODAL BUAT JADWAL SESI LIVE -->
<div class="modal fade" id="modalBuatSesi" tabindex="-1" aria-labelledby="modalBuatSesiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-bottom" style="border-color: #F7E5EA;">
                <h5 class="modal-title fw-bold text-dark" id="modalBuatSesiLabel">
                    <i class="bi bi-calendar-plus text-danger me-2"></i>Buat Jadwal Sesi Live Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('sesi_live.store_jadwal') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Sesi Live <span class="text-danger">*</span></label>
                        <input type="text" name="nama_sesi" class="form-control rounded-3" placeholder="Contoh: Live Pagi Shopee / Live Flash Sale Malam" required autofocus>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Platform <span class="text-danger">*</span></label>
                            <select name="platform" class="form-select rounded-3" required>
                                <option value="shopee">🛒 Shopee</option>
                                <option value="tiktok">📱 TikTok Live</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_live" class="form-control rounded-3" value="{{ $tanggal }}" required>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" class="form-control rounded-3" value="08:00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Jam Selesai (Opsional)</label>
                            <input type="time" name="jam_selesai" class="form-control rounded-3" value="11:00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2" style="border-color: #F7E5EA;">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold" style="background: linear-gradient(135deg, #EC95A8, #D97B90); border: none;">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection