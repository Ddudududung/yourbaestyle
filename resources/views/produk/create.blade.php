@extends('layouts.app')

@section('title', 'Tambah Produk & HPP ')

@section('content')

<div class="card-yb p-4" style="max-width:740px">
    <h6 class="fw-bold mb-4" style="font-family:'Quicksand',sans-serif">Tambah Data Produk &amp; HPP</h6>

    <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <p class="text-secondary small fw-bold text-uppercase mb-3" style="letter-spacing:.4px">🧷 Data Produk</p>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" name="nama_produk" class="form-control" value="{{ old('nama_produk') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Jenis Produk <span class="text-danger">*</span></label>
<select name="jenis" id="jenis" class="form-select" required>
    <option value="thrift" {{ old('jenis') == 'thrift' ? 'selected' : '' }}>Thrift</option>
    <option value="rebranding" {{ old('jenis') == 'rebranding' ? 'selected' : '' }}>Rebranding</option>
</select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Pemasok <span class="text-danger">*</span></label>
                <select name="id_pemasok" class="form-select" required>
                    <option value="">— Pilih Pemasok —</option>
                    @foreach($pemasok as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_pemasok }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text" style="border-radius:16px 0 0 16px;background:var(--pink-soft);border:2px solid var(--border-soft);border-right:none">Rp</span>
                    <input type="number" name="harga_jual" id="harga_jual" class="form-control" value="{{ old('harga_jual') }}" required>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Foto Produk</label>
            <input type="file" name="foto" class="form-control" accept="image/png,image/jpeg">
        </div>

        <p class="text-secondary small fw-bold text-uppercase mb-3 mt-4" style="letter-spacing:.4px">💰 Data Pembelian &amp; HPP</p>
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label">Jumlah Unit <span class="text-danger">*</span></label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="{{ old('jumlah') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Harga Beli / Unit <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text" style="border-radius:16px 0 0 16px;background:var(--pink-soft);border:2px solid var(--border-soft);border-right:none">Rp</span>
                    <input type="number" name="harga_beli_per_unit" id="harga_beli" class="form-control" value="{{ old('harga_beli_per_unit') }}" required>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">HPP Otomatis</label>
                <div class="input-group">
                    <span class="input-group-text" style="border-radius:16px 0 0 16px;background:var(--sage-soft);border:2px solid var(--border-soft);border-right:none">Rp</span>
                    <input type="text" id="hpp_otomatis_display" class="form-control" readonly placeholder="0" style="background:var(--sage-soft)">
                </div>
            </div>
        </div>

        <div class="mb-4" style="background:var(--amber-soft);border-radius:18px;padding:16px 18px">
            <label class="form-label">
                ✨ HPP Realisasi
                <span class="text-secondary fw-normal" style="font-size:11px">(opsional — kosongkan jika belum diketahui)</span>
            </label>
            <div class="input-group">
                <span class="input-group-text" style="border-radius:16px 0 0 16px;background:#fff;border:2px solid var(--border-soft);border-right:none">Rp</span>
                <input type="number" name="hpp_realisasi" class="form-control" value="{{ old('hpp_realisasi') }}" placeholder="Isi jika biaya aktual sudah diketahui" style="background:#fff">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('produk.index') }}" class="btn btn-yb-outline">Batal</a>
            <button type="submit" class="btn btn-yb">Simpan Produk 🌸</button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    function hitungHPP() {
        const hargaBeli = parseFloat(document.getElementById('harga_beli').value) || 0;
        document.getElementById('hpp_otomatis_display').value = hargaBeli.toLocaleString('id-ID');
    }
    document.getElementById('harga_beli').addEventListener('input', hitungHPP);
    document.getElementById('jumlah').addEventListener('input', hitungHPP);
</script>
@endpush
