@extends('layouts.app')

@section('title', 'Manajemen Barang')

@section('content')

<div class="container-fluid" style="max-width: 1400px; margin: 0 auto; padding: 20px;">

    <!-- ========== HEADER ========== -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark" style="font-family: 'Quicksand', sans-serif;">
                <i class="bi bi-box2 me-2" style="color: #EC95A8;"></i>Manajemen Barang
            </h4>
            <small class="text-muted">Kelola semua produk inventory Yourbaestyle</small>
        </div>
        <a href="{{ route('produk.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: linear-gradient(135deg, #EC95A8, #D97B90); border: none;">
            <i class="bi bi-plus-lg me-2"></i>Tambah Produk
        </a>
    </div>

    <!-- ========== FILTER & SEARCH ========== -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('produk.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label fw-bold text-dark mb-2" style="font-size: 13px;">Cari Produk</label>
                    <input type="text" name="search" class="form-control rounded-3" placeholder="Cari nama atau kode produk..." value="{{ request('search', '') }}">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold text-dark mb-2" style="font-size: 13px;">Jenis</label>
                    <select name="jenis" class="form-select rounded-3">
                        <option value="">-- Semua Jenis --</option>
                        <option value="thrift" {{ request('jenis') === 'thrift' ? 'selected' : '' }}>Thrift</option>
                        <option value="rebranding" {{ request('jenis') === 'rebranding' ? 'selected' : '' }}>Rebranding</option>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label fw-bold text-dark mb-2" style="font-size: 13px;">Status</label>
                    <select name="status" class="form-select rounded-3">
                        <option value="">-- Semua Status --</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3" style="background: #EC95A8; border: none;">
                        <i class="bi bi-search"></i> Cari
                    </button>
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
                                <th class="py-3 border-0">Kode</th>
                                <th class="py-3 border-0">Nama Produk</th>
                                <th class="py-3 border-0 text-center">Jenis</th>
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
                                        <img src="{{ asset('storage/' . $p->foto) }}" width="40" height="40" class="rounded-2" style="object-fit: cover; border: 1px solid #E8D9E0;">
                                    @else
                                        <div style="width: 40px; height: 40px; background: #FFF5F7; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 1px solid #E8D9E0;">
                                            <i class="bi bi-image text-muted" style="font-size: 18px;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <code style="font-size: 11px; color: #EC95A8; background: #FFF0F3; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                                        {{ $p->kode_produk }}
                                    </code>
                                </td>
                                <td class="fw-semibold text-dark">{{ substr($p->nama_produk, 0, 35) }}</td>
                                <td class="text-center">
                                    <span class="badge" style="background: {{ $p->jenis === 'thrift' ? '#E8F7EE' : '#FFE6E6' }}; color: {{ $p->jenis === 'thrift' ? '#52976D' : '#DC3545' }}; font-size: 10px;">
                                        {{ ucfirst($p->jenis) }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold" style="color: #4D3D43;">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <small style="color: #8C7B80;">Rp {{ number_format($p->hpp_otomatis, 0, ',', '.') }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge" style="background: {{ $p->stok > 5 ? '#D4EDDA' : ($p->stok > 0 ? '#FFF9E6' : '#FFE6E6') }}; color: {{ $p->stok > 5 ? '#155724' : ($p->stok > 0 ? '#856404' : '#DC3545') }};">
                                        {{ $p->stok }} pcs
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge" style="background: {{ $p->status === 'aktif' ? '#D4EDDA' : '#F5F5F5' }}; color: {{ $p->status === 'aktif' ? '#155724' : '#6C757D' }}; font-size: 10px;">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>
                                <td class="pe-4">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('produk.show', $p->id) }}" class="btn btn-outline-primary rounded-2" style="font-size: 11px;" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('produk.edit', $p->id) }}" class="btn btn-outline-primary rounded-2" style="font-size: 11px;" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form method="POST" action="{{ route('produk.destroy', $p->id) }}" style="display: inline;" onsubmit="return confirm('Yakin ingin nonaktifkan produk ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger rounded-2" style="font-size: 11px;" title="Nonaktifkan">
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
                                        <small class="text-muted">Mulai dengan menambahkan produk baru</small>
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
                <div class="card shadow-sm border-0 mb-3 rounded-3" style="background: #fff; overflow: hidden;">
                    <div class="card-body p-3">
                        <!-- Header: Foto + Nama + Jenis -->
                        <div class="d-flex gap-3 mb-3">
                            <div>
                                @if($p->foto && file_exists(storage_path('app/public/' . $p->foto)))
                                    <img src="{{ asset('storage/' . $p->foto) }}" width="50" height="50" class="rounded-2" style="object-fit: cover;">
                                @else
                                    <div style="width: 50px; height: 50px; background: #FFF5F7; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-image text-muted" style="font-size: 20px;"></i>
                                    </div>
                                @endif
                            </div>
                            <div style="flex: 1;">
                                <code style="font-size: 10px; color: #EC95A8; background: #FFF0F3; padding: 2px 6px; border-radius: 4px;">{{ $p->kode_produk }}</code>
                                <h6 class="mb-1 fw-bold text-dark" style="font-size: 13px;">{{ substr($p->nama_produk, 0, 25) }}</h6>
                                <small class="text-muted">{{ ucfirst($p->jenis) }}</small>
                            </div>
                            <span class="badge" style="background: {{ $p->status === 'aktif' ? '#D4EDDA' : '#F5F5F5' }}; color: {{ $p->status === 'aktif' ? '#155724' : '#6C757D' }}; height: fit-content;">
                                {{ ucfirst($p->status) }}
                            </span>
                        </div>

                        <!-- Stats Grid -->
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="p-2 rounded-2 text-center" style="background: #FFF5F7;">
                                    <small class="text-muted d-block" style="font-size: 10px;">Harga</small>
                                    <strong style="color: #EC95A8; font-size: 11px;">{{ number_format($p->harga_jual / 1000, 0) }}K</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded-2 text-center" style="background: #E8F7EE;">
                                    <small class="text-muted d-block" style="font-size: 10px;">Stok</small>
                                    <strong style="color: #52976D; font-size: 11px;">{{ $p->stok }} pcs</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded-2 text-center" style="background: #E8F4F8;">
                                    <small class="text-muted d-block" style="font-size: 10px;">HPP</small>
                                    <strong style="color: #0277BD; font-size: 10px;">{{ number_format($p->hpp_otomatis / 1000, 0) }}K</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <a href="{{ route('produk.show', $p->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1 rounded-2" style="font-size: 11px;">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('produk.edit', $p->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1 rounded-2" style="font-size: 11px;">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <form method="POST" action="{{ route('produk.destroy', $p->id) }}" style="display: inline;" onsubmit="return confirm('Yakin?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger flex-grow-1 rounded-2" style="font-size: 11px;">
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