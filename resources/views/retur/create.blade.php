@extends('layouts.app')

@section('title', 'Buat Retur - Yourbaestyle')

@section('content')
<div class="container-fluid py-4" style="max-width: 900px; margin: 0 auto;">
    
    <!-- Tombol Kembali -->
    <div class="mb-3">
        <a href="{{ route('retur.select') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Kartu Form Retur -->
    <div class="card border-0 shadow-sm rounded-4" style="background: #fff; overflow: hidden;">
        <div class="card-header bg-white py-3 px-4 border-bottom" style="border-color: #F7E5EA;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-reply-fill me-2" style="color: #EC95A8;"></i>Buat Retur Produk
                </h5>
            </div>
        </div>

        <div class="card-body p-4">


            <!-- INFO TRANSAKSI YANG DIPILIH -->
            @if($transaksi)
            <div class="alert rounded-4 mb-4 p-3" style="background: #E8F4F8; border: none;">
                <div class="row g-2">
                    <div class="col-md-3">
                        <small class="text-muted">No. Transaksi</small>
                        <div class="fw-bold text-dark">{{ $transaksi->no_pesanan ?? $transaksi->kode_transaksi ?? '-' }}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Tipe</small>
                        <div class="fw-bold">
                            <span class="badge" style="background: {{ $transaksiType === 'online' ? '#FFE6E6' : '#E8F7EE' }}; color: {{ $transaksiType === 'online' ? '#DC3545' : '#52976D' }};">
                                {{ $transaksiType === 'online' ? 'ONLINE' : 'POS' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Pembeli/Kasir</small>
                        <div class="fw-bold text-dark">{{ $transaksi->nama_pembeli ?? $transaksi->user?->name ?? '-' }}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Tanggal</small>
                        <div class="fw-bold text-dark">{{ $transaksi->tanggal ? \Carbon\Carbon::parse($transaksi->tanggal)->format('d-m-Y') : '-' }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- FORM RETUR -->
            <form action="{{ route('retur.store') }}" method="POST" id="formRetur">
                @csrf

                <!-- Hidden: Transaksi Info -->
                <input type="hidden" name="transaksi_type" value="{{ $transaksiType }}">
                <input type="hidden" name="transaksi_id" value="{{ $transaksi->id }}">

                <!-- BAGIAN 1: PILIH PRODUK (OTOMATIS JIKA CUMA 1 BARANG) -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Produk yang Di-retur <span class="text-danger">*</span></label>
                    
                    @if($detail && $detail->count() === 1)
                        <?php $singleItem = $detail->first(); ?>
                        <!-- JIKA CUMA 1 BARANG: Tampilkan sebagai kotak info rapi dan input hidden -->
                        <div class="p-3 rounded-3" style="background: #FFF5F7; border: 1px solid #F7E5EA;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">
                                        {{ $singleItem->produk->nama_produk ?? $singleItem->nama_produk_history ?? 'Produk' }}
                                        @if($singleItem->variasi) <span class="text-muted">({{ $singleItem->variasi }})</span> @endif
                                    </h6>
                                    <span class="badge bg-light text-dark border me-1">Total Beli: <strong>{{ $singleItem->qty }} pcs</strong></span>
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle font-semibold">Stok Inventaris Saat Ini: {{ $singleItem->produk->stok ?? 0 }} pcs</span>
                                </div>
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                        </div>
                        
                        <!-- Hidden Input untuk dikirim ke Backend -->
                        <input type="hidden" name="id_produk" id="produkSelect" value="{{ $singleItem->id_produk }}" data-max="{{ $singleItem->qty }}" data-auto="true">
                        
                    @elseif($detail && $detail->count() > 1)
                        <!-- JIKA LEBIH DARI 1 BARANG: Tampilkan Dropdown -->
                        <select name="id_produk" class="form-select" id="produkSelect" required>
                            <option value="" data-max="0">-- Pilih Produk --</option>
                            @foreach($detail as $d)
                            <option value="{{ $d->id_produk }}" 
                                    data-max="{{ $d->qty }}" 
                                    data-name="{{ $d->produk->nama_produk ?? $d->nama_produk_history ?? 'Produk' }}">
                                {{ $d->produk->nama_produk ?? $d->nama_produk_history ?? 'Produk Telah Dihapus' }} 
                                @if($d->variasi) ({{ $d->variasi }}) @endif
                                — Beli: {{ $d->qty }} pcs | Stok Inventaris Saat Ini: {{ $d->produk->stok ?? 0 }} pcs
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih salah satu produk yang akan di-retur dari transaksi ini.</small>
                    @else
                        <div class="alert alert-warning rounded-3">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Tidak ada detail produk pada transaksi ini.
                        </div>
                    @endif
                </div>

                <!-- BAGIAN 2: QTY RETUR -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Jumlah Produk yang Di-Retur <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" name="qty" id="qtyInput" class="form-control" min="1" max="1" value="1" required disabled>
                        <span class="input-group-text bg-light">pcs</span>
                    </div>
                    <small class="text-primary fw-bold" id="qtyHelperText">Silakan pilih produk terlebih dahulu.</small>
                </div>

                <!-- BAGIAN 3: ALASAN RETUR -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Alasan Retur <span class="text-danger">*</span></label>
                    <select name="alasan" class="form-select" required>
                        <option value="">-- Pilih Alasan --</option>
                        <option value="cacat_produksi">Cacat Produksi / Rusak</option>
                        <option value="tidak_sesuai_pesanan">Tidak Sesuai Pesanan (Salah Warna/Model)</option>
                        <option value="salah_ukuran">Salah Ukuran (Kebesaran/Kekecilan)</option>
                        <option value="rusak_pengiriman">Rusak saat Pengiriman</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <!-- BAGIAN 4: KONDISI BARANG -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Kondisi Barang <span class="text-danger">*</span></label>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-check p-3 rounded-3 w-100 d-block" for="layakJual" style="background: #E8F7EE; border: 2px solid #C3EEDB; cursor: pointer;">
                                <input class="form-check-input ms-0 me-2" type="radio" name="kondisi_barang" value="layak_jual" id="layakJual" required>
                                <span class="fw-bold text-dark">
                                    <i class="bi bi-check-circle me-1" style="color: #52976D;"></i>Layak Jual
                                </span>
                                <small class="d-block text-muted mt-1">Barang masih bagus dan akan dikembalikan ke stok inventaris.</small>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="form-check p-3 rounded-3 w-100 d-block" for="tidakLayak" style="background: #FFE6E6; border: 2px solid #F7C9D3; cursor: pointer;">
                                <input class="form-check-input ms-0 me-2" type="radio" name="kondisi_barang" value="tidak_layak" id="tidakLayak" required>
                                <span class="fw-bold text-dark">
                                    <i class="bi bi-x-circle me-1" style="color: #DC3545;"></i>Tidak Layak Jual
                                </span>
                                <small class="d-block text-muted mt-1">Barang rusak/cacat dan akan dihitung sebagai kerugian/penyusutan.</small>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN 5: TIPE RETUR & BARANG PENGGANTI (TUKAR BARANG) -->
                <div class="mb-4 p-3 rounded-4" style="background: #FDF4F6; border: 1.5px solid #F7D4DD;">
                    <label class="form-label fw-bold text-dark d-block mb-2">
                        <i class="bi bi-arrow-repeat me-1" style="color: var(--pink-primary);"></i> Opsi / Tipe Retur <span class="text-danger">*</span>
                    </label>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <div class="form-check card p-2.5 rounded-3 mb-0" style="background: #ffffff; border: 1px solid var(--border-soft);">
                                <input class="form-check-input ms-1 me-2" type="radio" name="tipe_retur" id="tipeTukar" value="tukar_barang" checked onchange="togglePengganti(true)">
                                <label class="form-check-label fw-bold text-dark" for="tipeTukar" style="cursor: pointer;">
                                    🔄 Tukar Barang (Kirim Barang Pengganti)
                                </label>
                                <small class="text-muted d-block ms-4 mt-0.5" style="font-size: 11.5px;">Barang yang diretur ditukar dengan produk lain/sejenis.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check card p-2.5 rounded-3 mb-0" style="background: #ffffff; border: 1px solid var(--border-soft);">
                                <input class="form-check-input ms-1 me-2" type="radio" name="tipe_retur" id="tipeKembali" value="kembali_barang" onchange="togglePengganti(false)">
                                <label class="form-check-label fw-bold text-dark" for="tipeKembali" style="cursor: pointer;">
                                    📦 Pengembalian Barang Saja (Tanpa Tukar)
                                </label>
                                <small class="text-muted d-block ms-4 mt-0.5" style="font-size: 11.5px;">Barang diretur tanpa pengiriman produk baru pengganti.</small>
                            </div>
                        </div>
                    </div>

                    <!-- SELECTOR BARANG PENGGANTI -->
                    <div id="sectionPengganti">
                        <label class="form-label fw-bold text-dark" style="font-size: 13px;">
                            Pilih Produk Pengganti <span class="text-danger">*</span>
                        </label>
                        <select name="id_produk_pengganti" id="produkPenggantiSelect" class="form-select font-semibold" onchange="checkStokPengganti()" required>
                            <option value="" data-stok="0">— Pilih Barang Pengganti Dari Katalog —</option>
                            @foreach($semuaProduk as $p)
                                <option value="{{ $p->id }}" data-stok="{{ $p->stok }}" data-nama="{{ $p->nama_produk }}">
                                    {{ $p->nama_produk }} (Kode: {{ $p->kode_produk }}) — Stok Tersedia: {{ $p->stok }} pcs
                                </option>
                            @endforeach
                        </select>
                        <div id="stokPenggantiInfo" class="mt-2 font-semibold" style="font-size: 12px;">
                            <span class="text-muted"><i class="bi bi-info-circle me-1"></i>Pilih produk pengganti yang akan dikirimkan ke pelanggan.</span>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN 6: TANGGAL RETUR -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Tanggal Retur <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>

                <!-- BAGIAN 7: ONGKIR RETUR (OPTIONAL) -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Ongkir Retur (Optional)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">Rp</span>
                        <input type="number" name="ongkir_retur" class="form-control" min="0" value="{{ old('ongkir_retur', 0) }}">
                    </div>
                    <small class="text-muted">Biaya pengiriman barang retur (jika ditanggung atau perlu dicatat).</small>
                </div>

                <!-- TOMBOL ACTION -->
                <div class="d-flex gap-2 justify-content-between pt-3">
                    <a href="{{ route('retur.select') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="btnSubmit">
                        <i class="bi bi-check-lg me-1"></i> Simpan Retur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
function togglePengganti(show) {
    const sec = document.getElementById('sectionPengganti');
    const select = document.getElementById('produkPenggantiSelect');
    if (show) {
        sec.style.display = 'block';
        select.setAttribute('required', 'required');
    } else {
        sec.style.display = 'none';
        select.removeAttribute('required');
        select.value = '';
        document.getElementById('stokPenggantiInfo').innerHTML = '';
    }
}

function checkStokPengganti() {
    const select = document.getElementById('produkPenggantiSelect');
    const info = document.getElementById('stokPenggantiInfo');
    const qtyInput = document.getElementById('qtyInput');
    const qtyNeed = parseInt(qtyInput ? qtyInput.value : 1) || 1;
    
    if (!select.value) {
        info.innerHTML = '<span class="text-muted"><i class="bi bi-info-circle me-1"></i>Pilih produk pengganti yang akan dikirimkan ke pelanggan.</span>';
        return;
    }
    
    const selectedOption = select.options[select.selectedIndex];
    const stok = parseInt(selectedOption.getAttribute('data-stok')) || 0;
    const nama = selectedOption.getAttribute('data-nama') || 'Produk';

    if (stok < qtyNeed) {
        info.innerHTML = `<div class="alert alert-danger p-2 mb-0 rounded-3" style="font-size: 12px;"><i class="bi bi-exclamation-triangle-fill me-1"></i> <strong>STOK TIDAK MENCUKUPI!</strong> Stok <strong>${nama}</strong> saat ini hanya ${stok} pcs (Dibutuhkan: ${qtyNeed} pcs). Pilih produk pengganti lain atau isi stok terlebih dahulu.</div>`;
    } else {
        info.innerHTML = `<div class="alert alert-success p-2 mb-0 rounded-3" style="font-size: 12px;"><i class="bi bi-check-circle-fill me-1"></i> Stok <strong>${nama}</strong> aman (${stok} pcs tersedia). Stok akan otomatis dipotong ${qtyNeed} pcs setelah retur disimpan.</div>`;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const produkSelect = document.getElementById('produkSelect');
    const qtyInput = document.getElementById('qtyInput');
    const qtyHelperText = document.getElementById('qtyHelperText');

    function updateQtyState(maxQty) {
        if (maxQty > 0) {
            qtyInput.removeAttribute('disabled');
            qtyInput.setAttribute('max', maxQty);
            qtyInput.value = 1; 
            qtyHelperText.innerHTML = `<i class="bi bi-info-circle me-1"></i>Maksimal yang dapat diretur untuk produk ini adalah <strong>${maxQty} pcs</strong>.`;
            qtyHelperText.className = 'text-success small fw-bold mt-1 d-block';
        } else {
            qtyInput.setAttribute('disabled', 'disabled');
            qtyInput.value = '';
            qtyHelperText.innerHTML = 'Silakan pilih produk terlebih dahulu.';
            qtyHelperText.className = 'text-primary small fw-bold mt-1 d-block';
        }
    }

    if (produkSelect && qtyInput) {
        // Cek jika produknya otomatis terpilih karena cuma ada 1 macam barang
        if (produkSelect.getAttribute('data-auto') === 'true') {
            const maxQty = parseInt(produkSelect.getAttribute('data-max')) || 0;
            updateQtyState(maxQty);
        }

        // Kontrol perubahan jika ada lebih dari 1 macam barang (menggunakan dropdown)
        produkSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const maxQty = parseInt(selectedOption.getAttribute('data-max')) || 0;
            updateQtyState(maxQty);
        });

        // Cegah input melebihi batas maksimal atau kurang dari 1
        qtyInput.addEventListener('input', function() {
            const max = parseInt(this.getAttribute('max')) || 1;
            let val = parseInt(this.value) || 0;

            if (val > max) {
                alert(`Jumlah retur tidak boleh melebihi jumlah pembelian (${max} pcs).`);
                this.value = max;
            } else if (val < 1 && this.value !== '') {
                this.value = 1;
            }
            checkStokPengganti();
        });
    }

    // Proteksi double submit pada tombol simpan
    const formRetur = document.getElementById('formRetur');
    if(formRetur) {
        formRetur.addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
        });
    }
});
</script>
@endpush