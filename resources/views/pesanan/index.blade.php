@extends('layouts.app')

@section('title', 'Riwayat Pesanan Online ')

@section('content')

<div class="card-yb p-4 mb-3">
    <div class="d-flex justify-content-between">
        <form method="GET" class="row g-2 grow">
            <div class="col-md-3">
                <select name="platform" class="form-select">
                    <option value="">Semua Platform</option>
                    <option value="shopee" {{ request('platform')=='shopee'?'selected':'' }}>Shopee</option>
                    <option value="tiktok" {{ request('platform')=='tiktok'?'selected':'' }}>TikTok</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="diproses" {{ request('status')=='diproses'?'selected':'' }}>Diproses</option>
                    <option value="dikirim" {{ request('status')=='dikirim'?'selected':'' }}>Dikirim</option>
                    <option value="selesai" {{ request('status')=='selesai'?'selected':'' }}>Selesai</option>
                </select>
            </div>
            <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Cari no. pesanan / pembeli..." value="{{ request('search') }}"></div>
            <div class="col-md-2"><button class="btn btn-yb w-100">Filter</button></div>
        </form>
        <a href="{{ route('pesanan.import') }}" class="btn btn-yb ms-2"><i class="bi bi-cloud-upload"></i> Import File</a>
    </div>
</div>

<div class="card-yb p-4">
    <div class="table-responsive">
        <table class="table table-yb align-middle">
            <thead><tr><th>No. Pesanan</th><th>Platform</th><th>Pembeli</th><th>Tanggal</th><th class="text-end">Total</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($pesanan as $p)
                <tr>
                    <td class="fw-semibold">{{ $p->no_pesanan }}</td>
                    <td><span class="badge {{ $p->platform=='shopee' ? 'bg-warning' : 'bg-light' }}">{{ ucfirst($p->platform) }}</span></td>
                    <td>{{ $p->nama_pembeli }}</td>
                    <td>{{ $p->tanggal->format('d M Y') }}</td>
                    <td class="text-end fw-bold" style="color:var(--pink-primary-dark)">Rp {{ number_format($p->total_harga,0,',','.') }}</td>
                    <td><span class="badge bg-light">{{ ucfirst($p->status) }}</span></td>
                    <td><a href="{{ route('pesanan.show',$p->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-secondary py-4">Belum ada pesanan online </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $pesanan->links() }}
</div>

@endsection
