@extends('layouts.app')

@section('title', 'Rincian Retur Barang — Yourbaestyle')

@section('content')
<div class="container-fluid py-4" style="max-width: 900px; margin: 0 auto;">
    
    <!-- Tombol Kembali -->
    <div class="mb-3">
        <a href="{{ route('retur.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Retur
        </a>
    </div>

    <!-- Kartu Rincian Retur -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3.5 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color: #F7E5EA;">
            <div>
                <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-file-earmark-text-fill me-2" style="color: var(--pink-primary);"></i>Rincian Retur #{{ $retur->id }}
                </h5>
                <small class="text-muted font-semibold">Diproses pada {{ \Carbon\Carbon::parse($retur->tanggal)->format('d F Y') }}</small>
            </div>

            @if(!empty($retur->id_pesanan_online))
                <span class="badge px-3 py-2" style="background: #FFE6E6; color: #DC3545; font-size: 11px; font-weight: 700;">TRANSAKSI ONLINE</span>
            @else
                <span class="badge px-3 py-2" style="background: #E8F7EE; color: #52976D; font-size: 11px; font-weight: 700;">TRANSAKSI POS (OFFLINE)</span>
            @endif
        </div>

        <div class="card-body p-4">

            <!-- BAGIAN 1: INFORMASI TRANSAKSI ASAL -->
            <div class="p-3.5 rounded-4 mb-4" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                <h6 class="fw-bold mb-3 text-dark" style="font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-receipt me-2" style="color: var(--pink-primary);"></i>Transaksi Asal
                </h6>
                
                @php
                    $tx = $retur->pesananOnline ?? $retur->transaksiPos;
                    $kodeTx = $retur->pesananOnline->no_pesanan ?? $retur->transaksiPos->kode_transaksi ?? '-';
                    $pembeli = $retur->pesananOnline->nama_pembeli ?? ($retur->transaksiPos->user->name ?? '-');
                    $tglTx = $tx?->tanggal ? \Carbon\Carbon::parse($tx->tanggal)->format('d-m-Y H:i') : '-';
                @endphp

                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block font-semibold" style="font-size: 11px;">No. Nota / Pesanan</small>
                        <strong class="font-monospace text-dark" style="font-size: 13.5px;">{{ $kodeTx }}</strong>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block font-semibold" style="font-size: 11px;">Pembeli / Kasir</small>
                        <strong class="text-dark">{{ $pembeli }}</strong>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block font-semibold" style="font-size: 11px;">Tanggal Transaksi Asal</small>
                        <strong class="text-dark">{{ $tglTx }}</strong>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 2: PRODUK YANG DIRETUR -->
            <div class="p-3.5 rounded-4 mb-4" style="background: #FFF5F7; border: 1px solid #F7E5EA;">
                <h6 class="fw-bold mb-3 text-dark" style="font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-box-seam me-2" style="color: #EC95A8;"></i>Barang / Produk yang Di-Retur
                </h6>

                <div class="row g-3 align-items-center">
                    <div class="col-md-7">
                        <h6 class="fw-bold text-dark mb-1">{{ $retur->produk->nama_produk ?? 'Produk Telah Dihapus' }}</h6>
                        <small class="text-muted font-monospace me-2">Kode: {{ $retur->produk->kode_produk ?? '-' }}</small>
                        <span class="badge bg-white text-dark border">Harga Jual: Rp {{ number_format($retur->produk->harga_jual ?? 0, 0, ',', '.') }}</span>
                    </div>

                    <div class="col-md-5 text-md-end">
                        <small class="text-muted d-block font-semibold" style="font-size: 11px;">Jumlah Retur</small>
                        <span class="fs-5 fw-bold text-danger">{{ $retur->qty }} pcs</span>
                    </div>
                </div>

                <hr style="border-color: #F7E5EA;" class="my-3">

                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block font-semibold" style="font-size: 11px;">Alasan Retur</small>
                        <span class="badge bg-light text-dark border font-semibold px-2.5 py-1.5 mt-1" style="font-size: 12px;">
                            {{ str_replace('_', ' ', ucfirst($retur->alasan)) }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block font-semibold" style="font-size: 11px;">Kondisi Fisik Barang</small>
                        @if($retur->kondisi_barang === 'layak_jual')
                            <span class="badge bg-success-subtle text-success border border-success-subtle font-semibold px-2.5 py-1.5 mt-1" style="font-size: 12px;">
                                Layak Jual (Restok (+{{ $retur->qty }} pcs) ke Inventaris)
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-semibold px-2.5 py-1.5 mt-1" style="font-size: 12px;">
                                Tidak Layak (Kerugian Finansial)
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- BAGIAN 3: BARANG PENGGANTI (TUKAR BARANG) -->
            <div class="p-3.5 rounded-4 mb-4" style="background: var(--pink-soft-2); border: 1px solid var(--border-soft);">
                <h6 class="fw-bold mb-3 text-dark" style="font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-arrow-repeat me-2" style="color: var(--pink-primary);"></i>Barang Pengganti (Tukar Barang)
                </h6>

                @if($retur->produkPengganti)
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">{{ $retur->produkPengganti->nama_produk }}</h6>
                            <small class="text-muted font-monospace me-2">Kode: {{ $retur->produkPengganti->kode_produk }}</small>
                            <span class="badge bg-white text-dark border">Stok Inventaris Saat Ini: {{ $retur->produkPengganti->stok }} pcs</span>
                        </div>
                        <span class="badge bg-yb px-3 py-2 rounded-pill font-semibold text-white">
                            Dipotong {{ $retur->qty_pengganti ?? $retur->qty }} pcs dari stok
                        </span>
                    </div>
                @else
                    <span class="text-muted font-semibold">Produk pengganti tidak ditentukan.</span>
                @endif
            </div>

            <!-- BAGIAN 4: DAMPAK FINANSIAL & OPERATOR -->
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 rounded-4" style="background: #FAF5FF; border: 1px solid #E9D5FF;">
                        <small class="text-muted d-block font-semibold" style="font-size: 11px;">Nilai Kerugian Terhitung</small>
                        <strong class="fs-5" style="color: #6B21A8;">
                            Rp {{ number_format($retur->nilai_kerugian ?? 0, 0, ',', '.') }}
                        </strong>
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">
                            (HPP × Qty Retur + Ongkir Retur)
                        </small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 rounded-4" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                        <small class="text-muted d-block font-semibold" style="font-size: 11px;">Ongkir Retur & Diproses Oleh</small>
                        <div class="fw-bold text-dark mb-1">Ongkir: Rp {{ number_format($retur->ongkir_retur ?? 0, 0, ',', '.') }}</div>
                        <small class="text-muted font-semibold">Operator: {{ $retur->user->nama ?? $retur->user->name ?? 'Admin' }}</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
