@extends('layouts.app')

@section('title', 'Detail Produk — ' . $produk->nama_produk)

@section('content')

<!-- Header Action Nav -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <a href="{{ route('produk.index') }}" class="btn btn-yb-outline text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Produk
    </a>
    <div class="d-flex gap-2">
        @if($prevProduk)
            <a href="{{ route('produk.show', $prevProduk->id) }}" id="btnPrevProduk" class="btn btn-white border rounded-pill px-3 py-2 font-semibold text-decoration-none" title="Sebelumnya: {{ $prevProduk->nama_produk }}" style="font-size: 13px; color: var(--ink);">
                <i class="bi bi-chevron-left me-1" style="color: var(--pink-primary);"></i> <span class="d-none d-sm-inline">Sebelumnya</span>
            </a>
        @endif
        @if($nextProduk)
            <a href="{{ route('produk.show', $nextProduk->id) }}" id="btnNextProduk" class="btn btn-white border rounded-pill px-3 py-2 font-semibold text-decoration-none" title="Selanjutnya: {{ $nextProduk->nama_produk }}" style="font-size: 13px; color: var(--ink);">
                <span class="d-none d-sm-inline">Selanjutnya</span> <i class="bi bi-chevron-right ms-1" style="color: var(--pink-primary);"></i>
            </a>
        @endif
        <a href="{{ route('produk.edit', $produk->id) }}" class="btn btn-yb rounded-pill px-3.5 py-2 font-semibold text-decoration-none">
            <i class="bi bi-pencil-square me-1"></i> Edit Produk
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Informasi Utama Produk -->
    <div class="col-12 col-lg-4">
        <div class="card-yb p-4 h-100 position-relative overflow-hidden">
            <!-- FOTO PRODUK -->
            @if($produk->foto && file_exists(storage_path('app/public/' . $produk->foto)))
            <div class="mb-4" style="border-radius: 14px; overflow: hidden; background: #fff; aspect-ratio: 1/1; border: 1.5px solid var(--border-soft); box-shadow: inset 0 0 10px rgba(0,0,0,0.02);">
                <img src="{{ asset('storage/' . $produk->foto) }}" alt="{{ $produk->nama_produk }}" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            @endif

            <div class="d-flex align-items-center gap-3 mb-4 pb-3" style="border-bottom: 1.5px dashed var(--border-soft);">
                @if(!$produk->foto || !file_exists(storage_path('app/public/' . $produk->foto)))
                <div style="width: 52px; height: 52px; border-radius: 18px; background: var(--pink-soft); display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--pink-primary-dark); transform: rotate(-5deg); flex-shrink: 0;">
                    <i class="bi bi-bag-heart"></i>
                </div>
                @endif
                <div>
                    <h5 class="fw-bold mb-1" style="color: var(--ink); font-family: 'Quicksand', sans-serif; line-height: 1.3;">{{ $produk->nama_produk }}</h5>
                    <small class="text-muted font-semibold" style="font-size: 11.5px;">Kode: <span class="text-dark fw-bold">{{ $produk->kode_produk }}</span></small>
                </div>
            </div>

            <!-- Detail Grid -->
            <div class="mb-3">
                <small class="text-muted d-block font-semibold mb-1" style="font-size: 11px;">KATEGORI & STATUS</small>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" style="background: var(--pink-soft); color: var(--pink-primary-dark); font-size: 11px;">{{ ucfirst($produk->jenis) }}</span>
                    @if($produk->status == 'aktif')
                        <span class="badge bg-success" style="font-size: 11px;">Aktif / Dijual</span>
                    @else
                        <span class="badge bg-secondary" style="font-size: 11px;">Nonaktif</span>
                    @endif
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="p-3 rounded-3" style="background: var(--pink-soft-2); border: 1px solid var(--border-soft);">
                        <small class="text-muted d-block font-semibold mb-1" style="font-size: 10.5px;">HARGA JUAL</small>
                        <span class="fw-bold fs-6" style="color: #52976D;">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-3" style="background: #FFF5F5; border: 1px solid #FCEAEA;">
                        <small class="text-muted d-block font-semibold mb-1" style="font-size: 10.5px;">HPP (MODAL)</small>
                        <span class="fw-bold fs-6" style="color: #AD5050;">Rp {{ number_format($produk->hpp_otomatis, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Estimasi Margin Profit -->
            @php
                $marginUnit = $produk->harga_jual - $produk->hpp_otomatis;
                $persenMargin = $produk->harga_jual > 0 ? round(($marginUnit / $produk->harga_jual) * 100, 1) : 0;
            @endphp
            <div class="p-3 rounded-3 mb-3" style="background: #EDF5EA; border: 1px solid #ACC9A4;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="d-block font-semibold" style="font-size: 11px; color: #56794D;">ESTIMASI PROFIT / PRODUK</small>
                        <strong style="color: #2D7A4D; font-size: 15px;">Rp {{ number_format($marginUnit, 0, ',', '.') }}</strong>
                    </div>
                    <span class="badge bg-success" style="font-size: 11px;">{{ $persenMargin }}% Margin</span>
                </div>
            </div>

            <div>
                <small class="text-muted d-block font-semibold mb-1" style="font-size: 11px;">SISA STOK SAAT INI</small>
                <div class="d-flex align-items-baseline gap-2">
                    <span class="fw-bold fs-3" style="color: var(--ink);">{{ number_format($produk->stok) }}</span>
                    <small class="text-muted font-semibold">pcs tersedia di gudang</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Barang Masuk -->
    <div class="col-12 col-lg-8">
        <div class="card-yb p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-clock-history me-2" style="color: var(--pink-primary);"></i>Riwayat Barang Masuk (Restock)
                </h6>
                <span class="badge bg-light text-muted" style="font-size: 11px;">{{ count($riwayat) }} Transaksi Pembelian</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-yb align-middle">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pemasok</th>
                            <th>Jumlah Masuk</th>
                            <th>Harga Beli / Produk</th>
                            <th class="text-end">Total Modal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $r)
                            @php
                                $rawDate = $r->tanggal_pembelian ?? $r->tanggal ?? $r->created_at;
                                $tanggalDisplay = $rawDate ? \Carbon\Carbon::parse($rawDate)->format('d M Y') : '-';
                                $namaPemasok = $r->pemasok->nama_pemasok ?? $produk->pemasok->nama_pemasok ?? 'Pemasok Utama';
                                $qtyMasuk = $r->qty ?? $r->jumlah ?? 0;
                                $hargaUnit = $r->harga_beli_per_unit ?? $r->harga_satuan ?? 0;
                                $totalModal = ($r->total_harga > 0 ? $r->total_harga : ($r->total_modal > 0 ? $r->total_modal : ($qtyMasuk * $hargaUnit)));
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $tanggalDisplay }}</td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark fw-bold border" style="font-size: 11px;">
                                        <i class="bi bi-truck me-1" style="color: var(--pink-primary);"></i>{{ $namaPemasok }}
                                    </span>
                                </td>
                                <td><span class="badge bg-success" style="font-size: 11.5px; padding: 6px 12px;">+{{ number_format($qtyMasuk) }} pcs</span></td>
                                <td>Rp {{ number_format($hargaUnit, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold" style="color: var(--ink);">Rp {{ number_format($totalModal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-box-seam fs-1 d-block mb-2" style="color: var(--border-soft);"></i>
                                <span class="font-semibold">Belum ada riwayat pembelian restock untuk produk ini.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('keydown', function(e) {
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) return;
        
        if (e.key === 'ArrowLeft') {
            const prevLink = document.getElementById('btnPrevProduk');
            if (prevLink) {
                window.location.href = prevLink.href;
            }
        } else if (e.key === 'ArrowRight') {
            const nextLink = document.getElementById('btnNextProduk');
            if (nextLink) {
                window.location.href = nextLink.href;
            }
        }
    });
</script>
@endpush

@endsection