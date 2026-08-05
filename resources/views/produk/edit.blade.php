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
            
            <!-- PESAN ERROR VALIDASI -->
            @if ($errors->any())
                <div class="alert alert-danger rounded-3 mb-4 text-sm">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- FORM EDIT -->
            <form action="{{ route('produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- ==================== BAGIAN 1: FOTO PRODUK ==================== -->
                <div class="mb-4 p-3 rounded-4" style="background: #FFF5F7; border: 1px dashed #EC95A8;">
                    <label class="form-label fw-bold" style="color: #4D3D43;">Foto Produk</label>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        
                        <!-- Preview Foto Saat Ini -->
                        <div class="text-center">
                            @if($produk->foto && file_exists(storage_path('app/public/' . $produk->foto)))
                                <img src="{{ asset('storage/' . $produk->foto) }}" width="100" height="100" class="rounded-3 shadow-sm mb-1" style="object-fit: cover; border: 2px solid #fff;">
                                <div class="form-check form-check-inline mt-1 d-block">
                                    <input class="form-check-input" type="checkbox" name="hapus_foto" value="1" id="hapusFoto">
                                    <label class="form-check-label text-danger small fw-bold" for="hapusFoto">Hapus Foto Lama</label>
                                </div>
                            @else
                                <div style="width: 100px; height: 100px; background: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 1px solid #F7E5EA;">
                                    <i class="bi bi-camera fs-1 text-muted"></i>
                                </div>
                                <span class="d-block small text-muted mt-1">Belum ada foto</span>
                            @endif
                        </div>

                        <!-- Input Upload Baru -->
                        <div class="grow">
                            <input type="file" name="foto" class="form-control form-control-sm" accept="image/png, image/jpeg, image/jpg">
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">
                                <i class="bi bi-info-circle me-1"></i>Format: JPG, JPEG, PNG (Maks. 2MB). Biarkan kosong jika tidak ganti foto.
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

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" class="form-control" value="{{ old('nama_produk', $produk->nama_produk) }}" required placeholder="Contoh: Knitwear Kuning Garis">
                    </div>

                    <!-- DROPDOWN MASTER DATA UNTUK HALAMAN EDIT -->
<div class="row g-3 mb-3">
    <!-- 1. Dropdown Jenis Pakaian -->
    <div class="col-md-4">
        <label class="form-label fw-bold text-dark small">Jenis Pakaian <span class="text-danger">*</span></label>
        <select name="id_jenis_pakaian" class="form-select rounded-3 @error('id_jenis_pakaian') is-invalid @enderror" required>
            <option value="">-- Pilih Jenis --</option>
            @foreach($jenisPakaian as $jp)
                <option value="{{ $jp->id }}" {{ (old('id_jenis_pakaian', $produk->id_jenis_pakaian) == $jp->id) ? 'selected' : '' }}>
                    {{ $jp->nama }} ({{ $jp->kode }})
                </option>
            @endforeach
        </select>
        @error('id_jenis_pakaian') 
            <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> 
        @enderror
    </div>

    <!-- 2. Dropdown Warna -->
    <div class="col-md-4">
        <label class="form-label fw-bold text-dark small">Warna <span class="text-danger">*</span></label>
        <select name="id_warna" class="form-select rounded-3 @error('id_warna') is-invalid @enderror" required>
            <option value="">-- Pilih Warna --</option>
            @foreach($warna as $w)
                <option value="{{ $w->id }}" {{ (old('id_warna', $produk->id_warna) == $w->id) ? 'selected' : '' }}>
                    {{ $w->nama }} ({{ $w->kode }})
                </option>
            @endforeach
        </select>
        @error('id_warna') 
            <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> 
        @enderror
    </div>

    <!-- 3. Dropdown Model -->
    <div class="col-md-4">
        <label class="form-label fw-bold text-dark small">Model / Motif <span class="text-danger">*</span></label>
        <select name="id_model" class="form-select rounded-3 @error('id_model') is-invalid @enderror" required>
            <option value="">-- Pilih Model --</option>
            @foreach($model as $m)
                <option value="{{ $m->id }}" {{ (old('id_model', $produk->id_model) == $m->id) ? 'selected' : '' }}>
                    {{ $m->nama }} ({{ $m->kode }})
                </option>
            @endforeach
        </select>
        @error('id_model') 
            <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> 
        @enderror
    </div>
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
                            <input type="number" name="harga_jual" class="form-control" value="{{ old('harga_jual', $produk->harga_jual) }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Harga Beli Per Unit</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">Rp</span>
                            <input type="number" name="harga_beli_per_unit" class="form-control" value="{{ old('harga_beli_per_unit', $produk->harga_beli_per_unit) }}" step="0.01">
                        </div>
                        <small class="text-muted d-block mt-1">Digunakan jika ada penambahan stok.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">HPP Otomatis</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">Rp</span>
                            <input type="number" class="form-control bg-light" value="{{ $produk->hpp_otomatis }}" readonly>
                        </div>
                        <small class="text-muted" style="font-size: 11px;">Dihitung otomatis dari stok + harga beli.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">HPP Realisasi (Optional)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">Rp</span>
                            <input type="number" name="hpp_realisasi" class="form-control" value="{{ old('hpp_realisasi', $produk->hpp_realisasi) }}" step="0.01">
                        </div>
                    </div>
                </div>

                <!-- ==================== BAGIAN 4: STOK ==================== -->
                <div class="card mb-3 border-0" style="background: #E8F7EE;">
                    <div class="card-body p-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark mb-0">Stok Saat Ini</label>
                                <div style="font-size: 28px; font-weight: bold; color: #52976D;">
                                    {{ $produk->stok }} pcs
                                </div>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-bold text-dark">Penambahan Stok (Optional) <span class="text-success">+</span></label>
                                <div class="input-group">
                                    <input type="number" name="qty_tambah" class="form-control" min="0" placeholder="Masukkan jumlah tambahan" value="{{ old('qty_tambah', '') }}">
                                    <span class="input-group-text bg-success text-white fw-bold">pcs</span>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-info-circle me-1"></i>Kosongkan jika hanya update data (tanpa tambah stok). Jika ada qty, akan ditambahkan ke stok saat ini. HPP akan di-rata-rata otomatis.
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
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-label {
        font-size: 13px;
        color: #4D3D43;
    }
    .form-control, .form-select {
        border-color: #E8D9E0;
        border-radius: 8px;
        font-size: 13px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #EC95A8;
        box-shadow: 0 0 0 0.2rem rgba(236, 149, 168, 0.25);
    }
</style>
@endsection