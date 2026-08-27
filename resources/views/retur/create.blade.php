@extends('layouts.app')

@section('title', 'Buat Retur Transaksi - Yourbaestyle')

@section('content')
<div class="container-fluid py-4" style="max-width: 1000px; margin: 0 auto;">
    
    <!-- Tombol Kembali -->
    <div class="mb-3">
        <a href="{{ route('retur.select') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Pemilihan Transaksi
        </a>
    </div>

    <!-- Kartu Form Retur -->
    <div class="card border-0 shadow-sm rounded-4" style="background: #fff; overflow: hidden;">
        <div class="card-header bg-white py-3 px-4 border-bottom" style="border-color: #F7E5EA;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-reply-fill me-2" style="color: #EC95A8;"></i>Buat Retur Produk
                </h5>
                <span class="badge rounded-pill bg-light text-secondary border px-3 py-2">
                    <i class="bi bi-box-seam me-1"></i> Multi-Item Retur Supported
                </span>
            </div>
        </div>

        <div class="card-body p-4">

            <!-- NOTIFIKASI ERROR -->
            @if(session('error'))
                <div class="alert alert-danger rounded-4 mb-4 p-3 font-semibold" style="font-size: 13px;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                </div>
            @endif

            <!-- INFO TRANSAKSI YANG DIPILIH -->
            @if($transaksi)
            <div class="alert rounded-4 mb-4 p-3" style="background: #E8F4F8; border: 1px solid #C4E3ED;">
                <div class="row g-2 align-items-center">
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block" style="font-size: 11px;">No. Transaksi / Pesanan</small>
                        <strong class="text-dark font-monospace" style="font-size: 13.5px;">{{ $transaksi->no_pesanan ?? $transaksi->kode_transaksi ?? '-' }}</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block" style="font-size: 11px;">Tipe Transaksi</small>
                        <div>
                            <span class="badge" style="background: {{ $transaksiType === 'online' ? '#FFE6E6' : '#E8F7EE' }}; color: {{ $transaksiType === 'online' ? '#DC3545' : '#52976D' }}; font-weight: 700;">
                                {{ $transaksiType === 'online' ? 'PESANAN ONLINE' : 'KASIR (POS)' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block" style="font-size: 11px;">Pembeli / Kasir</small>
                        <strong class="text-dark">{{ $transaksi->nama_pembeli ?? ($transaksi->user->name ?? '-') }}</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block" style="font-size: 11px;">Tanggal Transaksi</small>
                        <strong class="text-dark">{{ $transaksi->tanggal ? \Carbon\Carbon::parse($transaksi->tanggal)->format('d-m-Y H:i') : '-' }}</strong>
                    </div>
                </div>
            </div>
            @endif

            <!-- FORM RETUR MULTI ITEM -->
            <form action="{{ route('retur.store') }}" method="POST" id="formRetur">
                @csrf

                <!-- Hidden: Transaksi Info -->
                <input type="hidden" name="transaksi_type" value="{{ $transaksiType }}">
                <input type="hidden" name="transaksi_id" value="{{ $transaksi->id }}">

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold text-dark mb-0">
                            Pilih Produk yang Akan Di-Retur <span class="text-danger">*</span>
                        </label>
                        <small class="text-muted">Centang barang yang ingin diretur dari transaksi ini</small>
                    </div>

                    @if($detail && $detail->count() > 0)
                        <div class="d-flex flex-column gap-3">
                            @foreach($detail as $index => $item)
                                @php
                                    $namaProduk = $item->produk->nama_produk ?? $item->nama_produk_history ?? 'Produk Telah Dihapus';
                                    $kodeProduk = $item->produk->kode_produk ?? '-';
                                    $stokSkrg = $item->produk->stok ?? 0;
                                    $qtyBeli = $item->qty;
                                    $qtySudahRetur = $item->qty_retur_sebelumnya ?? 0;
                                    $sisaBolehRetur = $item->sisa_retur ?? max(0, $qtyBeli - $qtySudahRetur);
                                    $isDisabled = ($sisaBolehRetur <= 0);
                                @endphp

                                <div class="card border rounded-4 shadow-sm p-3 item-card {{ $isDisabled ? 'bg-light text-muted' : '' }}" style="border-color: #E2E8F0 !important;">
                                    
                                    <!-- Baris Header Item & Checkbox -->
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="form-check mt-1">
                                            <input class="form-check-input item-checkbox" type="checkbox" 
                                                   name="items[{{ $index }}][selected]" value="1" 
                                                   id="check_item_{{ $index }}" 
                                                   data-index="{{ $index }}"
                                                   {{ $isDisabled ? 'disabled' : '' }}
                                                   {{ (!$isDisabled && $detail->count() === 1) ? 'checked' : '' }}>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="form-check-label fw-bold text-dark fs-6 d-block mb-1" for="check_item_{{ $index }}">
                                                {{ $namaProduk }}
                                                @if(!empty($item->variasi)) <span class="text-muted font-normal" style="font-size: 13px;">({{ $item->variasi }})</span> @endif
                                            </label>
                                            
                                            <div class="d-flex flex-wrap align-items-center gap-2" style="font-size: 12px;">
                                                <span class="badge bg-secondary-subtle text-secondary border">Kode: {{ $kodeProduk }}</span>
                                                <span class="badge bg-light text-dark border">Total Beli: <strong>{{ $qtyBeli }} pcs</strong></span>
                                                @if($qtySudahRetur > 0)
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Sudah Diretur: {{ $qtySudahRetur }} pcs</span>
                                                @endif
                                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">Stok Inventaris Saat Ini: {{ $stokSkrg }} pcs</span>
                                            </div>

                                            @if($isDisabled)
                                                <div class="alert alert-warning py-1.5 px-3 rounded-3 mt-2 mb-0 font-semibold" style="font-size: 12px;">
                                                    <i class="bi bi-check-circle-fill me-1 text-success"></i> Semua Qty barang ini dalam transaksi telah habis diretur.
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Form Inputs Detail Item (Hanya aktif jika checkbox di-centang) -->
                                    @if(!$isDisabled)
                                    <div class="item-form-body mt-3 pt-3 border-top {{ $detail->count() === 1 ? '' : 'd-none' }}" id="item_body_{{ $index }}">
                                        
                                        <input type="hidden" name="items[{{ $index }}][id_produk]" value="{{ $item->id_produk }}">

                                        <div class="row g-3 mb-3">
                                            <!-- Qty Retur -->
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold small text-dark">Jumlah Retur (pcs) <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" name="items[{{ $index }}][qty]" 
                                                           class="form-control qty-input font-semibold" 
                                                           min="1" max="{{ $sisaBolehRetur }}" value="1"
                                                           data-max="{{ $sisaBolehRetur }}" data-index="{{ $index }}" required>
                                                    <span class="input-group-text bg-light">pcs</span>
                                                </div>
                                                <small class="text-success fw-bold mt-1 d-block" style="font-size: 11px;">
                                                    Maksimal: {{ $sisaBolehRetur }} pcs
                                                </small>
                                            </div>

                                            <!-- Alasan Retur -->
                                            <div class="col-md-8">
                                                <label class="form-label fw-bold small text-dark">Alasan Retur <span class="text-danger">*</span></label>
                                                <select name="items[{{ $index }}][alasan]" class="form-select font-semibold" required>
                                                    <option value="cacat_produksi">Cacat Produksi / Rusak</option>
                                                    <option value="tidak_sesuai_pesanan">Tidak Sesuai Pesanan (Salah Warna/Model)</option>
                                                    <option value="salah_ukuran">Salah Ukuran (Kebesaran/Kekecilan)</option>
                                                    <option value="rusak_pengiriman">Rusak saat Pengiriman</option>
                                                    <option value="lainnya">Lainnya</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Kondisi Barang & Tipe Retur -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small text-dark">Kondisi Barang <span class="text-danger">*</span></label>
                                                <div class="p-2.5 rounded-3" style="background: #F8FAF9; border: 1px solid #E2E8F0;">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="radio" name="items[{{ $index }}][kondisi_barang]" value="layak_jual" id="layak_{{ $index }}" checked>
                                                        <label class="form-check-label fw-bold text-success" style="font-size: 13px;" for="layak_{{ $index }}">
                                                            🟢 Layak Jual (Kembali ke Stok Inventaris)
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="items[{{ $index }}][kondisi_barang]" value="tidak_layak" id="rusak_{{ $index }}">
                                                        <label class="form-check-label fw-bold text-danger" style="font-size: 13px;" for="rusak_{{ $index }}">
                                                            🔴 Tidak Layak (Kerugian / Penyusutan)
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small text-dark">Tipe / Opsi Retur <span class="text-danger">*</span></label>
                                                <div class="p-2.5 rounded-3" style="background: #FDF5F7; border: 1px solid #F7D4DD;">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input tipe-retur-radio" type="radio" 
                                                               name="items[{{ $index }}][tipe_retur]" value="tukar_barang" 
                                                               id="tukar_{{ $index }}" data-index="{{ $index }}" checked>
                                                        <label class="form-check-label fw-bold text-dark" style="font-size: 13px;" for="tukar_{{ $index }}">
                                                            🔄 Tukar Barang (Kirim Barang Pengganti)
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input tipe-retur-radio" type="radio" 
                                                               name="items[{{ $index }}][tipe_retur]" value="kembali_barang" 
                                                               id="kembali_{{ $index }}" data-index="{{ $index }}">
                                                        <label class="form-check-label fw-bold text-dark" style="font-size: 13px;" for="kembali_{{ $index }}">
                                                            📦 Pengembalian Barang Saja (Tanpa Tukar)
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Selector Produk Pengganti -->
                                        <div class="section-pengganti p-3 rounded-3 mt-2" id="section_pengganti_{{ $index }}" style="background: #FFFDF9; border: 1.5px dashed #F3D086;">
                                            <label class="form-label fw-bold text-dark small mb-1">
                                                Pilih Produk Pengganti <span class="text-danger">*</span>
                                            </label>
                                            <select name="items[{{ $index }}][id_produk_pengganti]" 
                                                    class="form-select select-pengganti font-semibold" 
                                                    id="pengganti_select_{{ $index }}" 
                                                    data-index="{{ $index }}" required>
                                                <option value="" data-stok="0">— Pilih Produk Pengganti Dari Katalog —</option>
                                                @foreach($semuaProduk as $p)
                                                    <option value="{{ $p->id }}" data-stok="{{ $p->stok }}" data-nama="{{ $p->nama_produk }}">
                                                        {{ $p->nama_produk }} (Kode: {{ $p->kode_produk }}) — Stok Tersedia: {{ $p->stok }} pcs
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="pengganti-info mt-2 font-semibold" id="pengganti_info_{{ $index }}" style="font-size: 12px;">
                                                <span class="text-muted"><i class="bi bi-info-circle me-1"></i>Pilih barang pengganti yang dikirimkan ke pembeli.</span>
                                            </div>
                                        </div>

                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning rounded-3">
                            <i class="bi bi-exclamation-triangle me-1"></i> Tidak ada detail produk pada transaksi ini.
                        </div>
                    @endif
                </div>

                <!-- ONGKIR RETUR GLOBAL -->
                <div class="mb-4 pt-3 border-top">
                    <label class="form-label fw-bold text-dark">Ongkir Retur (Opsional)</label>
                    <div class="input-group" style="max-width: 350px;">
                        <span class="input-group-text bg-light">Rp</span>
                        <input type="number" name="ongkir_retur" class="form-control font-semibold" min="0" value="{{ old('ongkir_retur', 0) }}">
                    </div>
                    <small class="text-muted">Biaya pengiriman barang retur (jika ditanggung oleh penjual / perlu dicatat).</small>
                </div>

                <!-- TANGGAL RETUR -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Tanggal Retur <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control font-semibold" value="{{ old('tanggal', date('Y-m-d')) }}" style="max-width: 350px;" required>
                </div>

                <!-- TOMBOL ACTION -->
                <div class="d-flex gap-2 justify-content-between pt-3 border-top">
                    <a href="{{ route('retur.select') }}" class="btn btn-outline-secondary rounded-pill px-4 font-semibold">
                        <i class="bi bi-x-lg me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-yb rounded-pill px-5 fw-bold text-white shadow-sm" id="btnSubmit">
                        <i class="bi bi-check-lg me-1"></i> Simpan Transaksi Retur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Toggle item detail form body based on checkbox check state
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const idx = this.getAttribute('data-index');
            const body = document.getElementById('item_body_' + idx);
            if (!body) return;

            if (this.checked) {
                body.classList.remove('d-none');
                // Enable required attributes inside body
                body.querySelectorAll('input, select').forEach(el => {
                    if (el.classList.contains('select-pengganti')) {
                        const isTukar = document.getElementById('tukar_' + idx).checked;
                        if (isTukar) el.setAttribute('required', 'required');
                    } else if (el.type !== 'radio' && el.type !== 'hidden') {
                        el.setAttribute('required', 'required');
                    }
                });
            } else {
                body.classList.add('d-none');
                // Remove required attributes
                body.querySelectorAll('input, select').forEach(el => el.removeAttribute('required'));
            }
        });
    });

    // Toggle section pengganti based on tipe retur radio
    const tipeRadios = document.querySelectorAll('.tipe-retur-radio');
    tipeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const idx = this.getAttribute('data-index');
            const section = document.getElementById('section_pengganti_' + idx);
            const select = document.getElementById('pengganti_select_' + idx);
            if (!section || !select) return;

            if (this.value === 'tukar_barang') {
                section.style.display = 'block';
                select.setAttribute('required', 'required');
            } else {
                section.style.display = 'none';
                select.removeAttribute('required');
                select.value = '';
                document.getElementById('pengganti_info_' + idx).innerHTML = '<span class="text-muted"><i class="bi bi-info-circle me-1"></i>Pilih barang pengganti yang dikirimkan ke pembeli.</span>';
            }
        });
    });

    // Check replacement stock live
    const penggantiSelects = document.querySelectorAll('.select-pengganti');
    penggantiSelects.forEach(select => {
        select.addEventListener('change', function() {
            const idx = this.getAttribute('data-index');
            const info = document.getElementById('pengganti_info_' + idx);
            const qtyInput = document.querySelector(`input[name="items[${idx}][qty]"]`);
            const qtyNeed = parseInt(qtyInput ? qtyInput.value : 1) || 1;

            if (!this.value) {
                info.innerHTML = '<span class="text-muted"><i class="bi bi-info-circle me-1"></i>Pilih barang pengganti yang dikirimkan ke pembeli.</span>';
                return;
            }

            const selectedOption = this.options[this.selectedIndex];
            const stok = parseInt(selectedOption.getAttribute('data-stok')) || 0;
            const nama = selectedOption.getAttribute('data-nama') || 'Produk';

            if (stok < qtyNeed) {
                info.innerHTML = `<div class="alert alert-danger p-2 mb-0 rounded-3" style="font-size: 12px;"><i class="bi bi-exclamation-triangle-fill me-1"></i> <strong>STOK TIDAK MENCUKUPI!</strong> Stok <strong>${nama}</strong> saat ini hanya ${stok} pcs (Dibutuhkan: ${qtyNeed} pcs). Pilih produk pengganti lain atau isi stok terlebih dahulu.</div>`;
            } else {
                info.innerHTML = `<div class="alert alert-success p-2 mb-0 rounded-3" style="font-size: 12px;"><i class="bi bi-check-circle-fill me-1"></i> Stok <strong>${nama}</strong> aman (${stok} pcs tersedia). Stok akan otomatis dipotong ${qtyNeed} pcs setelah retur disimpan.</div>`;
            }
        });
    });

    // Validate max quantity input
    const qtyInputs = document.querySelectorAll('.qty-input');
    qtyInputs.forEach(input => {
        input.addEventListener('input', function() {
            const max = parseInt(this.getAttribute('data-max')) || 1;
            let val = parseInt(this.value) || 0;
            const idx = this.getAttribute('data-index');

            if (val > max) {
                alert(`Jumlah retur tidak boleh melebihi sisa yang dapat diretur (${max} pcs).`);
                this.value = max;
            } else if (val < 1 && this.value !== '') {
                this.value = 1;
            }

            const penggantiSelect = document.getElementById('pengganti_select_' + idx);
            if (penggantiSelect) penggantiSelect.dispatchEvent(new Event('change'));
        });
    });

    // Form submit validation & protection
    const formRetur = document.getElementById('formRetur');
    if (formRetur) {
        formRetur.addEventListener('submit', function(e) {
            const checkedItems = document.querySelectorAll('.item-checkbox:checked');
            if (checkedItems.length === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal 1 item produk yang akan di-retur.');
                return false;
            }

            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses Retur...';
        });
    }
});
</script>
@endpush