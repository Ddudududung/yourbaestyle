@extends('layouts.app')

@section('title', 'Riwayat Transaksi ')

@section('content')

<div class="card-yb p-4 mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3"><input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}"></div>
        <div class="col-md-3"><input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}"></div>
        <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Cari kode transaksi..." value="{{ request('search') }}"></div>
        <div class="col-md-2"><button class="btn btn-yb w-100"><i class="bi bi-search"></i> Filter</button></div>
    </form>
</div>

<div class="card-yb p-4">
    <div class="table-responsive">
        <table class="table table-yb align-middle">
            <thead><tr><th>Kode Transaksi</th><th>Kasir</th><th>Tanggal</th><th>Metode</th><th class="text-end">Total</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($transaksi as $t)
                <tr>
                    <td class="fw-semibold">{{ $t->kode_transaksi }}</td>
                    <td>{{ $t->user->nama }}</td>
                    <td>{{ $t->tanggal->format('d M Y, H:i') }}</td>
                    <td><span class="badge bg-light">{{ ucfirst($t->metode_bayar) }}</span></td>
                    <td class="text-end fw-bold" style="color:var(--pink-primary-dark)">Rp {{ number_format($t->total_harga,0,',','.') }}</td>
                    <td><a href="{{ route('transaksi.show',$t->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer"></i> Cetak Ulang</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada transaksi 🌷</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $transaksi->links() }}
</div>

@endsection
