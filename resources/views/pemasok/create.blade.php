@extends('layouts.app')

@section('title', 'Tambah Pemasok ')

@section('content')

<div class="card-yb p-4" style="max-width:580px">
    <h6 class="fw-bold mb-4" style="font-family:'Quicksand',sans-serif">Tambah Pemasok</h6>
    <form action="{{ route('pemasok.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Pemasok <span class="text-danger">*</span></label>
            <input type="text" name="nama_pemasok" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Kontak</label>
            <input type="text" name="kontak" class="form-control" placeholder="08xxxxxxxxxx">
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" rows="2"></textarea>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('pemasok.index') }}" class="btn btn-yb-outline">Batal</a>
            <button class="btn btn-yb">Simpan</button>
        </div>
    </form>
</div>

@endsection
