@extends('layouts.app')

@section('title', 'Detail Produk ')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold m-0" style="font-family:'Quicksand',sans-serif">
        <a href="{{ route('produk.index') }}" class="text-decoration-none text-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </h5>
    <a href="{{ route('produk.edit', $produk->id) }}" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit Produk</a>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card-yb p-4 h-100">
            <h6 class="fw-bold mb-3 text-secondary text-uppercase">Informasi Produk</h6>
            
            <div class="mb-3">
                <small class="text-muted d-block">Kode Produk</small>
                <span class="fw-bold fs-5">{{ $produk->kode_produk }}</span>
            </div>
            
            <div class="mb-3">
                <small class="text-muted d-block">Nama Produk</small>
                <span class="fw-semibold fs-5">{{ $produk->nama_produk }}</span>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Jenis</small>
                <span class="badge bg-primary">{{ ucfirst($produk->jenis) }}</span>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Harga Jual</small>
                <span class="fw-bold text-success fs-5">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</span>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">HPP (Modal Rata-rata)</small>
                <span class="fw-bold text-danger fs-5">Rp {{ number_format($produk->hpp_otomatis, 0, ',', '.') }}</span>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Sisa Stok</small>
                <span class="fw-bold fs-4">{{ $produk->stok }} <small class="fs-6 fw-normal text-muted">pcs</small></span>
            </div>

            <div>
                <small class="text-muted d-block">Status</small>
                @if($produk->status == 'aktif')
                    <span class="badge bg-success">Aktif / Dijual</span>
                @else
                    <span class="badge bg-secondary">Nonaktif</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8 mb-4">
        <div class="card-yb p-4 h-100">
            <h6 class="fw-bold mb-3 text-secondary text-uppercase">Riwayat Barang Masuk (Restock)</h6>
            
            <div class="table-responsive">
                <table class="table table-yb align-middle">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pemasok</th>
                            <th>Jml Masuk</th>
                            <th>Harga Beli / Unit</th>
                            <th class="text-end">Total Modal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $r)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
                            <td>{{ $r->pemasok->nama_pemasok ?? 'Tidak Diketahui' }}</td>
                            <td class="fw-bold text-primary">+{{ $r->jumlah }}</td>
                            <td>Rp {{ number_format($r->harga_beli_per_unit, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($r->total_modal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-4">Belum ada riwayat pembelian untuk produk ini 🌷</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection