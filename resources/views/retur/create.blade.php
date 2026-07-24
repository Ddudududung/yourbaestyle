@extends('layouts.app')

@section('title', 'Buat Retur')

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
            
            <!-- ERROR MESSAGES -->
            @if ($errors->any())
                <div class="alert alert-danger rounded-3 mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- INFO TRANSAKSI YANG DIPILIH -->
            @if($transaksi)
            <div class="alert rounded-4 mb-4 p-3" style="background: #E8F4F8; border: none;">
                <div class="row g-2">
                    <div class="col-md-3">
                        <small class="text-muted">No. Transaksi</small>
                        <div class="fw-bold text-dark">{{ $transaksi->no_pesanan ?? $transaksi->kode_transaksi }}</div>
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
                        <div class="fw-bold text-dark">{{ $transaksi->tanggal->format('d-m-Y') }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- FORM RETUR -->
            <form action="{{ route('retur.store') }}" method="POST">
                @csrf

                <!-- Hidden: Transaksi Info -->
                <input type="hidden" name="transaksi_type" value="{{ $transaksiType }}">
                <input type="hidden" name="transaksi_id" value="{{ $transaksi->id }}">

                <!-- BAGIAN 1: PILIH PRODUK -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Pilih Produk dari Transaksi <span class="text-danger">*</span></label>
                    
                    @if($detail && $detail->count() > 0)
                        <select name="id_produk" class="form-select" id="produkSelect" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($detail as $d)
                            <option value="{{ $d->id_produk }}" data-name="{{ $d->produk->nama_produk }}">
                                {{ $d->produk->nama_produk }} 
                                @if($d->variasi) ({{ $d->variasi }}) @endif
                                - Qty: {{ $d->qty }}
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih produk yang akan di-retur dari transaksi ini.</small>
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
                        <input type="number" name="qty" class="form-control" min="1" value="1" required>
                        <span class="input-group-text bg-light">pcs</span>
                    </div>
                </div>

                <!-- BAGIAN 3: ALASAN RETUR -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Alasan Retur <span class="text-danger">*</span></label>
                    <select name="alasan" class="form-select" required>
                        <option value="">-- Pilih Alasan --</option>
                        <option value="cacat_produksi">Cacat Produksi</option>
                        <option value="tidak_sesuai_pesanan">Tidak Sesuai Pesanan</option>
                        <option value="rusak_pengiriman">Rusak saat Pengiriman</option>
                        <option value="tidak_layak_jual">Tidak Layak Jual</option>
                        <option value="kesalahan_barang">Kesalahan Barang</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <!-- BAGIAN 4: KONDISI BARANG -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Kondisi Barang <span class="text-danger">*</span></label>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check p-3 rounded-3" style="background: #E8F7EE; border: 2px solid #C3EEDB; cursor: pointer;">
                                <input class="form-check-input" type="radio" name="kondisi_barang" value="layak_jual" id="layakJual" required>
                                <label class="form-check-label fw-bold text-dark" for="layakJual" style="cursor: pointer;">
                                    <i class="bi bi-check-circle me-2" style="color: #52976D;"></i>Layak Jual
                                </label>
                                <small class="d-block text-muted ms-4">Barang akan dikembalikan ke stok</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check p-3 rounded-3" style="background: #FFE6E6; border: 2px solid #F7C9D3; cursor: pointer;">
                                <input class="form-check-input" type="radio" name="kondisi_barang" value="tidak_layak" id="tidakLayak" required>
                                <label class="form-check-label fw-bold text-dark" for="tidakLayak" style="cursor: pointer;">
                                    <i class="bi bi-x-circle me-2" style="color: #DC3545;"></i>Tidak Layak Jual
                                </label>
                                <small class="d-block text-muted ms-4">Akan dihitung sebagai kerugian</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN 5: TANGGAL RETUR -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Tanggal Retur <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>

                <!-- BAGIAN 6: ONGKIR RETUR (OPTIONAL) -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Ongkir Retur (Optional)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">Rp</span>
                        <input type="number" name="ongkir_retur" class="form-control" min="0" value="0">
                    </div>
                    <small class="text-muted">Biaya pengiriman barang retur (jika ada).</small>
                </div>

                <!-- TOMBOL ACTION -->
                <div class="d-flex gap-2 justify-content-between pt-3">
                    <a href="{{ route('retur.select') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="bi bi-check-lg me-1"></i> Simpan Retur
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
    .form-check {
        margin: 0;
    }
    .form-check-input {
        border-color: #E8D9E0;
    }
    .form-check-input:checked {
        background-color: #EC95A8;
        border-color: #EC95A8;
    }
</style>
@endsection