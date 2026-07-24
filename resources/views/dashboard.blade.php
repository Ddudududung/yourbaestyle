@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="container-fluid" style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card shadow-sm border-0 h-100" style="border-radius: 16px; background: linear-gradient(135deg, #FFF5F7, #fff); overflow: hidden; border-left: 4px solid #EC95A8;">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div style="font-size: 32px; color: #EC95A8;"><i class="bi bi-receipt"></i></div>
                        <span class="badge bg-light text-muted" style="font-size: 10px;">Hari Ini</span>
                    </div>
                    <small style="color: #8C7B80; font-size: 12px;">Transaksi Hari Ini</small>
                    <div class="stat-value fw-bold mt-2" style="font-size: 32px; color: #4D3D43;">{{ $transaksiHariIni ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card shadow-sm border-0 h-100" style="border-radius: 16px; background: linear-gradient(135deg, #E8F7EE, #fff); overflow: hidden; border-left: 4px solid #52976D;">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div style="font-size: 32px; color: #52976D;"><i class="bi bi-cash-coin"></i></div>
                        <span class="badge bg-light text-muted" style="font-size: 10px;">Revenue</span>
                    </div>
                    <small style="color: #8C7B80; font-size: 12px;">Penjualan Hari Ini</small>
                    <div class="stat-value fw-bold mt-2" style="font-size: 24px; color: #52976D;">Rp {{ number_format($penjualanHariIni ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card shadow-sm border-0 h-100" style="border-radius: 16px; background: linear-gradient(135deg, #FFF9E6, #fff); overflow: hidden; border-left: 4px solid #F39C12;">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div style="font-size: 32px; color: #F39C12;"><i class="bi bi-box2"></i></div>
                        <span class="badge bg-light text-muted" style="font-size: 10px;">Aktif</span>
                    </div>
                    <small style="color: #8C7B80; font-size: 12px;">Total Stok</small>
                    <div class="stat-value fw-bold mt-2" style="font-size: 32px; color: #4D3D43;">{{ $totalStok ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card shadow-sm border-0 h-100" style="border-radius: 16px; background: linear-gradient(135deg, #FFE6E6, #fff); overflow: hidden; border-left: 4px solid #DC3545;">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div style="font-size: 32px; color: #DC3545;"><i class="bi bi-exclamation-triangle"></i></div>
                        <span class="badge bg-light text-muted" style="font-size: 10px;">Alert</span>
                    </div>
                    <small style="color: #8C7B80; font-size: 12px;">Stok Habis</small>
                    <div class="stat-value fw-bold mt-2" style="font-size: 32px; color: #DC3545;">{{ $totalHabis ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($sesiHariIni && count($sesiHariIni) > 0)
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="mb-0 fw-bold" style="color: #4D3D43; font-family: 'Quicksand', sans-serif; font-size: 16px;">
                <i class="bi bi-broadcast me-2" style="color: #EC95A8;"></i>Breakdown Sesi Live Hari Ini
            </h6>
        </div>

        <div class="row g-3">
            @foreach($sesiHariIni as $sesi)
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100" style="background: #fff; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="mb-0 fw-bold" style="color: #4D3D43;">{{ $sesi->nama_sesi }}</h6>
                                <small class="text-muted" style="font-size: 11px;">{{ $sesi->jam_mulai }}</small>
                            </div>
                            <span class="badge" style="font-size: 10px; background: {{ $sesi->status === 'ongoing' ? '#D4EDDA' : ($sesi->status === 'completed' ? '#D1ECF1' : '#F8F9FA') }}; color: {{ $sesi->status === 'ongoing' ? '#155724' : ($sesi->status === 'completed' ? '#0C5460' : '#6C757D') }};">
                                {{ ucfirst($sesi->status) }}
                            </span>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="p-2 rounded-2" style="background: #FFF5F7; text-align: center;">
                                    <small class="text-muted d-block" style="font-size: 10px;">Pesanan</small>
                                    <strong style="color: #EC95A8; font-size: 18px;">{{ $sesi->total_pesanan }}</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded-2" style="background: #E8F7EE; text-align: center;">
                                    <small class="text-muted d-block" style="font-size: 10px;">Revenue</small>
                                    <strong style="color: #52976D; font-size: 14px;">{{ number_format($sesi->total_revenue, 0) }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="p-2 rounded-2" style="background: #E8F4F8; text-align: center;">
                            <small class="text-muted d-block" style="font-size: 10px;">Profit</small>
                            <strong style="color: #0277BD; font-size: 16px;">Rp {{ number_format($sesi->total_profit, 0) }}</strong>
                        </div>

                        <a href="#" class="btn btn-sm btn-outline-primary w-100 mt-3 rounded-2" style="font-size: 11px;">
                            Lihat Detail
                        </a>
                        <div class="d-flex gap-2 mt-3">
    <a href="{{ url('/sesi-live?filter_sesi=' . $sesi->id) }}" class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1 fw-bold">
        <i class="bi bi-eye me-1"></i> Lihat Detail
    </a>

    <!-- Tombol Hapus Sesi (Jika salah bikin / ga jadi) -->
    <form action="{{ route('sesi_live.destroy_jadwal', $sesi->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus sesi [{{ $sesi->nama_sesi }}]?\n\nJika dihapus, sesi ini akan hilang dari Dashboard dan Dropdown Co-Host.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" title="Hapus jika salah bikin">
            <i class="bi bi-trash me-1"></i> Hapus
        </button>
    </form>
</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="alert rounded-3 mb-4" style="background: #E8F4F8; border: 1px solid #B3E5FC; color: #0277BD;">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Belum ada sesi live hari ini.</strong> Buat sesi live untuk memulai tracking pesanan per sesi.
    </div>
    @endif

    <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header bg-white" style="padding: 20px 24px; border-bottom: 1px solid #F7E5EA;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0 fw-bold" style="color: #4D3D43; font-family: 'Quicksand', sans-serif; font-size: 15px;">
                    <i class="bi bi-receipt me-2" style="color: #EC95A8;"></i>Transaksi Terakhir
                </h6>
                <a href="#" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 12px;">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                        <thead style="background-color: #FFF5F7; color: #7A686D; font-size: 11.5px; text-transform: uppercase; font-weight: 700;">
                            <tr>
                                <th class="py-3 ps-4 border-0">No. Transaksi</th>
                                <th class="py-3 border-0">Produk</th>
                                <th class="py-3 text-center border-0">Qty</th>
                                <th class="py-3 text-end border-0">Total</th>
                                <th class="py-3 text-end border-0">Waktu</th>
                                <th class="py-3 pe-4 text-end border-0">User</th>
                            </tr>
                        </thead>
                        <tbody style="border-top: 1px solid #F7E5EA;">
                            @forelse($recentTransactions ?? [] as $tx)
                            <tr style="transition: all 0.2s ease;">
                                <td class="ps-4">
                                    <code style="font-size: 11.5px; color: #EC95A8; font-weight: 700; background: #FFF0F3; padding: 4px 8px; border-radius: 6px;">
                                        {{ $tx->no_transaksi ?? '-' }}
                                    </code>
                                </td>
                                <td class="fw-semibold text-dark">{{ substr($tx->produk_nama ?? '-', 0, 30) }}</td>
                                <td class="text-center fw-bold">{{ $tx->qty ?? 0 }}</td>
                                <td class="text-end fw-bold" style="color: #4D3D43;">Rp {{ number_format($tx->total ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end text-muted"><small>{{ $tx->created_at?->diffForHumans() ?? '-' }}</small></td>
                                <td class="pe-4 text-end">
                                    <span class="badge" style="background: {{ $tx->user_name === 'SHOPEE' || $tx->user_name === 'TIKTOK' ? '#FFE6E6' : '#E8F7EE' }}; color: {{ $tx->user_name === 'SHOPEE' || $tx->user_name === 'TIKTOK' ? '#DC3545' : '#52976D' }}; font-size: 10px;">
                                        {{ $tx->user_name ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="py-4">
                                        <div style="font-size: 3rem; color: #F6C9D3; margin-bottom: 10px;">
                                            <i class="bi bi-inbox"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="color: #4D3D43;">Belum ada transaksi</h6>
                                        <small class="text-muted">Data transaksi akan muncul di sini</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-md-none p-3">
                @forelse($recentTransactions ?? [] as $tx)
                <div style="padding: 12px 0; border-bottom: 1px solid #F7E5EA;">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div style="flex: 1;">
                            <code style="font-size: 11px; color: #EC95A8; background: #FFF0F3; padding: 3px 6px; border-radius: 4px;">{{ $tx->no_transaksi ?? '-' }}</code>
                            <div class="fw-bold text-dark mt-1" style="font-size: 13px;">{{ substr($tx->produk_nama ?? '-', 0, 20) }}</div>
                            <small class="text-muted d-block mt-1">{{ $tx->qty ?? 0 }} pcs • {{ $tx->created_at?->diffForHumans() ?? '-' }}</small>
                        </div>
                        <div style="text-align: right;">
                            <div style="color: #EC95A8; font-weight: 700;">Rp {{ number_format($tx->total ?? 0, 0) }}</div>
                            <span class="badge mt-1" style="background: #E8F7EE; color: #52976D; font-size: 9px;">{{ $tx->user_name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <div style="font-size: 2.5rem; color: #F6C9D3; margin-bottom: 10px;">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h6 class="fw-bold" style="color: #4D3D43; font-size: 14px;">Belum ada transaksi</h6>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header bg-white" style="padding: 20px 24px; border-bottom: 1px solid #F7E5EA;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0 fw-bold" style="color: #4D3D43; font-family: 'Quicksand', sans-serif; font-size: 15px;">
                    <i class="bi bi-exclamation-triangle-fill me-2" style="color: #F39C12;"></i>Produk Stok Rendah
                </h6>
                <a href="#" class="btn btn-sm btn-outline-warning rounded-pill px-3" style="font-size: 12px;">
                    Kelola Stok <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                        <thead style="background-color: #FFF9E6; color: #7A686D; font-size: 11.5px; text-transform: uppercase; font-weight: 700;">
                            <tr>
                                <th class="py-3 ps-4 border-0">Produk</th>
                                <th class="py-3 border-0 text-center">Stok Saat Ini</th>
                                <th class="py-3 border-0 text-center">Stok Minimum</th>
                                <th class="py-3 border-0">Status</th>
                                <th class="py-3 pe-4 border-0 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody style="border-top: 1px solid #F7E5EA;">
                            @forelse($lowStockProducts ?? [] as $prod)
                            <tr>
                                <td class="ps-4 fw-semibold" style="color: #4D3D43;">{{ substr($prod->nama_produk, 0, 40) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-danger text-white">{{ $prod->stok }} pcs</span>
                                </td>
                                <td class="text-center text-muted">5 pcs</td>
                                <td>
                                    @if($prod->stok === 0)
                                        <span class="badge bg-danger">Habis</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Rendah</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('produk.edit', $prod->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2" style="font-size: 11px;">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="py-4">
                                        <div style="font-size: 3rem; color: #D4EDDA; margin-bottom: 10px;">
                                            <i class="bi bi-check-circle"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="color: #4D3D43;">Semua stok OK</h6>
                                        <small class="text-muted">Tidak ada produk dengan stok rendah</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-md-none p-3">
                @forelse($lowStockProducts ?? [] as $prod)
                <div style="padding: 12px 0; border-bottom: 1px solid #F7E5EA;">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div style="flex: 1;">
                            <div class="fw-bold text-dark" style="font-size: 13px;">{{ substr($prod->nama_produk, 0, 25) }}</div>
                            <small class="text-muted d-block mt-1">Stok: {{ $prod->stok }} pcs (minimum: 5 pcs)</small>
                        </div>
                        <div style="text-align: right;">
                            <span class="badge" style="background: {{ $prod->stok === 0 ? '#DC3545' : '#F39C12' }}; color: #fff; font-size: 10px;">
                                {{ $prod->stok === 0 ? 'Habis' : 'Rendah' }}
                            </span>
                            <a href="{{ route('produk.edit', $prod->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 mt-1" style="font-size: 10px;">
                                Edit
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <div style="font-size: 2.5rem; color: #D4EDDA; margin-bottom: 10px;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h6 class="fw-bold" style="color: #4D3D43; font-size: 14px;">Semua stok OK</h6>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(236, 149, 168, 0.1) !important;
    }
    .table tbody tr {
        border-bottom: 1px solid #F7E5EA;
    }
    .table tbody tr:hover {
        background-color: #FFF5F7;
    }
    .badge {
        font-weight: 600;
        letter-spacing: 0.3px;
    }
</style>

@endsection