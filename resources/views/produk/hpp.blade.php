@extends('layouts.app')

@section('title', 'Tetapkan HPP Realisasi ')

@section('content')

<div class="card-yb p-4">
    <div style="background:var(--pink-soft-2);border-radius:18px;padding:14px 18px;margin-bottom:18px">
        <p class="text-secondary small mb-0 fw-semibold">
            💡 Owner dapat menetapkan HPP Realisasi kapan saja berdasarkan biaya aktual yang diketahui
            (misal biaya cuci, press, atau ongkir pembelian). Jika dikosongkan, sistem memakai HPP Otomatis.
        </p>
    </div>

    <div class="table-responsive">
        <table class="table table-yb align-middle">
            <thead>
                <tr><th>Kode</th><th>Nama Produk</th><th>HPP Otomatis</th><th>HPP Realisasi</th><th style="width:120px">Status</th></tr>
            </thead>
            <tbody>
                @foreach($produk as $p)
                <tr>
                    <td class="fw-semibold">{{ $p->kode_produk }}</td>
                    <td>{{ $p->nama_produk }}</td>
                    <td>Rp {{ number_format($p->hpp_otomatis,0,',','.') }}</td>
                    <td>
                        <form action="{{ route('produk.hpp.update',$p->id) }}" method="POST" class="d-flex gap-1">
                            @csrf @method('PUT')
                            <input type="number" name="hpp_realisasi" class="form-control form-control-sm"
                                   value="{{ $p->hpp_realisasi }}" placeholder="Pakai otomatis" style="border-radius:12px">
                            <button class="btn btn-sm btn-yb"><i class="bi bi-check-lg"></i></button>
                        </form>
                    </td>
                    <td>
                        @if($p->hpp_realisasi)
                            <span class="badge bg-success">Realisasi ✓</span>
                        @else
                            <span class="badge bg-light">Otomatis</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $produk->links() }}
</div>

@endsection
