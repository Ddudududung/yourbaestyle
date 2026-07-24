@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $pesanan->no_pesanan . ' — Yourbaestyle')

@section('content')
<div class="container-fluid py-3" style="max-width: 1000px; margin: 0 auto;">
    
    <!-- HEADER & TOMBOL KEMBALI -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <a href="{{ route('pesanan.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold mb-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
            <h4 class="mb-0 fw-bold text-dark" style="font-family: 'Quicksand', sans-serif;">
                <i class="bi bi-receipt me-2" style="color: #EC95A8;"></i>Detail Pesanan Online
            </h4>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-sm rounded-pill px-3 fw-bold text-dark border" style="background: #FFF5F7; border-color: #F7E5EA !important;">
                <i class="bi bi-printer me-1" style="color: #EC95A8;"></i> Cetak Nota
            </button>
        </div>
    </div>

    <!-- KARTU INFORMASI PESANAN -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #fff; overflow: hidden;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color: #F7E5EA !important;">
            <div>
                <span class="text-muted small d-block">Nomor Pesanan:</span>
                <code class="fw-bold fs-6" style="color: #EC95A8; background: #FFF0F3; padding: 4px 10px; border-radius: 6px;">{{ $pesanan->no_pesanan }}</code>
            </div>
            <div>
                @if($pesanan->status === 'diproses')
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-6"><i class="bi bi-check-circle me-1"></i> Diproses</span>
                @elseif(in_array($pesanan->status, ['hold_review', 'hold_stok_kurang']))
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fs-6"><i class="bi bi-exclamation-triangle me-1"></i> Hold Review</span>
                @elseif($pesanan->status === 'cancel')
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fs-6"><i class="bi bi-x-circle me-1"></i> Dibatalkan</span>
                @else
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fs-6">{{ strtoupper($pesanan->status) }}</span>
                @endif
            </div>
        </div>
        
        <div class="card-body p-4" style="background: #FAF8F9;">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <span class="text-muted small d-block">Platform:</span>
                    <strong class="text-dark d-block" style="font-size: 15px;">
                        <i class="bi bi-bag-check-fill me-1" style="color: #EC95A8;"></i>{{ strtoupper($pesanan->platform) }}
                    </strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted small d-block">Pembeli / Penerima:</span>
                    <strong class="text-dark d-block" style="font-size: 15px;">{{ $pesanan->nama_pembeli }}</strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted small d-block">Tanggal Checkout:</span>
                    <strong class="text-dark d-block" style="font-size: 15px;">
                        {{ $pesanan->tanggal ? \Carbon\Carbon::parse($pesanan->tanggal)->format('d M Y, H:i') : '-' }}
                    </strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted small d-block">Di-import Oleh:</span>
                    <strong class="text-dark d-block" style="font-size: 15px;">{{ $pesanan->user->nama ?? 'System / Admin' }}</strong>
                </div>
            </div>

            @if($pesanan->pesan_mapping)
            <div class="alert rounded-3 mt-3 mb-0 p-2 border-0 small" style="background: #FFF0F3; color: #AD5050;">
                <i class="bi bi-info-circle-fill me-1"></i> <strong>Catatan Mapping:</strong> {{ $pesanan->pesan_mapping }}
            </div>
            @endif
        </div>
    </div>

    <!-- KARTU DAFTAR ITEM YANG DIBELI -->
    <div class="card border-0 shadow-sm rounded-4" style="background: #fff; overflow: hidden;">
        <div class="card-header bg-white py-3 px-4 border-bottom" style="border-color: #F7E5EA !important;">
            <h6 class="mb-0 fw-bold" style="color: #4D3D43;">
                <i class="bi bi-box-seam me-2" style="color: #EC95A8;"></i>Daftar Item Produk ({{ $pesanan->detail->count() }} Barang)
            </h6>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                    <thead style="background-color: #FFF5F7; color: #7A686D; font-size: 11.5px; text-transform: uppercase;">
                        <tr>
                            <th class="py-3 ps-4 border-0" style="width: 5%;">No</th>
                            <th class="py-3 border-0" style="width: 45%;">Nama Produk & Kode Variasi</th>
                            <th class="py-3 text-center border-0" style="width: 15%;">Harga Satuan</th>
                            <th class="py-3 text-center border-0" style="width: 15%;">Qty</th>
                            <th class="py-3 pe-4 text-end border-0" style="width: 20%;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: 1px solid #F7E5EA;">
                        @forelse($pesanan->detail as $index => $item)
                        <tr>
                            <td class="ps-4 text-muted fw-bold">{{ $index + 1 }}</td>
                            <td>
                                @if($item->produk)
                                    <strong class="text-dark d-block" style="font-size: 14.5px;">{{ $item->produk->nama_produk }}</strong>
                                    <small class="text-muted">Kode Gudang: <code>{{ $item->produk->kode_produk }}</code></small>
                                @else
                                    <strong class="text-danger d-block" style="font-size: 14.5px;">[Belum Dimapping ke Produk Fisik]</strong>
                                    <small class="text-muted">Kode Live dari Excel: <span class="badge bg-light text-dark border">{{ $item->variasi ?? $pesanan->kode_live_raw ?? '-' }}</span></small>
                                @endif
                            </td>
                            <td class="text-center text-dark">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="fw-bold px-3 py-1 rounded-3" style="background: #FFF5F7; color: #EC95A8;">{{ $item->qty }} pcs</span>
                            </td>
                            <td class="pe-4 text-end fw-bold text-dark" style="font-size: 14.5px;">
                                Rp {{ number_format($item->harga_satuan * $item->qty, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Tidak ada detail produk untuk pesanan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot style="background: #FAF8F9; border-top: 2px solid #F7E5EA;">
                        <tr>
                            <td colspan="4" class="text-end py-3 fw-bold text-dark" style="font-size: 14px;">Ongkos Kirim:</td>
                            <td class="pe-4 text-end fw-bold text-dark" style="font-size: 14px;">Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end py-3 fw-bold" style="color: #4D3D43; font-size: 16px;">Total Pembayaran:</td>
                            <td class="pe-4 text-end fw-bold" style="color: #EC95A8; font-size: 18px;">
                                Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- STYLE KHUSUS SAAT DICETAK KE PRINTER / PDF -->
<style>
    @media print {
        body { background: #fff !important; }
        .navbar, .btn, footer, .sidebar { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        .container-fluid { max-width: 100% !important; padding: 0 !important; }
    }
</style>
@endsection