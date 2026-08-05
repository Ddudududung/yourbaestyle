@extends('layouts.app')

@section('title', 'Manajemen Produk')

@section('content')

<div class="container-fluid" style="max-width: 1400px; margin: 0 auto; padding: 20px;">

    <!-- ========== HEADER ========== -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark" style="font-family: 'Quicksand', sans-serif;">
                <i class="bi bi-box2 me-2" style="color: #EC95A8;"></i>Manajemen Produk
            </h4>
            <small class="text-muted">Kelola semua produk inventory Yourbaestyle</small>
        </div>
        <a href="{{ route('produk.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: linear-gradient(135deg, #EC95A8, #D97B90); border: none;">
            <i class="bi bi-plus-lg me-2"></i>Tambah Produk
        </a>
    </div>

    <!-- ========== FILTER & SEARCH (UPDATED DENGAN MASTER DATA) ========== -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('produk.index') }}" class="row g-2 align-items-end">
                
                <!-- Cari Produk -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Cari Produk</label>
                    <input type="text" name="search" class="form-control rounded-3" placeholder="Nama / kode produk..." value="{{ request('search', '') }}">
                </div>

                <!-- Filter Jenis Pakaian -->
                <div class="col-6 col-md-2">
                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Jenis Pakaian</label>
                    <select name="id_jenis_pakaian" class="form-select rounded-3">
                        <option value="">-- Semua Jenis --</option>
                        @foreach($jenisPakaian as $jp)
                            <option value="{{ $jp->id }}" {{ request('id_jenis_pakaian') == $jp->id ? 'selected' : '' }}>
                                {{ $jp->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Warna -->
                <div class="col-6 col-md-2">
                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Warna</label>
                    <select name="id_warna" class="form-select rounded-3">
                        <option value="">-- Semua Warna --</option>
                        @foreach($warna as $w)
                            <option value="{{ $w->id }}" {{ request('id_warna') == $w->id ? 'selected' : '' }}>
                                {{ $w->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Model -->
                <div class="col-6 col-md-2">
                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Model</label>
                    <select name="id_model" class="form-select rounded-3">
                        <option value="">-- Semua Model --</option>
                        @foreach($model as $m)
                            <option value="{{ $m->id }}" {{ request('id_model') == $m->id ? 'selected' : '' }}>
                                {{ $m->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="col-6 col-md-2">
                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Status</label>
                    <select name="status" class="form-select rounded-3">
                        <option value="">-- Semua Status --</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <!-- Tombol Cari & Reset -->
                <div class="col-12 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold" style="background: #EC95A8; border: none;" title="Cari">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request()->anyFilled(['search', 'id_jenis_pakaian', 'id_warna', 'id_model', 'status']))
                        <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary rounded-3" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>

            </form>
        </div>
    </div>

    <!-- ========== PRODUK TABLE ========== -->
    <div class="card shadow-sm border-0" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header bg-white" style="padding: 20px 24px; border-bottom: 1px solid #F7E5EA;">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark" style="font-size: 15px;">
                    <i class="bi bi-table me-2" style="color: #EC95A8;"></i>Daftar Produk
                </h6>
                <small class="text-muted">Total: {{ $produk->total() }} produk</small>
            </div>
        </div>

        <div class="card-body p-0">
            <!-- Desktop Table -->
            <div class="d-none d-lg-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                        <thead style="background-color: #FFF5F7; color: #7A686D; font-size: 11.5px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                            <tr>
                                <th class="py-3 ps-4 border-0" width="50">Foto</th>
                                <th class="py-3 border-0">Kode SKU</th>
                                <th class="py-3 border-0">Nama Produk</th>
                                <th class="py-3 border-0 text-center">Spesifikasi (Jenis / Warna / Model)</th>
                                <th class="py-3 border-0 text-end">Harga Jual</th>
                                <th class="py-3 border-0 text-center">HPP Otomatis</th>
                                <th class="py-3 border-0 text-center">Stok</th>
                                <th class="py-3 border-0 text-center">Status</th>
                                <th class="py-3 pe-4 border-0 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody style="border-top: 1px solid #F7E5EA;">
                            @forelse($produk as $p)
                            <tr style="transition: all 0.2s ease;">
                                <td class="ps-4">
                                    @if($p->foto && file_exists(storage_path('app/public/' . $p->foto)))
                                        <img src="{{ asset('storage/' . $p->foto) }}" width="45" height="45" class="rounded-2 shadow-sm" style="object-fit: cover; border: 1px solid #E8D9E0;">
                                    @else
                                        <div style="width: 45px; height: 45px; background: #FFF5F7; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 1px solid #E8D9E0;">
                                            <i class="bi bi-image text-muted" style="font-size: 18px;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <code style="font-size: 11.5px; color: #EC95A8; background: #FFF0F3; padding: 5px 8px; border-radius: 6px; font-weight: 700;">
                                        {{ $p->kode_produk ?? '-' }}
                                    </code>
                                </td>
                                <td class="fw-semibold text-dark">{{ substr($p->nama_produk, 0, 35) }}</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-1 mb-1" style="background: #FFF5F7; color: #EC95A8; border: 1px solid #F7E5EA; font-size: 11px;">
                                        {{ strtoupper($p->jenisPakaian->nama ?? '-') }}
                                    </span>
                                    <div class="text-muted" style="font-size: 11px;">
                                        {{ $p->warna->nama ?? '-' }} • {{ $p->model->nama ?? '-' }}
                                    </div>
                                </td>
                                <td class="text-end fw-bold" style="color: #4D3D43;">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <small style="color: #8C7B80; font-weight: 600;">Rp {{ number_format($p->hpp_otomatis, 0, ',', '.') }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge px-2.5 py-1" style="background: {{ $p->stok > 5 ? '#D4EDDA' : ($p->stok > 0 ? '#FFF9E6' : '#FFE6E6') }}; color: {{ $p->stok > 5 ? '#155724' : ($p->stok > 0 ? '#856404' : '#DC3545') }}; font-size: 11px;">
                                        {{ $p->stok }} pcs
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge" style="background: {{ $p->status === 'aktif' ? '#D4EDDA' : '#F5F5F5' }}; color: {{ $p->status === 'aktif' ? '#155724' : '#6C757D' }}; font-size: 10px;">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('produk.show', $p->id) }}" class="btn btn-outline-primary rounded-2" style="font-size: 11px;" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('produk.edit', $p->id) }}" class="btn btn-outline-primary rounded-2 ms-1" style="font-size: 11px;" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form method="POST" action="{{ route('produk.destroy', $p->id) }}" style="display: inline;" onsubmit="return confirm('Yakin ingin nonaktifkan produk ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger rounded-2 ms-1" style="font-size: 11px;" title="Nonaktifkan">
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
                                        <div style="font-size: 3rem; color: #F6C9D3; margin-bottom: 10px;">
                                            <i class="bi bi-inbox"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1 text-dark">Belum ada produk</h6>
                                        <small class="text-muted">Mulai dengan menambahkan produk baru atau reset filter pencarian</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Cards -->
            <div class="d-lg-none p-3">
                @forelse($produk as $p)
                <div class="card shadow-sm border-0 mb-3 rounded-3" style="background: #fff; overflow: hidden; border: 1px solid #F7E5EA !important;">
                    <div class="card-body p-3">
                        <!-- Header: Foto + Nama + SKU -->
                        <div class="d-flex gap-3 mb-3">
                            <div>
                                @if($p->foto && file_exists(storage_path('app/public/' . $p->foto)))
                                    <img src="{{ asset('storage/' . $p->foto) }}" width="55" height="55" class="rounded-2 shadow-sm" style="object-fit: cover;">
                                @else
                                    <div style="width: 55px; height: 55px; background: #FFF5F7; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 1px solid #E8D9E0;">
                                        <i class="bi bi-image text-muted" style="font-size: 22px;"></i>
                                    </div>
                                @endif
                            </div>
                            <div style="flex: 1;">
                                <code style="font-size: 10.5px; color: #EC95A8; background: #FFF0F3; padding: 2px 6px; border-radius: 4px; font-weight: 700;">{{ $p->kode_produk ?? '-' }}</code>
                                <h6 class="mb-1 mt-1 fw-bold text-dark" style="font-size: 13.5px;">{{ substr($p->nama_produk, 0, 30) }}</h6>
                                <small class="text-muted" style="font-size: 11.5px;">
                                    <strong>{{ $p->jenisPakaian->nama ?? '-' }}</strong> • {{ $p->warna->nama ?? '-' }} • {{ $p->model->nama ?? '-' }}
                                </small>
                            </div>
                            <span class="badge" style="background: {{ $p->status === 'aktif' ? '#D4EDDA' : '#F5F5F5' }}; color: {{ $p->status === 'aktif' ? '#155724' : '#6C757D' }}; height: fit-content; font-size: 10px;">
                                {{ ucfirst($p->status) }}
                            </span>
                        </div>

                        <!-- Stats Grid -->
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="p-2 rounded-2 text-center" style="background: #FFF5F7; border: 1px solid #F7E5EA;">
                                    <small class="text-muted d-block" style="font-size: 10px;">Harga Jual</small>
                                    <strong style="color: #EC95A8; font-size: 11.5px;">{{ number_format($p->harga_jual / 1000, 0) }}K</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded-2 text-center" style="background: #E8F7EE;">
                                    <small class="text-muted d-block" style="font-size: 10px;">Stok Ready</small>
                                    <strong style="color: #52976D; font-size: 11.5px;">{{ $p->stok }} pcs</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded-2 text-center" style="background: #E8F4F8;">
                                    <small class="text-muted d-block" style="font-size: 10px;">HPP Oto</small>
                                    <strong style="color: #0277BD; font-size: 11px;">{{ number_format($p->hpp_otomatis / 1000, 0) }}K</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <a href="{{ route('produk.show', $p->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1 rounded-2 fw-bold" style="font-size: 11px;">
                                <i class="bi bi-eye me-1"></i> Detail
                            </a>
                            <a href="{{ route('produk.edit', $p->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1 rounded-2 fw-bold" style="font-size: 11px;">
                                <i class="bi bi-pencil-fill me-1"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('produk.destroy', $p->id) }}" style="display: inline; flex-grow: 1;" onsubmit="return confirm('Yakin ingin nonaktifkan produk ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-2 fw-bold" style="font-size: 11px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="card border-0 text-center py-5">
                    <div style="font-size: 2.5rem; color: #F6C9D3; margin-bottom: 10px;">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Belum ada produk</h6>
                    <small class="text-muted">Mulai dengan menambahkan produk baru</small>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ========== PAGINATION ========== -->
    @if($produk->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $produk->links('pagination::bootstrap-4') }}
    </div>
    @endif

</div>

<style>
    .form-control, .form-select {
        border-color: #E8D9E0;
        font-size: 13px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #EC95A8;
        box-shadow: 0 0 0 0.2rem rgba(236, 149, 168, 0.25);
    }
    .table tbody tr {
        border-bottom: 1px solid #F7E5EA;
    }
    .table tbody tr:hover {
        background-color: #FFF5F7;
    }
    .btn-outline-primary {
        color: #EC95A8;
        border-color: #EC95A8;
    }
    .btn-outline-primary:hover {
        background-color: #EC95A8;
        border-color: #EC95A8;
        color: #fff;
    }
    .badge {
        font-weight: 600;
        letter-spacing: 0.3px;
    }
</style>

@endsection