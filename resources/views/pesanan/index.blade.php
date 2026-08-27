@extends('layouts.app')

@section('title', 'Riwayat Pesanan Online')

@section('content')

<div class="card-yb p-4 mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <form method="GET" class="row g-2 grow">
            <div class="col-md-3">
                <select name="platform" class="form-select font-semibold">
                    <option value="">Semua Platform</option>
                    <option value="shopee" {{ request('platform')=='shopee'?'selected':'' }}>Shopee</option>
                    <option value="tiktok" {{ request('platform')=='tiktok'?'selected':'' }}>TikTok</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select font-semibold">
                    <option value="">Semua Status</option>
                    <option value="diproses" {{ request('status')=='diproses'?'selected':'' }}>⏳ Diproses</option>
                    <option value="dikirim" {{ request('status')=='dikirim'?'selected':'' }}>🚚 Dikirim</option>
                    <option value="selesai" {{ request('status')=='selesai'?'selected':'' }}>✅ Selesai</option>
                    <option value="cancel" {{ request('status')=='cancel'?'selected':'' }}>❌ Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control font-semibold" placeholder="Cari no. pesanan / pembeli..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-yb w-100 font-semibold">Filter</button>
            </div>
        </form>
        <a href="{{ route('pesanan.import') }}" class="btn btn-yb ms-2 font-semibold"><i class="bi bi-cloud-upload"></i> Import File</a>
    </div>
</div>

<!-- BAR AKSI MASSAL (BULK ACTION BAR) -->
<form id="bulk-form" action="{{ route('pesanan.bulk_update_status') }}" method="POST">
    @csrf
    @method('PATCH')

    <div id="bulk-action-bar" class="card-yb p-3 mb-3 d-none border-primary" style="background: #FFF0F3; border: 1.5px solid var(--pink-primary);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark font-bold px-3 py-2 border shadow-2xs" style="font-size: 12.5px;">
                    <i class="bi bi-check2-square text-primary me-1"></i> <span id="selected-count">0</span> Pesanan Terpilih
                </span>
                <span class="text-muted font-semibold small d-none d-md-inline">Pilih aksi massal untuk pesanan yang dicentang:</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <select name="status" id="bulk-status-select" class="form-select form-select-sm font-semibold rounded-3 shadow-2xs" style="max-width: 200px; font-size: 12.5px;">
                    <option value="selesai">✅ Ubah ke Selesai</option>
                    <option value="dikirim">🚚 Ubah ke Dikirim</option>
                    <option value="diproses">⏳ Ubah ke Diproses</option>
                    <option value="cancel">❌ Ubah ke Dibatalkan</option>
                </select>
                <button type="submit" class="btn btn-sm btn-yb px-3 font-semibold text-white shadow-2xs" style="border-radius: 10px; font-size: 12.5px;" onclick="return confirm('Yakin ingin memperbarui status pesanan terpilih secara massal?');">
                    <i class="bi bi-lightning-fill me-1"></i> Terapkan Massal
                </button>
            </div>
        </div>
    </div>

    <div class="card-yb p-4">
        <div class="table-responsive">
            <table class="table table-yb align-middle" style="font-size: 13px;">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">
                            <input type="checkbox" id="check-all" class="form-check-input" title="Pilih Semua di Halaman Ini">
                        </th>
                        <th>No. Pesanan</th>
                        <th>Platform</th>
                        <th>Pembeli</th>
                        <th>Tanggal</th>
                        <th class="text-end">Total</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanan as $p)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="ids[]" value="{{ $p->id }}" class="form-check-input order-checkbox">
                        </td>
                        <td class="fw-semibold">
                            <code class="fw-bold" style="color: var(--pink-primary-dark); font-size: 12px;">{{ $p->no_pesanan }}</code>
                        </td>
                        <td>
                            <span class="badge font-semibold {{ $p->platform=='shopee' ? 'bg-warning text-dark' : 'bg-dark text-white' }}">
                                {{ ucfirst($p->platform) }}
                            </span>
                        </td>
                        <td class="fw-bold" style="color: var(--ink);">{{ $p->nama_pembeli }}</td>
                        <td>{{ $p->tanggal ? $p->tanggal->format('d M Y') : '-' }}</td>
                        <td class="text-end fw-bold" style="color: #2EAF6C;">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('pesanan.update_status', $p->id) }}" method="POST" class="d-inline-block">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="form-select form-select-sm rounded-pill font-semibold shadow-2xs" style="font-size: 11.5px; border-color: var(--border-soft); background-color: {{ $p->status=='selesai' ? '#E8F7EE' : ($p->status=='dikirim' ? '#EBF5FF' : ($p->status=='cancel' ? '#FFE6E6' : '#FFF9E6')) }}; color: {{ $p->status=='selesai' ? '#1E7E34' : ($p->status=='dikirim' ? '#1B6EC2' : ($p->status=='cancel' ? '#DC3545' : '#B35118')) }};">
                                    <option value="diproses" {{ $p->status=='diproses'?'selected':'' }}>⏳ Diproses</option>
                                    <option value="dikirim" {{ $p->status=='dikirim'?'selected':'' }}>🚚 Dikirim</option>
                                    <option value="selesai" {{ $p->status=='selesai'?'selected':'' }}>✅ Selesai</option>
                                    <option value="cancel" {{ $p->status=='cancel'?'selected':'' }}>❌ Dibatalkan</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('pesanan.show', $p->id) }}" class="btn btn-sm btn-outline-secondary rounded-circle" title="Lihat Detail"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-secondary py-4">Belum ada pesanan online</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $pesanan->links() }}
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('check-all');
    const checkboxes = document.querySelectorAll('.order-checkbox');
    const bulkBar = document.getElementById('bulk-action-bar');
    const selectedCount = document.getElementById('selected-count');

    function updateBulkBar() {
        const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
        if (checkedCount > 0) {
            bulkBar.classList.remove('d-none');
            selectedCount.textContent = checkedCount;
        } else {
            bulkBar.classList.add('d-none');
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkBar();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (!this.checked && checkAll) {
                checkAll.checked = false;
            }
            updateBulkBar();
        });
    });
});
</script>

@endsection
