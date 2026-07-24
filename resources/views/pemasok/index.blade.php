@extends('layouts.app')

@section('title', 'Data Pemasok ')

@section('content')

<div class="card-yb p-4 mb-3">
    <div class="d-flex justify-content-between">
        <input type="text" class="form-control" style="max-width:300px" placeholder=" Cari pemasok...">
        <a href="{{ route('pemasok.create') }}" class="btn btn-yb"><i class="bi bi-plus-lg"></i> Tambah Pemasok</a>
    </div>
</div>

<div class="card-yb p-4">
    <div class="table-responsive">
        <table class="table table-yb align-middle">
            <thead><tr><th>Nama Pemasok</th><th>Kontak</th><th>Alamat</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($pemasok as $p)
                <tr>
                    <td class="fw-semibold">{{ $p->nama_pemasok }}</td>
                    <td>{{ $p->kontak ?? '-' }}</td>
                    <td>{{ $p->alamat ?? '-' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('pemasok.edit',$p->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('pemasok.destroy',$p->id) }}" method="POST" onsubmit="return confirm('Hapus pemasok ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-secondary py-4">Belum ada data pemasok </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $pemasok->links() }}
</div>

@endsection
