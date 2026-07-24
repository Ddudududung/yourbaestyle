@extends('layouts.app')

@section('title', 'Review & Pemetaan Kode Live — Yourbaestyle')

@section('content')
<div class="container-fluid py-3" style="max-width: 1250px; margin: 0 auto;">
    
    <!-- 1. HEADER HALAMAN -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <a href="{{ route('pesanan.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold mb-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat Pesanan
            </a>
            <h4 class="mb-1 fw-bold text-dark" style="font-family: 'Quicksand', sans-serif;">
                <i class="bi bi-clipboard-check-fill me-2" style="color: #EC95A8;"></i>Review & Pemetaan Kode Live Manual
            </h4>
            <p class="text-muted mb-0 small">
                Daftar pesanan tertahan (<strong class="text-warning-emphasis">Hold Review</strong>) karena kode variasi dari Excel belum terdaftar di sistem.
            </p>
        </div>
        <div>
            <span class="badge rounded-pill px-3 py-2 fs-6 border" style="background: #FFF0F3; color: #EC95A8; border-color: #F7E5EA !important;">
                <i class="bi bi-exclamation-circle-fill me-1"></i> Antrian Tertahan: <strong>{{ $reviews->total() }} Item</strong>
            </span>
        </div>
    </div>

    <!-- 2. PESAN NOTIFIKASI SUKSES / ERROR -->
    @if(session('success'))
    <div class="alert rounded-4 mb-4 p-3 border-0 shadow-sm d-flex align-items-center gap-2" style="background: #E8F7EE; color: #1E4620;">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="alert rounded-4 mb-4 p-3 border-0 shadow-sm d-flex align-items-center gap-2" style="background: #FDF2F2; color: #9B1C1C;">
        <i class="bi bi-x-circle-fill fs-5 text-danger"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="alert rounded-4 mb-4 p-3 border-0 shadow-sm" style="background: #FDF2F2; color: #9B1C1C;">
        <strong class="d-block mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Terjadi Kesalahan:</strong>
        <ul class="mb-0 ps-3 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- 3. TABEL ANTRIAN REVIEW -->
    <div class="card border-0 shadow-sm rounded-4" style="background: #fff; overflow: hidden;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center" style="border-color: #F7E5EA !important;">
            <h6 class="mb-0 fw-bold" style="color: #4D3D43;">
                <i class="bi bi-list-task me-2" style="color: #EC95A8;"></i>Daftar Antrian Pemetaan
            </h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                    <thead style="background-color: #FFF5F7; color: #7A686D; font-size: 11.5px; text-transform: uppercase;">
                        <tr>
                            <th class="py-3 ps-4 border-0" style="width: 15%;">No. Order & Platform</th>
                            <th class="py-3 border-0" style="width: 15%;">Pembeli</th>
                            <th class="py-3 border-0" style="width: 15%;">Kode Variasi Excel</th>
                            <th class="py-3 text-center border-0" style="width: 8%;">Qty</th>
                            <th class="py-3 pe-4 border-0" style="width: 47%;">Pascangkan ke Produk Fisik (Gudang)</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: 1px solid #F7E5EA;">
                        @forelse($reviews as $review)
                        <tr>
                            <!-- Kolom 1: No Order & Platform -->
                            <td class="ps-4">
                                <code class="fw-bold d-block mb-1" style="color: #EC95A8; background: #FFF0F3; padding: 4px 8px; border-radius: 6px; width: fit-content;">{{ $review->no_pesanan }}</code>
                                <span class="badge bg-light text-secondary border" style="font-size: 10px;">
                                    <i class="bi bi-shop me-1"></i>{{ strtoupper($review->platform) }}
                                </span>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                    Live: {{ \Carbon\Carbon::parse($review->tanggal_live)->format('d M Y') }}
                                </small>
                            </td>

                            <!-- Kolom 2: Pembeli -->
                            <td class="fw-bold text-dark">{{ $review->nama_pembeli ?: '-' }}</td>

                            <!-- Kolom 3: Kode Variasi Raw -->
                            <td>
                                <span class="badge px-3 py-2 fs-6 border border-danger-subtle text-danger" style="background: #FFF5F7; font-family: monospace;">
                                    {{ $review->kode_live_raw }}
                                </span>
                            </td>

                            <!-- Kolom 4: Qty -->
                            <td class="text-center">
                                <span class="fw-bold fs-6" style="color: #EC95A8;">{{ $review->qty_order }} <small class="text-muted fw-normal">pcs</small></span>
                            </td>

                            <!-- Kolom 5: Form Resolusi (Map / Skip / Reject) -->
                            <td class="pe-4 py-3">
                                <form action="{{ route('pesanan.resolve_manual', $review->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PATCH')
                                    
                                    <div class="row g-2 align-items-center">
                                        <!-- Dropdown Produk -->
                                        <div class="col-12 col-md-7">
                                            <select name="id_produk_mapping" class="form-select form-select-sm rounded-3" style="border-color: #F7E5EA;">
                                                <option value="">-- Pilih Produk Fisik yang Sesuai --</option>
                                                @foreach($produks as $prod)
                                                    <option value="{{ $prod->id }}" {{ $prod->stok < $review->qty_order ? 'disabled' : '' }}>
                                                        {{ $prod->nama_produk }} — (Stok: {{ $prod->stok }}) 
                                                        {{ $prod->stok < $review->qty_order ? '❌ STOK KURANG' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Tombol Aksi -->
                                        <div class="col-12 col-md-5 d-flex gap-1">
                                            <!-- Tombol Map -->
                                            <button type="submit" name="action" value="mapped" class="btn btn-sm text-white fw-bold px-3 rounded-3 grow shadow-sm" style="background: #EC95A8;" title="Simpan & Potong Stok">
                                                <i class="bi bi-check-lg me-1"></i> Map
                                            </button>

                                            <!-- Tombol Skip -->
                                            <button type="submit" name="action" value="skip" class="btn btn-sm btn-outline-secondary rounded-3 px-2" title="Lewati Sementara">
                                                <i class="bi bi-skip-forward"></i>
                                            </button>

                                            <!-- Tombol Reject -->
                                            <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-danger rounded-3 px-2" onclick="return confirm('Yakin ingin menolak dan membatalkan pesanan ini?')" title="Batalkan Pesanan">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                         <i class="text-dark">Tips:</i> Setelah dimap, sistem otomatis mencatat kode <strong>{{ $review->kode_live_raw }}</strong> ke kamus agar live berikutnya langsung otomatis!
                                    </small>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-3">
                                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                    <h6 class="fw-bold mt-2 text-dark">All Clean! Tidak Ada Antrian Review</h6>
                                    <p class="text-muted small mb-0">Semua kode variasi pesanan online sudah berhasil dipetakan ke produk fisik.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINASI -->
            @if($reviews->hasPages())
            <div class="p-3 border-top d-flex justify-content-end" style="background: #FAF8F9; border-color: #F7E5EA !important;">
                {{ $reviews->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection