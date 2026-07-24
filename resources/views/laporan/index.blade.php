@extends('layouts.app')

@section('title', 'Laporan Laba Rugi ')

@section('content')

<div class="card-yb p-4 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="dari" class="form-control" value="{{ $dari }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="sampai" class="form-control" value="{{ $sampai }}">
        </div>
        <div class="col-md-2"><button class="btn btn-yb w-100">Tampilkan</button></div>
        <div class="col-md-2">
            <a href="{{ route('laporan.unduh', request()->all()) }}" class="btn btn-yb-outline w-100"><i class="bi bi-download"></i> PDF</a>
        </div>
    </form>
</div>

<div class="row g-3 mb-3">
    <div class="col" style="flex:1">
        <div class="stat-card">
            <div class="stat-label">Total Penjualan</div>
            <div class="stat-value" style="font-size:18px">Rp {{ number_format($totalPenjualan,0,',','.') }}</div>
        </div>
    </div>
    <div class="col" style="flex:1">
        <div class="stat-card">
            <div class="stat-label">Total HPP</div>
            <div class="stat-value" style="font-size:18px">Rp {{ number_format($totalHpp,0,',','.') }}</div>
        </div>
    </div>
    <div class="col" style="flex:1">
        <div class="stat-card">
            <div class="stat-label">Laba Kotor</div>
            <div class="stat-value" style="font-size:18px;color:#56794D">Rp {{ number_format($labaKotor,0,',','.') }}</div>
        </div>
    </div>
    <div class="col" style="flex:1">
        <div class="stat-card">
            <div class="stat-label">Kerugian Retur</div>
            <div class="stat-value" style="font-size:18px;color:#AD5050">Rp {{ number_format($kerugianRetur,0,',','.') }}</div>
        </div>
    </div>
    <div class="col" style="flex:1">
        <div class="stat-card">
            <div class="stat-label">Laba Bersih Est.</div>
            <div class="stat-value" style="font-size:18px;color:#56794D">Rp {{ number_format($labaBersih,0,',','.') }}</div>
        </div>
    </div>
</div>

<div class="card-yb p-4 mb-3">
    <h6 class="fw-bold mb-3" style="font-family:'Quicksand',sans-serif"> Rincian Transaksi POS</h6>
    <div class="table-responsive">
        <table class="table table-yb">
            <thead><tr><th>Tanggal</th><th>Kode</th><th>Kasir</th><th class="text-end">Total</th><th class="text-end">HPP</th><th class="text-end">Margin</th></tr></thead>
            <tbody>
                @forelse($transaksiPos as $t)
                <tr>
                    <td>{{ $t->tanggal->format('d M Y') }}</td>
                    <td class="fw-semibold">{{ $t->kode_transaksi }}</td>
                    <td>{{ $t->user->nama }}</td>
                    <td class="text-end">Rp {{ number_format($t->total_harga,0,',','.') }}</td>
                    <td class="text-end">Rp {{ number_format($t->total_hpp,0,',','.') }}</td>
                    <td class="text-end fw-bold" style="color:#56794D">Rp {{ number_format($t->total_harga - $t->total_hpp,0,',','.') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-secondary py-3">Tidak ada transaksi POS pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card-yb p-4">
    <h6 class="fw-bold mb-3" style="font-family:'Quicksand',sans-serif"> Rincian Pesanan Online</h6>
    <div class="table-responsive">
        <table class="table table-yb">
            <thead><tr><th>Tanggal</th><th>No. Pesanan</th><th>Platform</th><th class="text-end">Total</th><th class="text-end">HPP</th><th class="text-end">Margin</th></tr></thead>
            <tbody>
                @forelse($pesananOnline as $p)
                <tr>
                    <td>{{ $p->tanggal->format('d M Y') }}</td>
                    <td class="fw-semibold">{{ $p->no_pesanan }}</td>
                    <td><span class="badge {{ $p->platform=='shopee'?'bg-warning':'bg-light' }}">{{ ucfirst($p->platform) }}</span></td>
                    <td class="text-end">Rp {{ number_format($p->total_harga,0,',','.') }}</td>
                    <td class="text-end">Rp {{ number_format($p->total_hpp,0,',','.') }}</td>
                    <td class="text-end fw-bold" style="color:#56794D">Rp {{ number_format($p->total_harga - $p->total_hpp,0,',','.') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-secondary py-3">Tidak ada pesanan online pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
