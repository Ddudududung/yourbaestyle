@extends('layouts.app')

@section('title', 'Manajemen Retur Barang — Yourbaestyle')

@section('content')

<!-- Header Action -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
            <i class="bi bi-arrow-return-left me-2" style="color: var(--pink-primary);"></i>Manajemen Retur Barang
        </h5>
        <small class="text-muted font-semibold">Kelola retur barang penjualan Online maupun Kasir POS Offline</small>
    </div>
    <a href="{{ route('retur.select') }}" class="btn btn-yb px-4 py-2 font-semibold text-white shadow-sm" style="border-radius: 12px; font-size: 13px;">
        <i class="bi bi-plus-lg me-1"></i> Tambah Retur Transaksi
    </a>
</div>

<!-- ALERT SUKSES -->
@if(session('success'))
    <div class="p-3 mb-4 d-flex align-items-center gap-2" style="background: #E8F7EE; border: 1.5px solid #A3E2BB; border-radius: 16px; color: #276A43; font-size: 13px; font-weight: 600;">
        <i class="bi bi-check-circle-fill fs-5 me-1" style="color: #4CAF50;"></i>
        {{ session('success') }}
    </div>
@endif

<!-- ALERT ERROR -->
@if(session('error'))
    <div class="p-3 mb-4 d-flex align-items-center gap-2" style="background: #FFF0F2; border: 1.5px solid #F5B8C5; border-radius: 16px; color: #A6243F; font-size: 13px; font-weight: 600;">
        <i class="bi bi-exclamation-triangle-fill fs-5 me-1" style="color: #E05270;"></i>
        {{ session('error') }}
    </div>
@endif

<!-- Card Filter Rapi & Aesthetic -->
<div class="card-yb p-4 mb-4">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-12 col-md-5">
            <label class="form-label font-semibold text-muted mb-1" style="font-size: 11.5px;">Cari No. Pesanan / Transaksi / Produk</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--border-soft); border-radius: 16px 0 0 16px; color: var(--ink-soft);">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="search" class="form-control border-start-0 font-semibold" placeholder="Ketik nomor pesanan atau nama produk..." value="{{ request('search') }}" style="border-radius: 0 16px 16px 0; font-size: 13px;">
            </div>
        </div>

        <div class="col-12 col-md-4">
            <label class="form-label font-semibold text-muted mb-1" style="font-size: 11.5px;">Kondisi Barang</label>
            <select name="kondisi" class="form-select font-semibold" style="border-radius: 14px; font-size: 13px;">
                <option value="">Semua Kondisi</option>
                <option value="layak_jual" {{ request('kondisi') == 'layak_jual' ? 'selected' : '' }}>Layak Jual (Restok)</option>
                <option value="tidak_layak" {{ request('kondisi') == 'tidak_layak' ? 'selected' : '' }}>Tidak Layak (Kerugian)</option>
            </select>
        </div>

        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-yb flex-grow-1 font-semibold text-white shadow-sm" style="border-radius: 14px;">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
            @if(request()->has('search') || request()->has('kondisi'))
                <a href="{{ route('retur.index') }}" class="btn btn-yb-outline font-semibold" style="border-radius: 14px;" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table Data Retur -->
<div class="card-yb p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
            <thead style="background: var(--pink-soft-2); color: var(--ink-soft); font-size: 11.5px; font-family: 'Quicksand', sans-serif; text-transform: uppercase; font-weight: 700; border-bottom: 1.5px solid var(--border-soft);">
                <tr>
                    <th class="py-3 ps-4">Tanggal</th>
                    <th class="py-3">No. Transaksi</th>
                    <th class="py-3">Produk Retur</th>
                    <th class="py-3">Opsi Retur</th>
                    <th class="py-3">Kondisi</th>
                    <th class="py-3">Nilai Kerugian</th>
                    <th class="py-3">Operator</th>
                    <th class="py-3 pe-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody style="border-top: 1px solid var(--border-soft);">
                @forelse($retur as $r)
                @php
                    $isOnline = !empty($r->id_pesanan_online);
                    $kodeTx = $isOnline ? ($r->pesananOnline->no_pesanan ?? '-') : ($r->transaksiPos->kode_transaksi ?? '-');
                @endphp
                <tr>
                    <td class="py-3 ps-4 fw-semibold text-muted" style="font-size: 12px;">
                        {{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}
                    </td>
                    <td class="py-3">
                        <span class="badge mb-1 font-monospace" style="background: var(--pink-soft-2); color: var(--pink-primary-dark); border: 1px solid var(--border-soft); font-size: 11px;">
                            {{ $kodeTx }}
                        </span>
                        <br>
                        @if($isOnline)
                            <span class="badge px-2 py-0.5" style="background: #FFF0F3; color: var(--pink-primary-dark); border: 1px solid var(--border-soft); font-size: 9.5px; font-weight: 700;">ONLINE</span>
                        @else
                            <span class="badge px-2 py-0.5" style="background: #E8F7EE; color: #1E7E34; border: 1px solid #C3EEDB; font-size: 9.5px; font-weight: 700;">POS (OFFLINE)</span>
                        @endif
                    </td>
                    <td class="py-3">
                        <div class="fw-bold" style="color: var(--ink);">{{ $r->produk->nama_produk ?? 'Produk Telah Dihapus' }}</div>
                        <small class="text-muted font-semibold" style="font-size: 11.5px;">Jumlah Retur: <strong>{{ $r->qty }} pcs</strong></small>
                    </td>
                    <td class="py-3">
                        <span class="badge bg-white text-muted border font-semibold px-2.5 py-1 mb-1 d-inline-block" style="font-size: 10.5px;">
                            <i class="bi bi-arrow-repeat me-1" style="color: var(--pink-primary);"></i> Tukar Barang
                        </span>
                        @if($r->produkPengganti)
                            <div class="text-success font-semibold" style="font-size: 11.5px;">
                                <i class="bi bi-arrow-right-short me-0.5"></i>{{ $r->produkPengganti->nama_produk }} ({{ $r->qty_pengganti ?? $r->qty }} pcs)
                            </div>
                        @endif
                    </td>
                    <td class="py-3">
                        @if($r->kondisi_barang == 'layak_jual')
                            <span class="badge bg-success-subtle text-success border border-success-subtle font-semibold px-2.5 py-1" style="font-size: 10.5px;">Layak Jual (Restok)</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-semibold px-2.5 py-1" style="font-size: 10.5px;">Tidak Layak (Rugi)</span>
                        @endif
                    </td>
                    <td class="py-3 fw-semibold">
                        @if($r->nilai_kerugian > 0)
                            <span style="color: #AD5050; font-weight: 700;">Rp {{ number_format($r->nilai_kerugian, 0, ',', '.') }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="py-3 font-semibold text-muted" style="font-size: 12px;">{{ $r->user->nama ?? $r->user->name ?? 'Admin' }}</td>
                    <td class="py-3 pe-4 text-center">
                        <a href="{{ route('retur.show', $r->id) }}" class="btn btn-sm btn-yb-outline px-3 py-1 font-semibold" style="border-radius: 8px; font-size: 12px;">
                            <i class="bi bi-eye me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--border-soft);"></i>
                        <span class="font-semibold">Belum ada data retur barang yang ditemukan.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">
        {{ $retur->links() }}
    </div>
</div>

@endsection
