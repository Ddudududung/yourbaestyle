@extends('layouts.app')

@section('title', 'Tambah Produk & HPP ')

@section('content')

<div class="card-yb p-4" style="max-width:740px">
    <h6 class="fw-bold mb-4" style="font-family:'Quicksand',sans-serif">Tambah Data Produk &amp; HPP</h6>

    <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <p class="text-secondary small fw-bold text-uppercase mb-3" style="letter-spacing:.4px">🧷 Data Produk</p>
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
            <div class="col-md-12">
                <label class="form-label fw-bold text-dark">Nama Produk <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="text" name="nama_produk" id="nama_produk" class="form-control" value="{{ old('nama_produk') }}" placeholder="Otomatis terisi saat memilih jenis, model, & warna" required>
                    <button type="button" id="btn_generate_nama" class="btn btn-outline-secondary btn-sm px-3" style="font-size:12px;" title="Reset ke nama otomatis">
                        ⚡ Generate Nama
                    </button>
                </div>
                <small class="text-muted d-block mt-1" style="font-size: 11px;">
                    💡 <i>Nama terisi otomatis dari gabungan <strong>[Jenis] [Model] [Warna]</strong>. Pegawai tetap bisa mengedit atau menambahkan detail manual.</i>
                </small>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Pemasok <span class="text-danger">*</span></label>
                <select name="id_pemasok" class="form-select select-searchable" required>
                    <option value="">— Pilih Pemasok —</option>
                    @foreach($pemasok as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_pemasok }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text" style="border-radius:16px 0 0 16px;background:var(--pink-soft);border:2px solid var(--border-soft);border-right:none">Rp</span>
                    <input type="text" name="harga_jual" id="harga_jual" class="form-control input-rupiah" data-type="rupiah" value="{{ old('harga_jual') }}" required>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="fotoInput" class="form-label font-semibold">Foto Produk</label>
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

        <p class="text-secondary small fw-bold text-uppercase mb-3 mt-4" style="letter-spacing:.4px">💰 Data Pembelian &amp; HPP</p>
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label">Jumlah Unit <span class="text-danger">*</span></label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="{{ old('jumlah') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Harga Beli / Unit <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text" style="border-radius:16px 0 0 16px;background:var(--pink-soft);border:2px solid var(--border-soft);border-right:none">Rp</span>
                    <input type="text" name="harga_beli_per_unit" id="harga_beli" class="form-control input-rupiah" data-type="rupiah" value="{{ old('harga_beli_per_unit') }}" required>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">HPP Otomatis</label>
                <div class="input-group">
                    <span class="input-group-text" style="border-radius:16px 0 0 16px;background:var(--sage-soft);border:2px solid var(--border-soft);border-right:none">Rp</span>
                    <input type="text" id="hpp_otomatis_display" class="form-control" readonly placeholder="0" style="background:var(--sage-soft)">
                </div>
            </div>
        </div>

        <div class="mb-4" style="background:var(--amber-soft);border-radius:18px;padding:16px 18px">
            <label class="form-label">
                ✨ HPP Realisasi
                <span class="text-secondary fw-normal" style="font-size:11px">(opsional — kosongkan jika belum diketahui)</span>
            </label>
            <div class="input-group">
                <span class="input-group-text" style="border-radius:16px 0 0 16px;background:#fff;border:2px solid var(--border-soft);border-right:none">Rp</span>
                <input type="number" name="hpp_realisasi" class="form-control" value="{{ old('hpp_realisasi') }}" placeholder="Isi jika biaya aktual sudah diketahui" style="background:#fff">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('produk.index') }}" class="btn btn-yb-outline">Batal</a>
            <button type="submit" class="btn btn-yb">Simpan Produk 🌸</button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
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

    function hitungHPP() {
        const hargaBeli = parseFloat((document.getElementById('harga_beli').value || '').toString().replace(/\D/g, '')) || 0;
        document.getElementById('hpp_otomatis_display').value = formatRupiahDisplay(hargaBeli);
    }
    document.getElementById('harga_beli')?.addEventListener('input', hitungHPP);
    document.getElementById('jumlah')?.addEventListener('input', hitungHPP);

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
