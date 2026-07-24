@extends('layouts.app')

@section('title', 'Manajemen Retur ↩')

@section('content')

<div class="card-yb p-4 mb-3">
    <div class="d-flex justify-content-between">
        <form method="GET" class="row g-2 grow">
            <div class="col-md-4">
                <select name="kondisi" class="form-select">
                    <option value="">Semua Kondisi</option>
                    <option value="layak_jual" {{ request('kondisi')=='layak_jual'?'selected':'' }}>Layak Jual</option>
                    <option value="tidak_layak" {{ request('kondisi')=='tidak_layak'?'selected':'' }}>Tidak Layak</option>
                </select>
            </div>
            <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Cari no. pesanan..." value="{{ request('search') }}"></div>
            <div class="col-md-2"><button class="btn btn-yb w-100">Filter</button></div>
        </form>
        <a href="{{ route('retur.create') }}" class="btn btn-yb ms-2"><i class="bi bi-plus-lg"></i> Tambah Retur</a>
    </div>
</div>

<div class="card-yb p-4">
    <div class="table-responsive">
        <table class="table table-yb align-middle">
            <thead><tr><th>Tanggal</th><th>No. Pesanan</th><th>Produk</th><th>Kondisi</th><th>Nilai Kerugian</th><th>Diproses Oleh</th></tr></thead>
            <tbody>
                @forelse($retur as $r)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
                    <td class="fw-semibold">{{ $r->pesananOnline->no_pesanan }}</td>
                    <td>{{ $r->produk->nama_produk }}</td>
                    <td>
                        @if($r->kondisi_barang == 'layak_jual')
                            <span class="badge bg-success">Layak Jual</span>
                        @else
                            <span class="badge bg-danger">Tidak Layak</span>
                        @endif
                    </td>
                    <td class="fw-semibold">
                        @if($r->nilai_kerugian > 0)
                            Rp {{ number_format($r->nilai_kerugian,0,',','.') }}
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $r->user->nama }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada data retur 🌷</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $retur->links() }}
</div>

@endsection
