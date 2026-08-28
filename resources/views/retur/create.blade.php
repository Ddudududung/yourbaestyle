@extends('layouts.app')

@section('title', 'Buat Retur Produk — Yourbaestyle')

@section('content')
<div class="container-fluid p-0" style="max-width: 1000px; margin: 0 auto;">
    
    <!-- HEADER HALAMAN -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1 fw-bold" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                <i class="bi bi-arrow-return-left me-2" style="color: var(--pink-primary);"></i>Buat Retur Produk
            </h4>
            <small class="text-muted font-semibold" style="font-size: 12.5px;">Penukaran barang retur transaksi dengan produk pengganti katalog</small>
        </div>
        <div>
            <a href="{{ route('retur.select') }}" class="btn btn-yb-outline px-3 py-2 text-decoration-none font-semibold shadow-2xs" style="border-radius: 12px; font-size: 12.5px;">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Pilih Transaksi
            </a>
        </div>
    </div>

    <!-- NOTIFIKASI ERROR -->
    @if(session('error'))
        <div class="p-3 mb-4 d-flex align-items-center gap-2" style="background: #FFF0F2; border: 1.5px solid #F5B8C5; border-radius: 16px; color: #A6243F; font-size: 13px; font-weight: 600;">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-1" style="color: #E05270;"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- INFO TRANSAKSI -->
    @if($transaksi)
    <div class="card-yb p-3 p-md-4 mb-4">
        <div class="d-flex align-items-center gap-2 mb-3 pb-2.5" style="border-bottom: 1.5px dashed var(--border-soft);">
            <h6 class="mb-0 fw-bold" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                <i class="bi bi-receipt me-2" style="color: var(--pink-primary);"></i>Informasi Transaksi Utama
            </h6>
        </div>
        
        <div class="row g-3 font-semibold" style="font-size: 12.5px;">
            <div class="col-6 col-md-3">
                <small class="text-muted d-block" style="font-size: 11px;">No. Transaksi / Pesanan</small>
                <code class="fw-bold" style="color: var(--pink-primary-dark); font-size: 12.5px;">{{ $transaksi->no_pesanan ?? $transaksi->kode_transaksi ?? '-' }}</code>
            </div>
            <div class="col-6 col-md-3">
                <small class="text-muted d-block" style="font-size: 11px;">Tipe Transaksi</small>
                <span class="badge px-2.5 py-1 font-semibold" style="background: {{ $transaksiType === 'online' ? '#FFF0F3' : '#E8F7EE' }}; color: {{ $transaksiType === 'online' ? 'var(--pink-primary-dark)' : '#1E7E34' }}; border: 1px solid var(--border-soft); border-radius: 8px;">
                    {{ $transaksiType === 'online' ? 'Pesanan Online' : 'Kasir (POS)' }}
                </span>
            </div>
            <div class="col-6 col-md-3">
                <small class="text-muted d-block" style="font-size: 11px;">Pembeli / Kasir</small>
                <strong style="color: var(--ink);">{{ $transaksi->nama_pembeli ?? ($transaksi->user->name ?? '-') }}</strong>
            </div>
            <div class="col-6 col-md-3">
                <small class="text-muted d-block" style="font-size: 11px;">Tanggal Transaksi</small>
                <strong style="color: var(--ink);">{{ $transaksi->tanggal ? \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y H:i') : '-' }}</strong>
            </div>
        </div>
    </div>
    @endif

    <!-- FORM RETUR MULTI ITEM -->
    <form action="{{ route('retur.store') }}" method="POST" id="formRetur">
        @csrf
        <input type="hidden" name="transaksi_type" value="{{ $transaksiType }}">
        <input type="hidden" name="transaksi_id" value="{{ $transaksi->id }}">

        <div class="card-yb p-3 p-md-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2.5" style="border-bottom: 1.5px dashed var(--border-soft);">
                <h6 class="mb-0 fw-bold" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-box-seam me-2" style="color: var(--pink-primary);"></i>Pilih Produk yang Akan Di-Retur
                </h6>
                <small class="text-muted font-semibold" style="font-size: 11.5px;">Centang item yang ingin ditukar dari transaksi ini</small>
            </div>

            @if($detail && $detail->count() > 0)
                <div class="d-flex flex-column gap-3">
                    @foreach($detail as $index => $item)
                        @php
                            $namaProduk = $item->produk->nama_produk ?? $item->nama_produk_history ?? 'Produk Telah Dihapus';
                            $kodeProduk = $item->produk->kode_produk ?? '-';
                            $hargaJualRetur = (float)($item->harga_satuan ?? $item->produk->harga_jual ?? 0);
                            $stokSkrg = $item->produk->stok ?? 0;
                            $qtyBeli = $item->qty;
                            $qtySudahRetur = $item->qty_retur_sebelumnya ?? 0;
                            $sisaBolehRetur = $item->sisa_retur ?? max(0, $qtyBeli - $qtySudahRetur);
                            $isDisabled = ($sisaBolehRetur <= 0);
                        @endphp

                        <div class="p-3.5 rounded-4 item-card {{ $isDisabled ? 'opacity-60' : '' }}" style="background: #ffffff; border: 1.5px solid var(--border-soft); border-left: 5px solid {{ $isDisabled ? '#CBD5E1' : 'var(--pink-primary)' }};">
                            
                            <!-- Header Item & Checkbox -->
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
                                    <label class="form-check-label fw-bold d-block mb-1" for="check_item_{{ $index }}" style="color: var(--ink); font-size: 14px;">
                                        {{ $namaProduk }}
                                        @if(!empty($item->variasi)) <span class="text-muted font-semibold" style="font-size: 12.5px;">({{ $item->variasi }})</span> @endif
                                    </label>
                                    
                                    <div class="d-flex flex-wrap align-items-center gap-2 font-semibold" style="font-size: 11.5px;">
                                        <span class="badge font-monospace" style="background: var(--pink-soft-2); color: var(--pink-primary-dark); border: 1px solid var(--border-soft); padding: 4px 8px;">
                                            Kode: {{ $kodeProduk }}
                                        </span>
                                        <span class="badge bg-white text-muted" style="border: 1px solid var(--border-soft); padding: 4px 8px;">
                                            Harga Jual: <strong>Rp {{ number_format($hargaJualRetur, 0, ',', '.') }}</strong>
                                        </span>
                                        <span class="badge bg-white text-muted" style="border: 1px solid var(--border-soft); padding: 4px 8px;">
                                            Beli: <strong>{{ $qtyBeli }} pcs</strong>
                                        </span>
                                        @if($qtySudahRetur > 0)
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="padding: 4px 8px;">
                                                Sudah Diretur: {{ $qtySudahRetur }} pcs
                                            </span>
                                        @endif
                                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="padding: 4px 8px;" title="Stok inventaris produk ini di toko saat ini">
                                            <i class="bi bi-box-seam me-1"></i>Stok Toko Saat Ini: <strong>{{ $stokSkrg }} pcs</strong>
                                        </span>
                                    </div>

                                    @if($isDisabled)
                                        <div class="p-2 rounded-3 mt-2 text-muted font-semibold" style="background: #F8FAFC; border: 1px solid var(--border-soft); font-size: 11.5px;">
                                            <i class="bi bi-check-circle me-1 text-success"></i> Seluruh Qty barang ini dalam transaksi telah selesai diretur.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Body Inputs Detail Item -->
                            @if(!$isDisabled)
                            <div class="item-form-body mt-3 pt-3 border-top {{ $detail->count() === 1 ? '' : 'd-none' }}" id="item_body_{{ $index }}" style="border-color: var(--border-soft) !important;">
                                
                                <input type="hidden" name="items[{{ $index }}][id_produk]" value="{{ $item->id_produk }}">
                                <input type="hidden" name="items[{{ $index }}][tipe_retur]" value="tukar_barang">
                                <input type="hidden" id="harga_asal_{{ $index }}" value="{{ $hargaJualRetur }}">

                                <div class="row g-3 mb-3">
                                    <!-- Qty Retur -->
                                    <div class="col-12 col-md-4">
                                        <label class="form-label font-semibold text-muted mb-1" style="font-size: 12px;">Jumlah Retur (pcs) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="items[{{ $index }}][qty]" 
                                                   class="form-control qty-input font-semibold" 
                                                   min="1" max="{{ $sisaBolehRetur }}" value="1"
                                                   data-max="{{ $sisaBolehRetur }}" data-index="{{ $index }}" required
                                                   style="border-radius: 12px 0 0 12px; border-color: var(--border-soft); font-size: 12.5px;">
                                            <span class="input-group-text bg-white text-muted font-semibold" style="border-radius: 0 12px 12px 0; border-color: var(--border-soft); font-size: 12px;">pcs</span>
                                        </div>
                                        <small class="text-success font-semibold mt-1 d-block" style="font-size: 11px;">
                                            Maksimal: {{ $sisaBolehRetur }} pcs
                                        </small>
                                    </div>

                                    <!-- Alasan Retur -->
                                    <div class="col-12 col-md-8">
                                        <label class="form-label font-semibold text-muted mb-1" style="font-size: 12px;">Alasan Retur <span class="text-danger">*</span></label>
                                        <select name="items[{{ $index }}][alasan]" class="form-select font-semibold" required style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px;">
                                            <option value="salah_ukuran">Salah Ukuran (Kebesaran / Kekecilan)</option>
                                            <option value="cacat_produksi">Cacat Produksi / Rusak</option>
                                            <option value="tidak_sesuai_pesanan">Tidak Sesuai Pesanan (Salah Warna / Model)</option>
                                            <option value="rusak_pengiriman">Rusak saat Pengiriman</option>
                                            <option value="lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Kondisi Barang -->
                                <div class="mb-3">
                                    <label class="form-label font-semibold text-muted mb-1" style="font-size: 12px;">Kondisi Barang yang Dikembalikan <span class="text-danger">*</span></label>
                                    <div class="p-3 rounded-3" style="background: var(--pink-soft-2); border: 1.5px solid var(--border-soft);">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="items[{{ $index }}][kondisi_barang]" value="layak_jual" id="layak_{{ $index }}" checked>
                                            <label class="form-check-label font-semibold" style="font-size: 12.5px; color: var(--ink);" for="layak_{{ $index }}">
                                                Layak Jual <span class="text-success small ms-1">(Masuk kembali ke stok toko)</span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="items[{{ $index }}][kondisi_barang]" value="tidak_layak" id="rusak_{{ $index }}">
                                            <label class="form-check-label font-semibold" style="font-size: 12.5px; color: var(--ink);" for="rusak_{{ $index }}">
                                                Tidak Layak / Rusak <span class="text-danger small ms-1">(Dicatat sebagai kerugian / penyusutan)</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Selector Produk Pengganti & Perhitungan Harga -->
                                <div class="p-3 rounded-4" style="background: #ffffff; border: 1.5px solid var(--border-soft);">
                                    <label class="form-label font-semibold mb-1" style="font-size: 12px; color: var(--ink);">
                                        Pilih Produk Pengganti <span class="text-danger">*</span>
                                    </label>
                                    <select name="items[{{ $index }}][id_produk_pengganti]" 
                                            class="form-select select-pengganti font-semibold" 
                                            id="pengganti_select_{{ $index }}" 
                                            data-index="{{ $index }}" required
                                            style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px;">
                                        <option value="" data-stok="0" data-harga="0">— Pilih Produk Pengganti Dari Katalog —</option>
                                        @foreach($semuaProduk as $p)
                                            <option value="{{ $p->id }}" data-stok="{{ $p->stok }}" data-nama="{{ $p->nama_produk }}" data-harga="{{ $p->harga_jual }}">
                                                {{ $p->nama_produk }} (Rp {{ number_format($p->harga_jual, 0, ',', '.') }}) — Stok: {{ $p->stok }} pcs
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    <!-- INFO PERHITUNGAN HARGA LOKAL -->
                                    <div class="pengganti-info mt-2 font-semibold" id="pengganti_info_{{ $index }}" style="font-size: 12px;">
                                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Pilih barang pengganti untuk melihat kalkulasi selisih harga.</small>
                                    </div>
                                </div>

                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-3 rounded-3 text-muted font-semibold" style="background: #FFF0F2; border: 1px solid #F5B8C5; font-size: 12.5px;">
                    <i class="bi bi-exclamation-circle me-1"></i> Tidak ada detail produk pada transaksi ini.
                </div>
            @endif
        </div>

        <!-- ONGKIR RETUR & TANGGAL -->
        <div class="card-yb p-3 p-md-4 mb-4">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label font-semibold text-muted mb-1" style="font-size: 12px;">Ongkir Retur (Opsional)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted font-semibold" style="border-radius: 12px 0 0 12px; border-color: var(--border-soft); font-size: 12px;">Rp</span>
                        <input type="number" name="ongkir_retur" class="form-control font-semibold" min="0" value="{{ old('ongkir_retur', 0) }}" style="border-radius: 0 12px 12px 0; border-color: var(--border-soft); font-size: 12.5px;">
                    </div>
                    <small class="text-muted font-semibold mt-1 d-block" style="font-size: 11px;">Biaya pengiriman barang retur (jika ditanggung oleh toko / perlu dicatat).</small>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label font-semibold text-muted mb-1" style="font-size: 12px;">Tanggal Retur <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control font-semibold" value="{{ old('tanggal', date('Y-m-d')) }}" required style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px;">
                </div>
            </div>
        </div>

        <!-- TOMBOL ACTION -->
        <div class="d-flex align-items-center justify-content-end gap-2 mb-5">
            <a href="{{ route('retur.select') }}" class="btn btn-yb-outline px-4 py-2 font-semibold text-decoration-none" style="border-radius: 12px; font-size: 13px;">Batal</a>
            <button type="submit" class="btn btn-yb px-4 py-2 font-semibold text-white shadow-sm" id="btnSubmit" style="border-radius: 12px; font-size: 13px;">
                <i class="bi bi-check-lg me-1.5"></i> Simpan Transaksi Retur
            </button>
        </div>
    </form>
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
                body.querySelectorAll('input, select').forEach(el => {
                    if (el.type !== 'radio' && el.type !== 'hidden') {
                        el.setAttribute('required', 'required');
                    }
                });
            } else {
                body.classList.add('d-none');
                body.querySelectorAll('input, select').forEach(el => el.removeAttribute('required'));
            }
        });
    });

    // Check replacement stock and calculate price difference live
    const penggantiSelects = document.querySelectorAll('.select-pengganti');
    penggantiSelects.forEach(select => {
        select.addEventListener('change', function() {
            const idx = this.getAttribute('data-index');
            const info = document.getElementById('pengganti_info_' + idx);
            const qtyInput = document.querySelector(`input[name="items[${idx}][qty]"]`);
            const qtyNeed = parseInt(qtyInput ? qtyInput.value : 1) || 1;
            const hargaAsal = parseFloat(document.getElementById('harga_asal_' + idx).value) || 0;

            if (!this.value) {
                info.innerHTML = '<small class="text-muted"><i class="bi bi-info-circle me-1"></i>Pilih barang pengganti untuk melihat kalkulasi selisih harga.</small>';
                return;
            }

            const selectedOption = this.options[this.selectedIndex];
            const stok = parseInt(selectedOption.getAttribute('data-stok')) || 0;
            const nama = selectedOption.getAttribute('data-nama') || 'Produk';
            const hargaPengganti = parseFloat(selectedOption.getAttribute('data-harga')) || 0;

            const totalHargaAsal = hargaAsal * qtyNeed;
            const totalHargaPengganti = hargaPengganti * qtyNeed;
            const selisih = totalHargaPengganti - totalHargaAsal;

            let textKalkulasi = '';
            if (selisih > 0) {
                textKalkulasi = `<div class="mt-1 text-warning-emphasis font-semibold"><i class="bi bi-arrow-up-circle me-1"></i> <strong>Barang Pengganti Lebih Mahal:</strong> Pembeli Tambah Bayar (Nombok) <strong>Rp ${selisih.toLocaleString('id-ID')}</strong></div>`;
            } else if (selisih < 0) {
                const sisaKredit = Math.abs(selisih);
                textKalkulasi = `<div class="mt-1 text-primary font-semibold"><i class="bi bi-arrow-down-circle me-1"></i> <strong>Barang Pengganti Lebih Murah:</strong> Sisa Kredit Pembeli <strong>Rp ${sisaKredit.toLocaleString('id-ID')}</strong> (Dijadikan Saldo Kredit Toko / Non-Tunai)</div>`;
            } else {
                textKalkulasi = `<div class="mt-1 text-success font-semibold"><i class="bi bi-check-circle me-1"></i> <strong>Tukar Impas:</strong> Harga barang pengganti sama (Selisih Rp 0)</div>`;
            }

            if (stok < qtyNeed) {
                info.innerHTML = `
                    <div class="p-2.5 rounded-3 mb-1" style="background: #FFF0F2; border: 1px solid #F5B8C5; color: #A6243F; font-size: 12px;">
                        <i class="bi bi-exclamation-triangle-fill me-1" style="color: #E05270;"></i> 
                        <strong>Stok Tidak Mencukupi!</strong> Stok <strong>${nama}</strong> saat ini hanya ${stok} pcs (Dibutuhkan: ${qtyNeed} pcs). Pilih produk pengganti lain atau isi stok terlebih dahulu.
                    </div>
                `;
            } else {
                info.innerHTML = `
                    <div class="p-2.5 rounded-3 mb-1" style="background: #E8F7EE; border: 1px solid #A3E2BB; color: #276A43; font-size: 12px;">
                        <i class="bi bi-check-circle-fill me-1" style="color: #4CAF50;"></i> 
                        Stok <strong>${nama}</strong> aman (${stok} pcs tersedia). Stok akan otomatis dipotong ${qtyNeed} pcs setelah retur disimpan.
                    </div>
                    ${textKalkulasi}
                `;
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
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5"></span> Memproses Retur...';
        });
    }
});
</script>
@endpush