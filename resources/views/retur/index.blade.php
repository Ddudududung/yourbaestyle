@extends('layouts.app')

@section('title', 'Manajemen Retur Barang')

@section('content')

<!-- Header Action -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
            <i class="bi bi-arrow-return-left me-2" style="color: var(--pink-primary);"></i>Manajemen Retur Barang
        </h5>
        <small class="text-muted font-semibold">Kelola retur barang penjualan online maupun kasir POS</small>
    </div>
    <a href="{{ route('retur.select') }}" class="btn btn-yb">
        <i class="bi bi-plus-lg me-1"></i> Tambah Retur
    </a>
</div>

<!-- Card Filter Rapi & Aesthetic -->
<div class="card-yb p-4 mb-4">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-12 col-md-5">
            <label class="form-label">Cari Nomor Pesanan / Transaksi</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--border-soft); border-radius: 16px 0 0 16px; color: var(--ink-soft);">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Ketik nomor pesanan..." value="{{ request('search') }}" style="border-radius: 0 16px 16px 0;">
            </div>
        </div>

        <div class="col-12 col-md-4">
            <label class="form-label">Kondisi Barang</label>
            <select name="kondisi" class="form-select">
                <option value="">Semua Kondisi</option>
                <option value="layak_jual" {{ request('kondisi') == 'layak_jual' ? 'selected' : '' }}>🟢 Layak Jual (Restok)</option>
                <option value="tidak_layak" {{ request('kondisi') == 'tidak_layak' ? 'selected' : '' }}>🔴 Tidak Layak (Rugi)</option>
            </select>
        </div>

        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-yb flex-grow-1">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
            @if(request()->has('search') || request()->has('kondisi'))
                <a href="{{ route('retur.index') }}" class="btn btn-yb-outline" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table Data Retur -->
<div class="card-yb p-4">
    <div class="table-responsive">
        <table class="table table-yb align-middle">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>No. Pesanan</th>
                    <th>Produk Retur</th>
                    <th>Opsi Retur</th>
                    <th>Kondisi</th>
                    <th>Nilai Kerugian</th>
                    <th class="text-end">Diproses Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($retur as $r)
                <tr>
                    <td class="fw-semibold">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
                    <td class="fw-bold" style="color: var(--ink);">{{ $r->pesananOnline->no_pesanan ?? 'POS Transaksi' }}</td>
                    <td>
                        <div class="fw-bold">{{ $r->produk->nama_produk ?? '-' }}</div>
                        <small class="text-muted font-semibold">Qty: {{ $r->qty }} pcs</small>
                    </td>
                    <td>
                        @if(($r->tipe_retur ?? 'tukar_barang') === 'tukar_barang')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-semibold px-2.5 py-1 mb-1 d-inline-block">
                                🔄 Tukar Barang
                            </span>
                            @if($r->produkPengganti)
                                <div class="text-success font-semibold" style="font-size: 11.5px;">
                                    <i class="bi bi-arrow-right-short me-0.5"></i>{{ $r->produkPengganti->nama_produk }} ({{ $r->qty_pengganti ?? 1 }} pcs)
                                </div>
                            @endif
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border font-semibold px-2.5 py-1">
                                📦 Tanpa Tukar
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($r->kondisi_barang == 'layak_jual')
                            <span class="badge bg-success-subtle text-success border border-success-subtle font-semibold">Layak Jual</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-semibold">Tidak Layak</span>
                        @endif
                    </td>
                    <td class="fw-semibold">
                        @if($r->nilai_kerugian > 0)
                            <span style="color: #AD5050;">Rp {{ number_format($r->nilai_kerugian, 0, ',', '.') }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-end font-semibold">{{ $r->user->nama ?? $r->user->name ?? 'Admin' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--border-soft);"></i>
                        <span class="font-semibold">Belum ada data retur barang yang ditemukan.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $retur->links() }}
    </div>
</div>

@endsection
