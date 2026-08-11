@extends('layouts.app')

@section('title', 'Edit Produk — Yourbaestyle')

@section('content')
<div class="container-fluid py-4" style="max-width: 900px; margin: 0 auto;">
    
    <!-- Tombol Kembali -->
    <div class="mb-3">
        <a href="{{ route('produk.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- Kartu Form Edit -->
    <div class="card border-0 shadow-sm rounded-4" style="background: #fff; overflow: hidden;">
        <div class="card-header bg-white py-3 px-4 border-bottom" style="border-color: #F7E5EA !important;">
            <h5 class="mb-0 fw-bold" style="color: #4D3D43; font-family: 'Quicksand', sans-serif;">
                <i class="bi bi-pencil-square me-2" style="color: #EC95A8;"></i>Edit Data Produk
            </h5>
        </div>

        <div class="card-body p-4">


            <!-- FORM EDIT -->
            <form action="{{ route('produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- ==================== BAGIAN 1: FOTO PRODUK ==================== -->
                <div class="mb-4 p-3 rounded-4" style="background: #FFF5F7; border: 1px dashed #EC95A8;">
                    <label class="form-label fw-bold" style="color: #4D3D43;">Foto Produk</label>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        
                        <!-- Preview Foto (Saat Ini / Hasil Upload Baru) -->
                        <div class="text-center">
                            <div id="previewBoxEdit" style="width: 100px; height: 100px; border-radius: 12px; background: #fff; border: 2px dashed #F7E5EA; overflow: hidden;" class="d-flex align-items-center justify-content-center">
                                @if($produk->foto && file_exists(storage_path('app/public/' . $produk->foto)))
                                    <img id="imgPreviewEdit" src="{{ asset('storage/' . $produk->foto) }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <img id="imgPreviewEdit" src="" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                    <div id="placeholderPreviewEdit" class="text-center text-muted">
                                        <i class="bi bi-camera fs-1 text-muted d-block"></i>
                                        <small style="font-size: 10px;">Belum ada foto</small>
                                    </div>
                                @endif
                            </div>
                            @if($produk->foto && file_exists(storage_path('app/public/' . $produk->foto)))
                                <div class="form-check form-check-inline mt-1 d-block">
                                    <input class="form-check-input" type="checkbox" name="hapus_foto" value="1" id="hapusFoto">
                                    <label class="form-check-label text-danger small fw-bold" for="hapusFoto">Hapus Foto Lama</label>
                                </div>
                            @endif
                        </div>

                        <!-- Input Upload Baru -->
                        <div class="grow" style="flex: 1;">
                            <input type="file" name="foto" id="fotoInputEdit" class="form-control form-control-sm" accept="image/*" onchange="previewFotoEdit(this)">
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">
                                <i class="bi bi-info-circle me-1"></i>Format: JPG, JPEG, PNG, WEBP (Maks. 2MB). Pilih foto baru untuk melihat pratinjau (*preview*).
                            </small>
                        </div>
                    </div>
                </div>

                <!-- ==================== BAGIAN 2: INFORMASI UTAMA ==================== -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Kode Produk</label>
                        <input type="text" class="form-control bg-light" value="{{ $produk->kode_produk }}" readonly>
                        <small class="text-muted" style="font-size: 11px;">Kode produk dikunci oleh sistem.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Status Produk <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="aktif" {{ old('status', $produk->status) == 'aktif' ? 'selected' : '' }}>Aktif / Dijual</option>
                            <option value="nonaktif" {{ old('status', $produk->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif / Arsip</option>
                        </select>
                    </div>

                    <!-- DROPDOWN MASTER DATA UNTUK HALAMAN EDIT -->
                    <div class="col-12">
                        <div class="row g-3">
                            <!-- 1. Dropdown Jenis Pakaian -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark small">Jenis Pakaian <span class="text-danger">*</span></label>
                                <select name="id_jenis_pakaian" id="id_jenis_pakaian" class="form-select rounded-3 @error('id_jenis_pakaian') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="__NEW__" style="font-weight:bold; color:#d63384;">➕ + Tambah Jenis Baru...</option>
                                    @foreach($jenisPakaian as $jp)
                                        <option value="{{ $jp->id }}" data-nama="{{ $jp->nama }}" {{ (old('id_jenis_pakaian', $produk->id_jenis_pakaian) == $jp->id) ? 'selected' : '' }}>
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
                                <select name="id_warna" id="id_warna" class="form-select rounded-3 @error('id_warna') is-invalid @enderror" required>
                                    <option value="">-- Pilih Warna --</option>
                                    <option value="__NEW__" style="font-weight:bold; color:#d63384;">➕ + Tambah Warna Baru...</option>
                                    @foreach($warna as $w)
                                        <option value="{{ $w->id }}" data-nama="{{ $w->nama }}" {{ (old('id_warna', $produk->id_warna) == $w->id) ? 'selected' : '' }}>
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

                            <!-- 3. Dropdown Model -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark small">Model / Motif <span class="text-danger">*</span></label>
                                <select name="id_model" id="id_model" class="form-select rounded-3 @error('id_model') is-invalid @enderror" required>
                                    <option value="">-- Pilih Model --</option>
                                    <option value="__NEW__" style="font-weight:bold; color:#d63384;">➕ + Tambah Model Baru...</option>
                                    @foreach($model as $m)
                                        <option value="{{ $m->id }}" data-nama="{{ $m->nama }}" {{ (old('id_model', $produk->id_model) == $m->id) ? 'selected' : '' }}>
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
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Nama Produk <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="nama_produk" id="nama_produk" class="form-control" value="{{ old('nama_produk', $produk->nama_produk) }}" required placeholder="Contoh: Knitwear Garis Kuning">
                            <button type="button" id="btn_generate_nama" class="btn btn-outline-secondary btn-sm px-3" style="font-size:12px;" title="Reset ke nama otomatis">
                                ⚡ Generate Nama
                            </button>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">
                            💡 <i>Format standar otomatis: <strong>[Jenis] [Model] [Warna]</strong>. Anda/Pegawai tetap dapat mengedit nama ini secara manual.</i>
                        </small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Pemasok <span class="text-danger">*</span></label>
                        <select name="id_pemasok" class="form-select" required>
                            <option value="">Pilih Pemasok</option>
                            @foreach($pemasok as $p)
                                <option value="{{ $p->id }}" {{ old('id_pemasok', $produk->id_pemasok) == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_pemasok }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- ==================== BAGIAN 3: HARGA & HPP ==================== -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Harga Jual <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">Rp</span>
                            <input type="text" name="harga_jual" class="form-control input-rupiah" data-type="rupiah" value="{{ old('harga_jual', number_format($produk->harga_jual ?? 0, 0, '', '')) }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Harga Beli Per Unit</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">Rp</span>
                            <input type="text" name="harga_beli_per_unit" class="form-control input-rupiah" data-type="rupiah" value="{{ old('harga_beli_per_unit', number_format($produk->harga_beli_per_unit ?? 0, 0, '', '')) }}">
                        </div>
                        <small class="text-muted d-block mt-1">Digunakan jika ada penambahan stok.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">HPP Otomatis</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">Rp</span>
                            <input type="text" class="form-control bg-light input-rupiah" value="{{ number_format($produk->hpp_otomatis ?? 0, 0, '', '') }}" readonly>
                        </div>
                        <small class="text-muted" style="font-size: 11px;">Dihitung otomatis dari stok + harga beli.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">HPP Realisasi (Optional)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">Rp</span>
                            <input type="text" name="hpp_realisasi" class="form-control input-rupiah" data-type="rupiah" value="{{ old('hpp_realisasi', $produk->hpp_realisasi ? number_format($produk->hpp_realisasi, 0, '', '') : '') }}">
                        </div>
                    </div>
                </div>

                <!-- ==================== BAGIAN 4: PENGELOLAAN & PENYESUAIAN STOK ==================== -->
                <div class="card mb-4 border-0 shadow-sm rounded-4" style="background: #FFF5F7; border: 1.5px solid #F7E5EA !important;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3" style="color: #4D3D43; font-family: 'Quicksand', sans-serif;">
                            <i class="bi bi-box-seam me-2" style="color: #EC95A8;"></i>Stok Barang & Penyesuaian
                        </h6>

                        <div class="row g-3 align-items-center">
                            <!-- Informasi Stok Saat Ini -->
                            <div class="col-md-4">
                                <label class="form-label font-semibold text-muted mb-1" style="font-size: 11.5px;">STOK SAAT INI</label>
                                <div style="font-size: 32px; font-weight: 800; color: #4D3D43;">
                                    {{ number_format($produk->stok) }} <span class="fs-6 font-semibold text-muted">pcs</span>
                                </div>
                            </div>

                            <!-- Opsi Penyesuaian Stok -->
                            <div class="col-md-8">
                                <label class="form-label fw-bold text-dark mb-1">Aksi Penyesuaian Stok</label>
                                <div class="row g-2">
                                    <div class="col-12 col-sm-6">
                                        <select name="jenis_koreksi_stok" id="jenisKoreksiStok" class="form-select font-semibold" onchange="toggleKoreksiStok(this.value)">
                                            <option value="tetap">-- Tidak Ada Perubahan Stok --</option>
                                            <option value="tambah">🟢 Tambah Stok (+)</option>
                                            <option value="kurang">🔴 Kurangi Stok (-)</option>
                                            <option value="set_total">⚙️ Set Total Stok Baru (=)</option>
                                        </select>
                                    </div>

                                    <!-- Input Qty Koreksi -->
                                    <div class="col-12 col-sm-6" id="boxQtyKoreksi" style="display: none;">
                                        <div class="input-group">
                                            <input type="number" name="qty_koreksi" id="qtyKoreksi" class="form-control" min="1" placeholder="Jumlah unit">
                                            <span class="input-group-text bg-light text-muted fw-bold">pcs</span>
                                        </div>
                                    </div>

                                    <!-- Input Set Total Baru -->
                                    <div class="col-12 col-sm-6" id="boxSetTotal" style="display: none;">
                                        <div class="input-group">
                                            <input type="number" name="stok_total_baru" id="stokTotalBaru" class="form-control" min="0" placeholder="Stok total baru" value="{{ $produk->stok }}">
                                            <span class="input-group-text bg-light text-muted fw-bold">pcs</span>
                                        </div>
                                    </div>
                                </div>

                                <small class="text-muted d-block mt-2" style="font-size: 11.5px;">
                                    <i class="bi bi-info-circle me-1"></i>Pilih <strong>"Tambah Stok"</strong> jika ada barang masuk, <strong>"Kurangi Stok"</strong> jika ada barang rusak/hilang, atau <strong>"Set Total Stok Baru"</strong> untuk mengubah angka stok secara langsung.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== BAGIAN 5: DESKRIPSI ==================== -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Deskripsi Produk</label>
                    <textarea name="deskripsi" class="form-control" rows="4" placeholder="Contoh: Kondisi baik, sedikit lecet di bagian atas...">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                </div>

                <!-- ==================== TOMBOL ACTION ==================== -->
                <div class="d-flex gap-2 justify-content-between pt-3">
                    <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: var(--pink-primary); border: none;">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewFotoEdit(input) {
    const previewImg = document.getElementById('imgPreviewEdit');
    const placeholder = document.getElementById('placeholderPreviewEdit');
    
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

function toggleKoreksiStok(val) {
    const boxQty = document.getElementById('boxQtyKoreksi');
    const boxSet = document.getElementById('boxSetTotal');
    if (!boxQty || !boxSet) return;

    if (val === 'tambah' || val === 'kurang') {
        boxQty.style.display = 'block';
        boxSet.style.display = 'none';
    } else if (val === 'set_total') {
        boxQty.style.display = 'none';
        boxSet.style.display = 'block';
    } else {
        boxQty.style.display = 'none';
        boxSet.style.display = 'none';
    }
}

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
@endsection