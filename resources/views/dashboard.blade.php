@extends('layouts.app')

@section('title', 'Dashboard Yourbaestyle')

@section('content')

<div class="container-fluid py-2" style="max-width: 1400px; margin: 0 auto;">
    
    <!-- 1. TOP STAT CARDS WITH MINI SPARKLINES (SEPERTI ATAS REFERENSI) -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Transaksi Hari Ini -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card h-100 d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block font-semibold mb-1" style="font-size: 11.5px;">Transaksi Hari Ini</small>
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="fw-bold fs-3" style="color: var(--ink);">{{ $transaksiHariIni ?? 0 }}</span>
                        <span class="badge" style="background: #EBF8F1; color: #52976D; font-size: 10px;">+12.5% <i class="bi bi-arrow-up-right"></i></span>
                    </div>
                </div>
                <div class="mini-sparkbar ms-2">
                    <span style="height: 12px; background: #EC95A8;"></span>
                    <span style="height: 20px; background: #EC95A8;"></span>
                    <span style="height: 14px; background: #EC95A8;"></span>
                    <span style="height: 28px; background: #EC95A8;"></span>
                    <span style="height: 22px; background: #EC95A8;"></span>
                    <span style="height: 32px; background: #EC95A8;"></span>
                </div>
            </div>
        </div>

        <!-- Card 2: Penjualan Hari Ini -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card h-100 d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block font-semibold mb-1" style="font-size: 11.5px;">Penjualan Hari Ini</small>
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="fw-bold fs-5" style="color: #52976D;">Rp {{ number_format($penjualanHariIni ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="mini-sparkbar ms-2">
                    <span style="height: 14px; background: #52976D;"></span>
                    <span style="height: 24px; background: #52976D;"></span>
                    <span style="height: 18px; background: #52976D;"></span>
                    <span style="height: 30px; background: #52976D;"></span>
                    <span style="height: 25px; background: #52976D;"></span>
                    <span style="height: 32px; background: #52976D;"></span>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Stok (Klik Mengarah ke Halaman Produk) -->
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="{{ route('produk.index') }}" class="stat-card h-100 d-flex justify-content-between align-items-center text-decoration-none" style="cursor: pointer;" title="Klik untuk lihat katalog produk">
                <div>
                    <small class="text-muted d-block font-semibold mb-1" style="font-size: 11.5px;">Total Stok Aktif <i class="bi bi-arrow-right-short"></i></small>
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="fw-bold fs-3" style="color: var(--ink);">{{ number_format($totalStok ?? 0) }}</span>
                        <span class="badge" style="background: #FFF8E6; color: #A07424; font-size: 10px;">pcs</span>
                    </div>
                </div>
                <div class="mini-sparkbar ms-2">
                    <span style="height: 20px; background: #F0C285;"></span>
                    <span style="height: 16px; background: #F0C285;"></span>
                    <span style="height: 28px; background: #F0C285;"></span>
                    <span style="height: 22px; background: #F0C285;"></span>
                    <span style="height: 30px; background: #F0C285;"></span>
                    <span style="height: 26px; background: #F0C285;"></span>
                </div>
            </a>
        </div>

        <!-- Card 4: Alert Stok Habis (Klik Mengarah ke Halaman Produk Stok Habis) -->
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="{{ route('produk.index', ['stok' => 'habis']) }}" class="stat-card h-100 d-flex justify-content-between align-items-center text-decoration-none" style="cursor: pointer;" title="Klik untuk lihat produk stok habis">
                <div>
                    <small class="text-muted d-block font-semibold mb-1" style="font-size: 11.5px;">Stok Habis (Alert) <i class="bi bi-arrow-right-short"></i></small>
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="fw-bold fs-3" style="color: #DC3545;">{{ $totalHabis ?? 0 }}</span>
                        <span class="badge bg-danger-subtle text-danger" style="font-size: 10px;">Habis</span>
                    </div>
                </div>
                <div class="mini-sparkbar ms-2">
                    <span style="height: 8px; background: #E74C3C;"></span>
                    <span style="height: 14px; background: #E74C3C;"></span>
                    <span style="height: 10px; background: #E74C3C;"></span>
                    <span style="height: 18px; background: #E74C3C;"></span>
                    <span style="height: 12px; background: #E74C3C;"></span>
                    <span style="height: 16px; background: #E74C3C;"></span>
                </div>
            </a>
        </div>
    </div>

    <!-- 2. SALES ANALYTICS SMOOTH AREA CHART & HIGHLIGHT WIDGET -->
    <div class="row g-4 mb-4">
        <!-- GRAFIK UTAMA: SMOOTH SALES ANALYTICS (KIRI) -->
        <div class="col-12 col-lg-8">
            <div class="card-yb p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-1" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                                Sales Analytics
                            </h6>
                            <small class="text-muted font-semibold" style="font-size: 11.5px;">Tren Omset Penjualan POS & Pesanan Online</small>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-light text-muted border px-3 py-1 font-semibold" style="font-size: 11px;">7 Hari Terakhir</span>
                        </div>
                    </div>

                    <!-- Canvas Smooth Line Chart -->
                    <div style="height: 250px; position: relative;" class="my-2">
                        <canvas id="smoothSalesChart"></canvas>
                    </div>
                </div>

                <!-- FOOTER SUMMARY BOXES (INCOME, EXPENSES/HPP, BALANCE) SEPERTI REFERENSI -->
                <div class="row g-2 mt-3 pt-3" style="border-top: 1.5px dashed var(--border-soft);">
                    <div class="col-4">
                        <div class="summary-stat-box">
                            <small class="text-muted d-block font-semibold mb-1" style="font-size: 11px;">Total Penjualan</small>
                            <strong style="color: #52976D; font-size: 14px;">Rp {{ number_format($totalSemuaOmset ?? 0, 0, ',', '.') }}</strong>
                            <span class="badge bg-success-subtle text-success ms-1" style="font-size: 9px;">+10.5%</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="summary-stat-box">
                            <small class="text-muted d-block font-semibold mb-1" style="font-size: 11px;">Total HPP (Modal)</small>
                            <strong style="color: #AD5050; font-size: 14px;">Rp {{ number_format($totalHpp7Hari ?? 0, 0, ',', '.') }}</strong>
                            <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 9px;">-4.2%</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="summary-stat-box">
                            <small class="text-muted d-block font-semibold mb-1" style="font-size: 11px;">Laba Kotor Est.</small>
                            <strong style="color: var(--pink-primary-dark); font-size: 14px;">Rp {{ number_format($labaKotor7Hari ?? 0, 0, ',', '.') }}</strong>
                            <span class="badge bg-success-subtle text-success ms-1" style="font-size: 9px;">+15.8%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FEATURED HIGHLIGHT BANNER & TARGETS (KANAN SEPERTI REFERENSI) -->
        <div class="col-12 col-lg-4">
            <div class="d-flex flex-column gap-3 h-100">
                <!-- Sesi Live Hari Ini (Highlight Banner Theme) -->
                <div class="highlight-card-yb d-flex flex-column justify-content-between p-4" style="flex: 1; min-height: 200px;">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-white" style="font-size: 13.5px; font-family: 'Quicksand', sans-serif;">
                                <i class="bi bi-broadcast me-2 opacity-90"></i>Sesi Live Hari Ini
                            </h6>
                            <a href="{{ route('sesi_live.index') }}" class="text-white-50 text-decoration-none font-semibold text-xs">Kelola Sesi <i class="bi bi-chevron-right"></i></a>
                        </div>

                        @forelse($sesiHariIni ?? [] as $sesi)
                        <div class="p-3 rounded-3 mb-2" style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(4px);">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong class="text-white d-block" style="font-size: 13px;">{{ $sesi->nama_sesi }}</strong>
                                    <small class="text-white-50 d-block" style="font-size: 11px;">Mulai: {{ \Carbon\Carbon::parse($sesi->jam_mulai)->format('H:i') }} WIB</small>
                                </div>
                                <span class="badge bg-white text-dark font-semibold px-2 py-1" style="font-size: 10px;">
                                    {{ ucfirst($sesi->status) }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-xs font-semibold pt-2" style="border-top: 1px dashed rgba(255, 255, 255, 0.35);">
                                <span class="text-white-50">Pesanan: <strong class="text-white">{{ $sesi->total_pesanan }}</strong></span>
                                <span class="text-white-50">Omset: <strong class="text-white">Rp {{ number_format($sesi->total_revenue, 0, ',', '.') }}</strong></span>
                            </div>
                        </div>
                        @empty
                        <div class="p-3 rounded-3 text-center mb-2" style="background: rgba(255, 255, 255, 0.15); border: 1.5px dashed rgba(255, 255, 255, 0.35);">
                            <small class="text-white font-semibold d-block" style="font-size: 11.5px;">
                                <i class="bi bi-calendar-event me-1"></i>Belum ada sesi live terjadwal hari ini.
                            </small>
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-2">
                        <a href="{{ route('sesi_live.index') }}" class="btn btn-light-yb text-decoration-none w-100 py-2.5 font-semibold text-center" style="font-size: 12.5px;">
                            <i class="bi bi-broadcast me-1"></i> Atur Sesi Live
                        </a>
                    </div>
                </div>

                <!-- Kasir POS Store (Standard White Card Theme) -->
                <div class="card-yb p-4 d-flex flex-column justify-content-between" style="flex: 1;">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold mb-0" style="color: var(--ink); font-size: 13.5px; font-family: 'Quicksand', sans-serif;">
                                <i class="bi bi-shop me-2" style="color: var(--pink-primary);"></i>Kasir POS Store
                            </h6>
                            <span class="badge bg-light text-muted font-semibold" style="font-size: 10px;">Kasir Instan</span>
                        </div>
                        <!-- Mini Stat Kasir Hari Ini -->
                        <div class="p-3 rounded-3 mb-3 d-flex justify-content-between align-items-center" style="background: var(--pink-soft-2); border: 1px solid var(--border-soft);">
                            <div>
                                <small class="text-muted d-block font-semibold" style="font-size: 9px;">Omset Kasir Hari Ini</small>
                                <strong style="color: #52976D; font-size: 14px;">Rp {{ number_format($posHariIniOmset ?? 0, 0, ',', '.') }}</strong>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block font-semibold" style="font-size: 9px;">Total Transaksi Hari Ini</small>
                                <strong style="color: var(--pink-primary-dark); font-size: 14px;">{{ $posHariIniCount ?? 0 }} Trx</strong>
                            </div>
                        </div>
                    </div>

                    <div>
                        <a href="{{ route('pos.index') }}" class="btn btn-yb w-100 py-2.5 text-decoration-none font-semibold text-center" style="font-size: 12.5px;">
                            <i class="bi bi-bag-plus me-1"></i> Buka Kasir POS
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. BARIS DOUGHNUT CHART & TABEL TRANSAKSI TERAKHIR -->
    <div class="row g-4 mb-4">
        <!-- CIRCULAR DOUGHNUT CHART (KANA PENJUALAN) -->
        <div class="col-12 col-lg-4">
            <div class="card-yb p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="fw-bold mb-1" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                        Kanal Penjualan
                    </h6>
                    <small class="text-muted font-semibold d-block mb-3" style="font-size: 11.5px;">Distribusi omset per channel</small>

                    <div style="height: 180px; position: relative;" class="my-3">
                        <canvas id="channelDoughnutChart"></canvas>
                    </div>
                </div>

                <div class="row text-center g-2 pt-3" style="border-top: 1.5px dashed var(--border-soft);">
                    <div class="col-4">
                        <small class="text-muted d-block" style="font-size: 10px;">Kasir POS</small>
                        <strong style="color: #52976D; font-size: 12px;">{{ $persenPos }}%</strong>
                    </div>
                    <div class="col-4">
                        <small class="text-muted d-block" style="font-size: 10px;">Shopee</small>
                        <strong style="color: #EE4D2D; font-size: 12px;">35%</strong>
                    </div>
                    <div class="col-4">
                        <small class="text-muted d-block" style="font-size: 10px;">TikTok Live</small>
                        <strong style="color: #EC95A8; font-size: 12px;">25%</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- TRANSAKSI TERAKHIR & PRODUK STOK RENDAH -->
        <div class="col-12 col-lg-8">
            <div class="card-yb p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                        <i class="bi bi-clock-history me-2" style="color: var(--pink-primary);"></i>Transaksi Terakhir
                    </h6>
                    <a href="{{ route('transaksi.index') }}" class="text-decoration-none font-semibold text-xs" style="color: var(--pink-primary-dark);">Lihat Semua <i class="bi bi-arrow-right"></i></a>
                </div>

                <div class="table-responsive">
                    <table class="table table-yb align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Produk</th>
                                <th class="text-center">Qty</th>
                                <th>Total</th>
                                <th class="text-end">Kanal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions ?? [] as $t)
                            <tr>
                                <td class="fw-bold" style="color: var(--ink);">{{ $t->no_transaksi }}</td>
                                <td>{{ $t->produk_nama }}</td>
                                <td class="text-center"><span class="badge bg-light text-dark">{{ $t->qty }}</span></td>
                                <td class="fw-bold" style="color: #52976D;">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <span class="badge" style="background: var(--pink-soft); color: var(--pink-primary-dark); font-size: 10px;">
                                        {{ $t->user_name }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada transaksi recorded hari ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. SMOOTH SALES ANALYTICS AREA CHART (LINE WITH CURVE & GRADIENT)
    const ctxSales = document.getElementById('smoothSalesChart');
    if (ctxSales) {
        const chartCtx = ctxSales.getContext('2d');

        // Gradients
        const gradPos = chartCtx.createLinearGradient(0, 0, 0, 240);
        gradPos.addColorStop(0, 'rgba(82, 151, 109, 0.35)');
        gradPos.addColorStop(1, 'rgba(82, 151, 109, 0.0)');

        const gradOnline = chartCtx.createLinearGradient(0, 0, 0, 240);
        gradOnline.addColorStop(0, 'rgba(236, 149, 168, 0.35)');
        gradOnline.addColorStop(1, 'rgba(236, 149, 168, 0.0)');

        new Chart(ctxSales, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels ?? []) !!},
                datasets: [
                    {
                        label: 'Pesanan Online',
                        data: {!! json_encode($chartDataOnline ?? []) !!},
                        borderColor: '#EC95A8',
                        backgroundColor: gradOnline,
                        borderWidth: 3,
                        tension: 0.45,
                        fill: true,
                        pointBackgroundColor: '#EC95A8',
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Kasir POS',
                        data: {!! json_encode($chartDataPos ?? []) !!},
                        borderColor: '#52976D',
                        backgroundColor: gradPos,
                        borderWidth: 3,
                        tension: 0.45,
                        fill: true,
                        pointBackgroundColor: '#52976D',
                        pointHoverRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { family: 'Nunito', weight: 'bold', size: 12 }, usePointStyle: true }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000) + ' Jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000) + ' Rb';
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. DOUGHNUT CHART KANAL PENJUALAN
    const ctxDoughnut = document.getElementById('channelDoughnutChart');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Kasir POS', 'Shopee', 'TikTok Live'],
                datasets: [{
                    data: [{{ $persenPos ?? 40 }}, 35, 25],
                    backgroundColor: ['#52976D', '#EE4D2D', '#EC95A8'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>
@endpush

@endsection