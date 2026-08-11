@extends('layouts.app')

@section('title', 'Manajemen Produk — Yourbaestyle')

@section('content')

<!-- HEADER & ACTION BUTTON -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h4 class="mb-1 fw-bold" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
            <i class="bi bi-box-seam me-2" style="color: var(--pink-primary);"></i>Manajemen & Katalog Produk
        </h4>
        <small class="text-muted font-semibold" style="font-size: 12px;">Kelola persediaan stok, spesifikasi, dan harga jual barang toko</small>
    </div>
    <a href="{{ route('produk.create') }}" class="btn btn-yb px-4 py-2.5 font-semibold text-decoration-none shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Produk Baru
    </a>
</div>

<!-- PANEL PENCARIAN & FILTER PRODUK -->
<div class="card-yb p-3 p-md-4 mb-4">
    <form method="GET" action="{{ route('produk.index') }}" class="row g-2.5 align-items-end">
        <!-- Search Input -->
        <div class="col-12 col-lg-4">
            <label class="form-label font-semibold text-muted mb-1" style="font-size: 11.5px;">Cari Produk / Kode SKU</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="border-color: var(--border-soft); border-radius: 12px 0 0 12px; color: var(--ink-soft);">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Ketik nama produk / SKU..." value="{{ request('search', '') }}" style="border-radius: 0 12px 12px 0; border-color: var(--border-soft); font-size: 12.5px;">
            </div>
        </div>

        <!-- Filter Jenis Pakaian -->
        <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label font-semibold text-muted mb-1" style="font-size: 11.5px;">Jenis Pakaian</label>
            <select name="id_jenis_pakaian" class="form-select font-semibold" style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px;">
                <option value="">Semua Jenis</option>
                @foreach($jenisPakaian as $jp)
                    <option value="{{ $jp->id }}" {{ request('id_jenis_pakaian') == $jp->id ? 'selected' : '' }}>
                        {{ $jp->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Filter Warna -->
        <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label font-semibold text-muted mb-1" style="font-size: 11.5px;">Warna</label>
            <select name="id_warna" class="form-select font-semibold" style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px;">
                <option value="">Semua Warna</option>
                @foreach($warna as $w)
                    <option value="{{ $w->id }}" {{ request('id_warna') == $w->id ? 'selected' : '' }}>
                        {{ $w->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Filter Model -->
        <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label font-semibold text-muted mb-1" style="font-size: 11.5px;">Model / Motif</label>
            <select name="id_model" class="form-select font-semibold" style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px;">
                <option value="">Semua Model</option>
                @foreach($model as $m)
                    <option value="{{ $m->id }}" {{ request('id_model') == $m->id ? 'selected' : '' }}>
                        {{ $m->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Filter Status Produk -->
        <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label font-semibold text-muted mb-1" style="font-size: 11.5px;">Status Produk</label>
            <select name="status" class="form-select font-semibold" style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px;">
                <option value="aktif" {{ request('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                <option value="semua" {{ request('status') === 'semua' ? 'selected' : '' }}>Semua Status</option>
            </select>
        </div>

        <!-- Filter Status Stok -->
        <div class="col-6 col-md-4 col-lg-3">
            <label class="form-label font-semibold text-muted mb-1" style="font-size: 11.5px;">Status Stok</label>
            <select name="stok" class="form-select font-semibold" style="border-radius: 12px; border-color: var(--border-soft); font-size: 12.5px;">
                <option value="">Semua Stok</option>
                <option value="ada" {{ request('stok') === 'ada' ? 'selected' : '' }}>Ready (>0)</option>
                <option value="habis" {{ request('stok') === 'habis' ? 'selected' : '' }}>Stok Habis (0)</option>
            </select>
        </div>

        <!-- Tombol Submit & Reset -->
        <div class="col-12 col-md-8 col-lg-9 ms-auto d-flex justify-content-end gap-2 mt-2">
            <button type="submit" class="btn btn-yb px-4 py-2 font-semibold shadow-sm text-white" style="border-radius: 12px; font-size: 13px;">
                <i class="bi bi-search me-1.5"></i> Cari & Filter Produk
            </button>
            @if(request()->anyFilled(['search', 'id_jenis_pakaian', 'id_warna', 'id_model', 'status', 'stok']))
                <a href="{{ route('produk.index') }}" class="btn btn-yb-outline px-3 py-2 text-decoration-none font-semibold" style="border-radius: 12px; font-size: 13px;">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- TABEL DAFTAR PRODUK (DESKTOP & MOBILE) -->
<div class="card-yb p-0 overflow-hidden">
    <div class="p-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1.5px dashed var(--border-soft); background: var(--pink-soft-2);">
        <h6 class="fw-bold mb-0" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
            <i class="bi bi-list-stars me-2" style="color: var(--pink-primary);"></i>Daftar Katalog Produk
        </h6>
        <span class="badge bg-white text-muted font-semibold px-3 py-1.5" style="border: 1px solid var(--border-soft); font-size: 11px;">
            Total: {{ $produk->total() }} Produk
        </span>
    </div>

    <!-- Desktop Table (Layar Besar) -->
    <div class="d-none d-lg-block">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead style="background: var(--pink-soft-2); color: var(--ink-soft); font-size: 11.5px; font-family: 'Quicksand', sans-serif; text-transform: uppercase; font-weight: 700; border-bottom: 1.5px solid var(--border-soft);">
                    <tr>
                        <th class="py-3 ps-4">SKU / Kode</th>
                        <th class="py-3">Nama Produk</th>
                        <th class="py-3">Spesifikasi</th>
                        <th class="py-3 text-end">Harga Beli</th>
                        <th class="py-3 text-end">Harga Jual</th>
                        <th class="py-3 text-center">Stok</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody style="border-top: 1px solid var(--border-soft);">
                    @forelse($produk as $p)
                    <tr style="transition: background-color 0.15s ease;">
                        <!-- SKU / Kode -->
                        <td class="py-3 ps-4">
                            <span class="badge" style="background: var(--pink-soft-2); color: var(--pink-primary-dark); border: 1px solid var(--border-soft); font-family: monospace; font-size: 11.5px; padding: 5px 8px;">
                                {{ $p->kode_produk ?? '-' }}
                            </span>
                        </td>

                        <!-- Nama Produk -->
                        <td class="py-3">
                            <a href="{{ route('produk.show', $p->id) }}" class="fw-bold text-decoration-none" style="color: var(--ink);" title="{{ $p->nama_produk }}">
                                {{ \Illuminate\Support\Str::limit($p->nama_produk, 32) }}
                            </a>
                        </td>

                        <!-- Spesifikasi -->
                        <td class="py-3">
                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                <span class="badge bg-light text-dark font-semibold border" style="font-size: 10px;">{{ strtoupper($p->jenisPakaian->nama ?? '-') }}</span>
                                <span class="text-muted" style="font-size: 11px;">{{ $p->warna->nama ?? '-' }} • {{ $p->model->nama ?? '-' }}</span>
                            </div>
                        </td>

                        <!-- Harga Beli -->
                        <td class="py-3 text-end font-semibold text-muted" style="font-size: 12px;">
                            Rp {{ number_format($p->harga_beli_per_unit, 0, ',', '.') }}
                        </td>

                        <!-- Harga Jual -->
                        <td class="py-3 text-end fw-bold" style="color: #52976D; font-size: 13.5px;">
                            Rp {{ number_format($p->harga_jual, 0, ',', '.') }}
                        </td>

                        <!-- Stok -->
                        <td class="py-3 text-center">
                            @if($p->stok == 0)
                                <span class="badge bg-danger font-semibold px-2.5 py-1" style="font-size: 10.5px;">Habis</span>
                            @elseif($p->stok <= 3)
                                <span class="badge bg-warning text-dark font-semibold px-2.5 py-1" style="font-size: 10.5px;">{{ $p->stok }} pcs</span>
                            @else
                                <span class="badge bg-success font-semibold px-2.5 py-1" style="font-size: 10.5px;">{{ $p->stok }} pcs</span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="py-3 text-center">
                            <span class="badge" style="background: {{ $p->status === 'aktif' ? '#D4EDDA' : '#F5F5F5' }}; color: {{ $p->status === 'aktif' ? '#155724' : '#6C757D' }}; font-size: 10px;">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>

                        <!-- Aksi -->
                        <td class="pe-4 py-3 text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('produk.show', $p->id) }}" class="btn btn-sm btn-light border p-1 px-2" style="border-radius: 8px; font-size: 11.5px;" title="Lihat Detail">
                                    <i class="bi bi-eye text-primary"></i>
                                </a>
                                <a href="{{ route('produk.edit', $p->id) }}" class="btn btn-sm btn-light border p-1 px-2" style="border-radius: 8px; font-size: 11.5px;" title="Edit Produk">
                                    <i class="bi bi-pencil-fill text-warning"></i>
                                </a>
                                <form method="POST" action="{{ route('produk.destroy', $p->id) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border p-1 px-2 text-danger" style="border-radius: 8px; font-size: 11.5px;" title="Hapus Produk">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--border-soft);"></i>
                                <h6 class="fw-bold mb-1" style="color: var(--ink);">Belum ada produk</h6>
                                <small class="text-muted font-semibold">Mulai dengan menambahkan produk baru atau reset filter pencarian</small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Cards (Layar HP) -->
    <div class="d-lg-none p-3">
        @forelse($produk as $p)
        <div class="p-3 mb-3 rounded-3" style="background: #ffffff; border: 1.5px solid var(--border-soft);">
            <div class="d-flex gap-3 mb-2.5">
                <div style="width: 52px; height: 52px; border-radius: 10px; background: var(--pink-soft-2); overflow: hidden; border: 1px solid var(--border-soft);" class="d-flex align-items-center justify-content-center">
                    @if($p->foto && file_exists(storage_path('app/public/' . $p->foto)))
                        <img src="{{ asset('storage/' . $p->foto) }}" alt="{{ $p->nama_produk }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="bi bi-bag-heart text-muted fs-4"></i>
                    @endif
                </div>
                <div style="flex: 1;">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <span class="badge" style="background: var(--pink-soft-2); color: var(--pink-primary-dark); font-family: monospace; font-size: 10.5px;">{{ $p->kode_produk ?? '-' }}</span>
                        <span class="badge" style="background: {{ $p->status === 'aktif' ? '#D4EDDA' : '#F5F5F5' }}; color: {{ $p->status === 'aktif' ? '#155724' : '#6C757D' }}; font-size: 9.5px;">
                            {{ ucfirst($p->status) }}
                        </span>
                    </div>
                    <a href="{{ route('produk.show', $p->id) }}" class="fw-bold text-decoration-none d-block text-truncate" style="color: var(--ink); font-size: 13px;">
                        {{ $p->nama_produk }}
                    </a>
                    <small class="text-muted font-semibold" style="font-size: 11px;">
                        {{ $p->jenisPakaian->nama ?? '-' }} • {{ $p->warna->nama ?? '-' }}
                    </small>
                </div>
            </div>

            <!-- Mini Summary Grid -->
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="p-2 rounded-2" style="background: var(--pink-soft-2); border: 1px solid var(--border-soft);">
                        <small class="text-muted d-block" style="font-size: 10px;">Harga Jual</small>
                        <strong style="color: #52976D; font-size: 12px;">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded-2" style="background: var(--pink-soft-2); border: 1px solid var(--border-soft);">
                        <small class="text-muted d-block" style="font-size: 10px;">Stok Ready</small>
                        <strong style="color: var(--pink-primary-dark); font-size: 12px;">{{ $p->stok }} pcs</strong>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <a href="{{ route('produk.show', $p->id) }}" class="btn btn-sm btn-light border flex-grow-1 font-semibold" style="font-size: 11.5px;">
                    <i class="bi bi-eye me-1"></i> Detail
                </a>
                <a href="{{ route('produk.edit', $p->id) }}" class="btn btn-sm btn-light border flex-grow-1 font-semibold" style="font-size: 11.5px;">
                    <i class="bi bi-pencil-fill me-1"></i> Edit
                </a>
                <form method="POST" action="{{ route('produk.destroy', $p->id) }}" class="d-inline flex-grow-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-light border text-danger w-100 font-semibold" style="font-size: 11.5px;">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted font-semibold">
            <i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--border-soft);"></i>
            Belum ada produk yang tersedia.
        </div>
        @endforelse
    </div>
</div>

<!-- PAGINATION -->
@if($produk->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $produk->links('pagination::bootstrap-4') }}
</div>
@endif

@endsection