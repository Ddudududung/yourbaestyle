@extends('layouts.app')

@section('title', 'Tambah Produk & HPP — Yourbaestyle')

@section('content')

<div class="card-yb p-4" style="max-width:820px; margin: 0 auto;">
    <h5 class="fw-bold mb-4" style="font-family:'Quicksand',sans-serif; color: var(--ink);">
        <i class="bi bi-box-seam-fill me-2" style="color: var(--pink-primary);"></i>Tambah Data Produk &amp; HPP
    </h5>

    <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data" id="formTambahProduk">
        @csrf

        <!-- ============================================================ -->
        <!-- BAGIAN 1: DATA PRODUK & INFORMASI MASTER DATA -->
        <!-- ============================================================ -->
        <p class="text-secondary small fw-bold text-uppercase mb-3" style="letter-spacing:.4px; font-family: 'Quicksand', sans-serif;">
            🧷 1. Informasi Produk
        </p>

        <div class="row g-3 mb-3">
            <!-- 1. Dropdown Jenis Pakaian -->
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark small">Jenis Pakaian <span class="text-danger">*</span></label>
                <select name="id_jenis_pakaian" id="id_jenis_pakaian" class="form-select select-searchable rounded-3 @error('id_jenis_pakaian') is-invalid @enderror" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="__NEW__" style="font-weight:bold; color:#d63384;">➕ + Tambah Jenis Baru...</option>
                    @foreach($jenisPakaian as $jp)
                        <option value="{{ $jp->id }}" data-nama="{{ $jp->nama }}" {{ old('id_jenis_pakaian') == $jp->id ? 'selected' : '' }}>
                            {{ $jp->nama }} ({{ $jp->kode }})
                        </option>
                    @endforeach
                </select>
                <div id="container_jenis_baru" style="display: {{ old('id_jenis_pakaian') == '__NEW__' ? 'block' : 'none' }}; border-color: #ec95a8;" class="mt-2 p-2 rounded-3 bg-light border">
                    <div class="row g-1 align-items-center">
                        <div class="col-7">
                            <input type="text" name="nama_jenis_pakaian_baru" id="nama_jenis_pakaian_baru" class="form-control form-control-sm rounded-3 @error('nama_jenis_pakaian_baru') is-invalid @enderror" value="{{ old('nama_jenis_pakaian_baru') }}" placeholder="✍️ Nama (cth: Cardigan)">
                        </div>
                        <div class="col-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text px-1 bg-white text-muted" style="font-size:10px;">KODE</span>
                                <input type="text" name="kode_jenis_pakaian_baru" id="kode_jenis_pakaian_baru" class="form-control form-control-sm rounded-end-3 text-uppercase fw-bold" value="{{ old('kode_jenis_pakaian_baru') }}" placeholder="CAR" maxlength="10">
                            </div>
                        </div>
                    </div>
                    @error('nama_jenis_pakaian_baru') 
                        <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> 
                    @enderror
                </div>
                @error('id_jenis_pakaian') 
                    <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> 
                @enderror
            </div>

            <!-- 2. Dropdown Warna -->
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark small">Warna <span class="text-danger">*</span></label>
                <select name="id_warna" id="id_warna" class="form-select select-searchable rounded-3 @error('id_warna') is-invalid @enderror" required>
                    <option value="">-- Pilih Warna --</option>
                    <option value="__NEW__" style="font-weight:bold; color:#d63384;">➕ + Tambah Warna Baru...</option>
                    @foreach($warna as $w)
                        <option value="{{ $w->id }}" data-nama="{{ $w->nama }}" {{ old('id_warna') == $w->id ? 'selected' : '' }}>
                            {{ $w->nama }} ({{ $w->kode }})
                        </option>
                    @endforeach
                </select>
                <div id="container_warna_baru" style="display: {{ old('id_warna') == '__NEW__' ? 'block' : 'none' }}; border-color: #ec95a8;" class="mt-2 p-2 rounded-3 bg-light border">
                    <div class="row g-1 align-items-center">
                        <div class="col-7">
                            <input type="text" name="nama_warna_baru" id="nama_warna_baru" class="form-control form-control-sm rounded-3 @error('nama_warna_baru') is-invalid @enderror" value="{{ old('nama_warna_baru') }}" placeholder="✍️ Nama (cth: Lavender)">
                        </div>
                        <div class="col-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text px-1 bg-white text-muted" style="font-size:10px;">KODE</span>
                                <input type="text" name="kode_warna_baru" id="kode_warna_baru" class="form-control form-control-sm rounded-end-3 text-uppercase fw-bold" value="{{ old('kode_warna_baru') }}" placeholder="LVD" maxlength="10">
                            </div>
                        </div>
                    </div>
                    @error('nama_warna_baru') 
                        <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> 
                    @enderror
                </div>
                @error('id_warna') 
                    <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> 
                @enderror
            </div>

            <!-- 3. Dropdown Model / Motif -->
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark small">Model / Motif <span class="text-danger">*</span></label>
                <select name="id_model" id="id_model" class="form-select select-searchable rounded-3 @error('id_model') is-invalid @enderror" required>
                    <option value="">-- Pilih Model --</option>
                    <option value="__NEW__" style="font-weight:bold; color:#d63384;">➕ + Tambah Model Baru...</option>
                    @foreach($model as $m)
                        <option value="{{ $m->id }}" data-nama="{{ $m->nama }}" {{ old('id_model') == $m->id ? 'selected' : '' }}>
                            {{ $m->nama }} ({{ $m->kode }})
                        </option>
                    @endforeach
                </select>
                <div id="container_model_baru" style="display: {{ old('id_model') == '__NEW__' ? 'block' : 'none' }}; border-color: #ec95a8;" class="mt-2 p-2 rounded-3 bg-light border">
                    <div class="row g-1 align-items-center">
                        <div class="col-7">
                            <input type="text" name="nama_model_baru" id="nama_model_baru" class="form-control form-control-sm rounded-3 @error('nama_model_baru') is-invalid @enderror" value="{{ old('nama_model_baru') }}" placeholder="✍️ Nama (cth: Oversize)">
                        </div>
                        <div class="col-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text px-1 bg-white text-muted" style="font-size:10px;">KODE</span>
                                <input type="text" name="kode_model_baru" id="kode_model_baru" class="form-control form-control-sm rounded-end-3 text-uppercase fw-bold" value="{{ old('kode_model_baru') }}" placeholder="OVS" maxlength="10">
                            </div>
                        </div>
                    </div>
                    @error('nama_model_baru') 
                        <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> 
                    @enderror
                </div>
                @error('id_model') 
                    <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> 
                @enderror
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <label class="form-label fw-bold text-dark">Nama Produk <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="text" name="nama_produk" id="nama_produk" class="form-control font-semibold" value="{{ old('nama_produk') }}" placeholder="Otomatis terisi saat memilih jenis, model, & warna" required>
                    <button type="button" id="btn_generate_nama" class="btn btn-outline-secondary btn-sm px-3" style="font-size:12px;" title="Reset ke nama otomatis">
                        ⚡ Generate Nama
                    </button>
                </div>
                <small class="text-muted d-block mt-1" style="font-size: 11px;">
                    💡 Terisi otomatis dari gabungan <strong>[Jenis] [Model] [Warna]</strong>. Bisa di-edit manual.
                </small>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark">Pemasok <span class="text-danger">*</span></label>
                <select name="id_pemasok" class="form-select select-searchable" required>
                    <option value="">— Pilih Pemasok —</option>
                    @foreach($pemasok as $p)
                        <option value="{{ $p->id }}" {{ old('id_pemasok') == $p->id ? 'selected' : '' }}>{{ $p->nama_pemasok }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold text-dark">Deskripsi Produk</label>
            <textarea name="deskripsi" class="form-control font-semibold" rows="2" placeholder="Catatan detail bahan, ukuran, atau spesifikasi barang...">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="mb-4">
            <label for="fotoInput" class="form-label font-semibold text-dark">Foto Produk</label>
            <div class="d-flex align-items-center gap-3">
                <div id="previewBox" style="width: 80px; height: 80px; border-radius: 14px; background: var(--pink-soft-2); border: 2px dashed var(--border-soft); overflow: hidden;" class="d-flex align-items-center justify-content-center flex-shrink-0">
                    <img id="imgPreview" src="" alt="Preview Foto" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                    <div id="placeholderPreview" class="text-center text-muted">
                        <i class="bi bi-image fs-3 d-block" style="color: var(--pink-primary);"></i>
                        <small style="font-size: 9px; font-weight: 700;">Preview</small>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <input type="file" name="foto" id="fotoInput" class="form-control" accept="image/*" onchange="previewFoto(this)">
                    <small class="text-muted d-block mt-1" style="font-size: 11px;">Upload foto produk (JPG, PNG, WEBP. Maksimal 2MB).</small>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- BAGIAN 2: DATA PEMBELIAN, HPP, DAN HARGA JUAL -->
        <!-- ============================================================ -->
        <p class="text-secondary small fw-bold text-uppercase mb-3 mt-4" style="letter-spacing:.4px; font-family: 'Quicksand', sans-serif;">
            💰 2. Data Pembelian &amp; HPP
        </p>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark">Jumlah Produk <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="jumlah" id="jumlah" class="form-control font-semibold" min="1" value="{{ old('jumlah', 1) }}" required>
                    <span class="input-group-text bg-light text-muted fw-bold">pcs</span>
                </div>
                <small class="text-muted" style="font-size: 11px;">Berapa produk / stok awal yang dibeli.</small>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold text-dark">Harga Beli / Produk <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text" style="border-radius:16px 0 0 16px;background:var(--pink-soft);border:2px solid var(--border-soft);border-right:none">Rp</span>
                    <input type="text" name="harga_beli_per_unit" id="harga_beli" class="form-control input-rupiah font-semibold" data-type="rupiah" value="{{ old('harga_beli_per_unit') }}" placeholder="0" required>
                </div>
                <small class="text-muted" style="font-size: 11px;">Harga modal per pcs dari pemasok.</small>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold text-dark">HPP Otomatis</label>
                <div class="input-group">
                    <span class="input-group-text" style="border-radius:16px 0 0 16px;background:var(--sage-soft);border:2px solid var(--border-soft);border-right:none">Rp</span>
                    <input type="text" id="hpp_otomatis_display" class="form-control font-semibold" readonly placeholder="0" style="background:var(--sage-soft)">
                </div>
                <small class="text-muted" style="font-size: 11px;">Otomatis sama dengan Harga Beli.</small>
            </div>
        </div>

        <!-- HPP REALISASI -->
        <div class="mb-4 p-3 rounded-4" style="background: #FFFBF0; border: 1.5px solid #F6E0B5;">
            <label class="form-label fw-bold text-dark mb-1">
                ✨ HPP Realisasi
                <span class="text-secondary fw-normal" style="font-size:11px">(opsional — diisi jika ada biaya ekstra/operasional)</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-white" style="border-radius:16px 0 0 16px;border:2px solid var(--border-soft);border-right:none">Rp</span>
                <input type="text" name="hpp_realisasi" id="hpp_realisasi" class="form-control input-rupiah font-semibold bg-white" data-type="rupiah" value="{{ old('hpp_realisasi') }}" placeholder="Isi jika HPP aktual berbeda dari harga beli (misal +ongkir/rebranding)">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">
                💡 <i>Jika HPP Realisasi diisi, nilai ini yang dipakai sebagai <strong>HPP Aktif</strong> dan akan otomatis mempengaruhi penentuan <strong>Harga Jual</strong>.</i>
            </small>
        </div>

        <!-- ============================================================ -->
        <!-- BAGIAN 3: PENENTUAN HARGA JUAL (BERADA DI BAWAH HPP) -->
        <!-- ============================================================ -->
        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #FDF6F8; border: 1.5px solid #F7D4DD !important;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Quicksand', sans-serif;">
                        🏷️ Penentuan Harga Jual
                    </h6>

                    <!-- Opsi Mode Margin: Persenan (%) vs Langsung Nominal (Rp) -->
                    <div class="btn-group btn-group-sm" role="group" id="groupMarginMode">
                        <input type="radio" class="btn-check" name="margin_mode" id="modePersen" value="persen" checked autocomplete="off">
                        <label class="btn btn-outline-pink font-semibold px-3" for="modePersen">
                            📊 Persenan Margin (%)
                        </label>

                        <input type="radio" class="btn-check" name="margin_mode" id="modeNominal" value="nominal" autocomplete="off">
                        <label class="btn btn-outline-pink font-semibold px-3" for="modeNominal">
                            💵 Langsung Nominal (Rp)
                        </label>
                    </div>
                </div>

                <!-- MODE 1: PERSENAN MARGIN (%) -->
                <div id="boxModePersen" class="mb-3">
                    <label class="form-label fw-bold text-dark small mb-1">Pilih / Masukkan Margin Profit (%)</label>
                    
                    <!-- Quick Preset Buttons -->
                    <div class="d-flex flex-wrap gap-1.5 mb-2.5">
                        <button type="button" class="btn btn-sm btn-preset-margin rounded-pill px-3 py-1 font-semibold" data-percent="10">+10%</button>
                        <button type="button" class="btn btn-sm btn-preset-margin rounded-pill px-3 py-1 font-semibold" data-percent="20">+20%</button>
                        <button type="button" class="btn btn-sm btn-preset-margin rounded-pill px-3 py-1 font-semibold active" data-percent="30">+30%</button>
                        <button type="button" class="btn btn-sm btn-preset-margin rounded-pill px-3 py-1 font-semibold" data-percent="50">+50%</button>
                        <button type="button" class="btn btn-sm btn-preset-margin rounded-pill px-3 py-1 font-semibold" data-percent="100">+100%</button>
                    </div>

                    <div class="input-group" style="max-width: 250px;">
                        <span class="input-group-text bg-white">Margin</span>
                        <input type="number" id="input_margin_persen" class="form-control font-semibold" min="0" max="1000" step="0.5" value="30">
                        <span class="input-group-text bg-light fw-bold">%</span>
                    </div>
                </div>

                <!-- INPUT UTAMA HARGA JUAL -->
                <div class="mb-2">
                    <label class="form-label fw-bold text-dark">Harga Jual Akhir <span class="text-danger">*</span></label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text fw-bold text-dark" style="border-radius:16px 0 0 16px;background:var(--pink-soft);border:2px solid var(--border-soft);border-right:none">Rp</span>
                        <input type="text" name="harga_jual" id="harga_jual" class="form-control input-rupiah font-bold text-dark" data-type="rupiah" value="{{ old('harga_jual') }}" style="font-size: 20px;" required>
                    </div>
                </div>

                <!-- LIVE BADGE INDIKATOR MARGIN & PROFIT -->
                <div id="liveProfitBadge" class="p-2.5 rounded-3 mt-3 d-flex justify-content-between align-items-center" style="background: #E8F7EE; border: 1px solid #C3EEDB;">
                    <div style="font-size: 12.5px;" class="font-semibold text-dark">
                        <i class="bi bi-graph-up-arrow me-1" style="color: #276A43;"></i> HPP Aktif Acuan: <strong id="lblHppAktif">Rp 0</strong>
                    </div>
                    <div style="font-size: 13px;" class="fw-bold text-success" id="lblEstimasiProfit">
                        Estimasi Profit: +0% (+Rp 0)
                    </div>
                </div>

            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
            <a href="{{ route('produk.index') }}" class="btn btn-yb-outline rounded-pill px-4 font-semibold">Batal</a>
            <button type="submit" class="btn btn-yb rounded-pill px-5 fw-bold text-white shadow-sm" id="btnSubmit">Simpan Produk 🌸</button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<style>
.btn-outline-pink {
    color: var(--pink-primary-dark);
    border-color: var(--pink-primary);
    background-color: transparent;
}
.btn-check:checked + .btn-outline-pink {
    background-color: var(--pink-primary);
    border-color: var(--pink-primary);
    color: #fff;
    font-weight: 700;
}
.btn-preset-margin {
    border: 1px solid #F0C4CE;
    background-color: #ffffff;
    color: var(--ink);
    font-size: 12px;
}
.btn-preset-margin:hover, .btn-preset-margin.active {
    background-color: var(--pink-primary);
    border-color: var(--pink-primary);
    color: #ffffff;
    font-weight: 700;
}
</style>

<script>
    function previewFoto(input) {
        const previewImg = document.getElementById('imgPreview');
        const placeholder = document.getElementById('placeholderPreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // ============================================================
    // LOGIKA PENENTUAN HARGA JUAL, MARGIN %, DAN HPP REALISASI
    // ============================================================
    function parseRupiahVal(valStr) {
        if (!valStr) return 0;
        return parseFloat(valStr.toString().replace(/\D/g, '')) || 0;
    }

    function formatRupiahVal(num) {
        return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputHargaBeli = document.getElementById('harga_beli');
        const inputHppRealisasi = document.getElementById('hpp_realisasi');
        const hppOtomatisDisplay = document.getElementById('hpp_otomatis_display');
        const inputHargaJual = document.getElementById('harga_jual');
        const inputMarginPersen = document.getElementById('input_margin_persen');
        const boxModePersen = document.getElementById('boxModePersen');

        const lblHppAktif = document.getElementById('lblHppAktif');
        const lblEstimasiProfit = document.getElementById('lblEstimasiProfit');

        let currentMode = 'persen'; // 'persen' or 'nominal'

        function getHppAktif() {
            const hargaBeli = parseRupiahVal(inputHargaBeli ? inputHargaBeli.value : 0);
            const hppReal = parseRupiahVal(inputHppRealisasi ? inputHppRealisasi.value : 0);
            return (hppReal > 0) ? hppReal : hargaBeli;
        }

        function kalkulasiHargaJual() {
            const hargaBeli = parseRupiahVal(inputHargaBeli ? inputHargaBeli.value : 0);
            const hppReal = parseRupiahVal(inputHppRealisasi ? inputHppRealisasi.value : 0);
            
            // Set HPP Otomatis
            if (hppOtomatisDisplay) {
                hppOtomatisDisplay.value = formatRupiahVal(hargaBeli);
            }

            const hppAktif = getHppAktif();
            if (lblHppAktif) {
                lblHppAktif.textContent = 'Rp ' + formatRupiahVal(hppAktif);
            }

            if (currentMode === 'persen') {
                const marginPct = parseFloat(inputMarginPersen.value) || 0;
                const hargaJualHitung = hppAktif + (hppAktif * (marginPct / 100));
                
                if (inputHargaJual) {
                    inputHargaJual.value = formatRupiahVal(hargaJualHitung);
                }
                
                const profitNominal = hargaJualHitung - hppAktif;
                if (lblEstimasiProfit) {
                    lblEstimasiProfit.innerHTML = `Estimasi Profit: <strong>+${marginPct}%</strong> (+Rp ${formatRupiahVal(profitNominal)})`;
                }
            } else {
                // Mode nominal langsung
                const hargaJualVal = parseRupiahVal(inputHargaJual ? inputHargaJual.value : 0);
                const profitNominal = hargaJualVal - hppAktif;
                const marginPct = (hppAktif > 0) ? Math.round(((profitNominal / hppAktif) * 100) * 10) / 10 : 0;

                if (lblEstimasiProfit) {
                    if (profitNominal >= 0) {
                        lblEstimasiProfit.className = 'fw-bold text-success';
                        lblEstimasiProfit.innerHTML = `Estimasi Profit: <strong>+${marginPct}%</strong> (+Rp ${formatRupiahVal(profitNominal)})`;
                    } else {
                        lblEstimasiProfit.className = 'fw-bold text-danger';
                        lblEstimasiProfit.innerHTML = `Estimasi Rugi: <strong>${marginPct}%</strong> (-Rp ${formatRupiahVal(Math.abs(profitNominal))})`;
                    }
                }
            }
        }

        // Toggle mode margin (Persenan vs Nominal)
        const radiosMarginMode = document.querySelectorAll('input[name="margin_mode"]');
        radiosMarginMode.forEach(radio => {
            radio.addEventListener('change', function() {
                currentMode = this.value;
                if (currentMode === 'persen') {
                    boxModePersen.style.display = 'block';
                } else {
                    boxModePersen.style.display = 'none';
                }
                kalkulasiHargaJual();
            });
        });

        // Quick Preset Margin Buttons
        const presetButtons = document.querySelectorAll('.btn-preset-margin');
        presetButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                presetButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const pct = parseFloat(this.getAttribute('data-percent')) || 0;
                inputMarginPersen.value = pct;
                kalkulasiHargaJual();
            });
        });

        // Event listeners untuk kalkulasi otomatis
        if (inputHargaBeli) inputHargaBeli.addEventListener('input', kalkulasiHargaJual);
        if (inputHppRealisasi) inputHppRealisasi.addEventListener('input', kalkulasiHargaJual);
        if (inputMarginPersen) inputMarginPersen.addEventListener('input', kalkulasiHargaJual);
        if (inputHargaJual) {
            inputHargaJual.addEventListener('input', function() {
                if (currentMode === 'nominal') {
                    kalkulasiHargaJual();
                }
            });
        }

        // Jalankan sekali saat pertama dimuat
        kalkulasiHargaJual();
    });

    // ============================================================
    // AUTO GENERATE NAMA PRODUK & KODE MASTER DATA BARU
    // ============================================================
    function generate3LetterCode(text) {
        if (!text) return '';
        const clean = text.replace(/[^A-Za-z0-9\s]/g, '').trim().toUpperCase();
        if (!clean) return '';
        const words = clean.split(/\s+/).filter(Boolean);
        let code = '';
        if (words.length >= 3) {
            code = words[0][0] + words[1][0] + words[2][0];
        } else if (words.length === 2) {
            if (words[0].length >= 2) {
                code = words[0].substring(0, 2) + words[1][0];
            } else {
                code = words[0][0] + words[1].substring(0, 2);
            }
        } else {
            code = clean.substring(0, 3);
        }
        return code.padEnd(3, 'X').substring(0, 3);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputNama = document.getElementById('nama_produk');
        const selectJenis = document.getElementById('id_jenis_pakaian');
        const containerJenisBaru = document.getElementById('container_jenis_baru');
        const inputJenisBaru = document.getElementById('nama_jenis_pakaian_baru');
        const inputKodeJenisBaru = document.getElementById('kode_jenis_pakaian_baru');

        const selectWarna = document.getElementById('id_warna');
        const containerWarnaBaru = document.getElementById('container_warna_baru');
        const inputWarnaBaru = document.getElementById('nama_warna_baru');
        const inputKodeWarnaBaru = document.getElementById('kode_warna_baru');

        const selectModel = document.getElementById('id_model');
        const containerModelBaru = document.getElementById('container_model_baru');
        const inputModelBaru = document.getElementById('nama_model_baru');
        const inputKodeModelBaru = document.getElementById('kode_model_baru');

        const btnGenerate = document.getElementById('btn_generate_nama');

        let isManuallyEdited = false;

        if (inputNama) {
            inputNama.addEventListener('input', function() {
                isManuallyEdited = true;
            });
        }

        function toggleNewContainer(selectElem, containerElem, inputNameElem) {
            if (!selectElem || !containerElem) return;
            if (selectElem.value === '__NEW__') {
                containerElem.style.display = 'block';
                if (inputNameElem) inputNameElem.focus();
            } else {
                containerElem.style.display = 'none';
            }
        }

        function getSelectedAttributeText(selectElement, inputNewElement) {
            if (!selectElement) return '';
            if (selectElement.value === '__NEW__') {
                return inputNewElement ? inputNewElement.value.trim() : '';
            }
            if (selectElement.selectedIndex <= 0) return '';
            const selectedOpt = selectElement.options[selectElement.selectedIndex];
            return selectedOpt.getAttribute('data-nama') || selectedOpt.text.replace(/\s*\([^)]*\)/g, '').trim();
        }

        function generateNamaProduk(force = false) {
            if (!inputNama) return;
            if (isManuallyEdited && !force) return;

            const jenis = getSelectedAttributeText(selectJenis, inputJenisBaru);
            const model = getSelectedAttributeText(selectModel, inputModelBaru);
            const warna = getSelectedAttributeText(selectWarna, inputWarnaBaru);

            const parts = [jenis, model, warna].filter(Boolean);
            if (parts.length > 0) {
                inputNama.value = parts.join(' ');
            }
        }

        const items = [
            { select: selectJenis, container: containerJenisBaru, inputName: inputJenisBaru, inputKode: inputKodeJenisBaru, userEditedKode: false },
            { select: selectWarna, container: containerWarnaBaru, inputName: inputWarnaBaru, inputKode: inputKodeWarnaBaru, userEditedKode: false },
            { select: selectModel, container: containerModelBaru, inputName: inputModelBaru, inputKode: inputKodeModelBaru, userEditedKode: false },
        ];

        items.forEach(item => {
            if (item.select) {
                item.select.addEventListener('change', function() {
                    toggleNewContainer(item.select, item.container, item.inputName);
                    generateNamaProduk(false);
                });
            }
            if (item.inputKode) {
                item.inputKode.addEventListener('input', function() {
                    item.userEditedKode = true;
                    this.value = this.value.toUpperCase();
                });
            }
            if (item.inputName) {
                item.inputName.addEventListener('input', function() {
                    if (item.inputKode && !item.userEditedKode) {
                        item.inputKode.value = generate3LetterCode(this.value);
                    }
                    generateNamaProduk(false);
                });
            }
        });

        if (btnGenerate) {
            btnGenerate.addEventListener('click', function() {
                isManuallyEdited = false;
                generateNamaProduk(true);
            });
        }
    });
</script>
@endpush
