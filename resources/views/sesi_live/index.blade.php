@extends('layouts.app')

@section('title', 'Mapping Sesi Live — Yourbaestyle')

@section('content')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

<!-- Header Action -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
            <i class="bi bi-broadcast me-2" style="color: var(--pink-primary);"></i>Setting Mapping Kode Sesi Live
        </h5>
        <small class="text-muted font-semibold">Halaman kerja Co-Host untuk memetakan kode variasi secara real-time saat live streaming</small>
    </div>
    <button type="button" class="btn btn-yb-outline" data-bs-toggle="modal" data-bs-target="#modalBuatSesi">
        <i class="bi bi-plus-lg me-1"></i> Bikin Sesi Baru
    </button>
</div>


<div class="row g-4">
    <!-- KOLOM KIRI: FORM INPUT CEPAT CO-HOST -->
    <div class="col-12 col-lg-4">
        <div class="card-yb p-4 h-100">
            <h6 class="fw-bold mb-3 pb-2" style="color: var(--ink); font-family: 'Quicksand', sans-serif; border-bottom: 1.5px dashed var(--border-soft);">
                <i class="bi bi-lightning-charge me-2" style="color: var(--amber);"></i>Input Kode Live Baru
            </h6>

            <form action="{{ url('/sesi-live') }}" method="POST" autocomplete="off">
                @csrf
                
                <!-- 1. INPUT TANGGAL -->
                <div class="mb-3">
                    <label class="form-label">Tanggal Sesi Live <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_live" class="form-control" value="{{ $tanggal }}" onchange="window.location.href='?tanggal='+this.value;" required>
                </div>

                <!-- 2. DROPDOWN PILIH SESI / JAM LIVE -->
                <div class="mb-3">
                    <label class="form-label">Pilih Sesi / Jam Live <span class="text-danger">*</span></label>
                    <select name="id_sesi_live" class="form-select font-semibold" required style="border-color: var(--pink-primary); background: var(--pink-soft-2);">
                        <option value="">-- Pilih Sesi Live --</option>
                        @foreach($sesiLives as $sesi)
                            <option value="{{ $sesi->id }}">
                                {{ $sesi->nama_sesi }} ({{ \Carbon\Carbon::parse($sesi->jam_mulai)->format('H:i') }}) [{{ strtoupper($sesi->platform) }}]
                            </option>
                        @endforeach
                    </select>
                    @if(count($sesiLives) == 0)
                        <small class="text-danger d-block mt-1 font-semibold" style="font-size: 11px;">⚠️ Belum ada jadwal di tanggal ini. Klik "+ Bikin Sesi Baru"!</small>
                    @endif
                </div>

                <!-- 3. INPUT KODE VARIATION -->
                <div class="mb-3">
                    <label class="form-label">Kode Live / Variasi <span class="text-danger">*</span></label>
                    <input type="text" name="kode_live" class="form-control form-control-lg text-uppercase font-semibold" style="border-color: var(--pink-primary); background: var(--pink-soft-2);" placeholder="CONTOH: FIX 10 / A1 / KUNING" required autofocus>
                    <small class="text-muted d-block mt-1" style="font-size: 11px;">Ketik kode persis seperti yang diketik pembeli saat checkout.</small>
                </div>

                <!-- 4. DROP DOWN PRODUK FISIK -->
                <div class="mb-3">
                    <label class="form-label">Pilih Produk Fisik <span class="text-danger">*</span></label>
                    <select name="id_produk" id="selectProdukSearch" class="form-select" required>
                        <option value="">-- Ketik Nama / Kode Barang --</option>
                        @foreach($produks as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->nama_produk }} — (Stok: {{ $p->stok }} pcs)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 5. HARGA LIVE -->
                <div class="mb-4">
                    <label class="form-label">Harga Khusus Live (Opsional)</label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0" style="border-color: var(--border-soft); background: var(--pink-soft-2); border-radius: 16px 0 0 16px;">Rp</span>
                        <input type="text" name="harga_live" class="form-control border-start-0 input-rupiah" data-type="rupiah" placeholder="Kosongkan jika harga normal" style="border-radius: 0 16px 16px 0;">
                    </div>
                </div>

                <button type="submit" class="btn btn-yb w-100">
                    <i class="bi bi-plus-circle me-1"></i> Simpan Mapping
                </button>
            </form>
        </div>
    </div>

    <!-- KOLOM KANAN: DAFTAR MAPPING AKTIF -->
    <div class="col-12 col-lg-8">
        <div class="card-yb p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 pb-2" style="border-bottom: 1.5px dashed var(--border-soft);">
                <h6 class="fw-bold mb-0" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-list-check me-2" style="color: var(--pink-primary);"></i>Daftar Mapping Terpasang ({{ count($mapped) }} Kode)
                </h6>
                
                <form action="{{ url('/sesi-live') }}" method="GET" class="d-flex align-items-center gap-2">
                    <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ $tanggal }}" onchange="this.form.submit()" style="max-width: 150px;">
                    <select name="filter_sesi" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 140px;">
                        <option value="">Semua Sesi</option>
                        @foreach($sesiLives as $sesi)
                            <option value="{{ $sesi->id }}" {{ $sesiSelected == $sesi->id ? 'selected' : '' }}>
                                {{ $sesi->nama_sesi }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-yb align-middle">
                    <thead>
                        <tr>
                            <th class="text-center">Kode Live</th>
                            <th>Terhubung ke Produk</th>
                            <th class="text-end">Harga Live</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mapped as $row)
                        <tr>
                            <td class="text-center">
                                <span class="badge" style="background: var(--pink-primary); color: #fff; font-size: 13px; padding: 6px 14px;">
                                    {{ $row->kode_live }}
                                </span>
                            </td>
                            <td>
                                <strong class="d-block" style="color: var(--ink);">{{ $row->produk->nama_produk ?? 'Produk Terhapus' }}</strong>
                                @if($row->sesiLive)
                                    <span class="badge" style="background: var(--pink-soft); color: var(--pink-primary-dark); font-size: 10px;">
                                        <i class="bi bi-camera-video me-1"></i>{{ $row->sesiLive->nama_sesi }}
                                    </span>
                                @endif
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Stok: <strong style="color: #52976D;">{{ $row->produk->stok ?? 0 }} pcs</strong></small>
                            </td>
                            <td class="text-end fw-bold" style="color: #52976D;">
                                Rp {{ number_format($row->harga_live > 0 ? $row->harga_live : ($row->produk->harga_jual ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <form action="{{ url('/sesi-live/' . $row->id) }}" method="POST" onsubmit="return confirm('Hapus mapping kode ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Hapus mapping">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--border-soft);"></i>
                                <span class="font-semibold">Belum ada kode live yang didaftarkan pada sesi ini.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL BUAT JADWAL SESI LIVE -->
<div class="modal fade" id="modalBuatSesi" tabindex="-1" aria-labelledby="modalBuatSesiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-yb p-2 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalBuatSesiLabel" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-calendar-plus me-2" style="color: var(--pink-primary);"></i>Buat Jadwal Sesi Live Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('sesi_live.store_jadwal') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Sesi Live <span class="text-danger">*</span></label>
                        <input type="text" name="nama_sesi" class="form-control" placeholder="Contoh: Live Pagi Shopee / Live Flash Sale" required autofocus>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Platform <span class="text-danger">*</span></label>
                            <select name="platform" class="form-select" required>
                                <option value="shopee">🛒 Shopee</option>
                                <option value="tiktok">📱 TikTok Live</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_live" class="form-control" value="{{ $tanggal }}" required>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" class="form-control" value="08:00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Jam Selesai (Opsional)</label>
                            <input type="time" name="jam_selesai" class="form-control" value="11:00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-yb-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-yb">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
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
@endpush

@endsection