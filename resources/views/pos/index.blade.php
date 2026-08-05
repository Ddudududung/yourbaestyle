@extends('layouts.app')

@section('title', 'Point of Sale (Kasir) — Yourbaestyle')

@section('content')

<div class="row g-4">
    <!-- KOLOM KIRI: KATALOG PRODUK KASIR -->
    <div class="col-12 col-lg-7">
        <div class="card-yb p-4 h-100">
            <!-- Header Card Produk -->
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 1.5px dashed var(--border-soft);">
                <h6 class="fw-bold mb-0" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                    <i class="bi bi-shop me-2" style="color: var(--pink-primary);"></i>Daftar Produk Kasir
                </h6>
                <span class="badge bg-light text-muted font-semibold px-3 py-1.5" style="font-size: 11px;">{{ count($produk) }} Produk Aktif</span>
            </div>

            <!-- Bar Pencarian (Baris 1) & Filter Pills (Baris 2) -->
            <div class="mb-3">
                <!-- Baris 1: Search Bar Full Width -->
                <div class="input-group mb-2">
                    <span class="input-group-text bg-white border-end-0" style="border-color: var(--border-soft); border-radius: 14px 0 0 14px; color: var(--ink-soft);">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="searchProduk" class="form-control border-start-0 ps-0" placeholder="Cari nama atau kode produk..." style="border-radius: 0 14px 14px 0; border-color: var(--border-soft);">
                </div>

                <!-- Baris 2: Pill Filter Cepat -->
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-sm btn-yb-outline font-semibold text-xs active py-1 px-3" onclick="filterStok('semua', this)">Semua</button>
                    <button type="button" class="btn btn-sm btn-yb-outline font-semibold text-xs py-1 px-3" onclick="filterStok('ready', this)">Ready (>3)</button>
                    <button type="button" class="btn btn-sm btn-yb-outline font-semibold text-xs py-1 px-3" onclick="filterStok('menipis', this)">Stok Menipis (<=3)</button>
                </div>
            </div>

            <!-- Grid Kartu Produk -->
            <div class="row g-3" id="gridProduk" style="max-height: 540px; overflow-y: auto; padding-right: 2px;">
                @forelse($produk as $p)
                <div class="col-6 col-sm-4 produk-item" 
                     data-nama="{{ strtolower($p->nama_produk) }}" 
                     data-kode="{{ strtolower($p->kode_produk ?? '') }}"
                     data-stok="{{ $p->stok }}">
                    <div class="pos-product-card h-100 d-flex flex-column justify-content-between p-3 {{ $p->stok == 0 ? 'disabled' : '' }}"
                         onclick="{{ $p->stok > 0 ? 'tambahKeranjang('.$p->id.', \''.addslashes($p->nama_produk).'\', '.$p->harga_jual.', '.$p->stok.')' : '' }}">
                        <div>
                            <!-- Thumbnail Foto -->
                            <div class="pos-thumb mb-2" style="aspect-ratio: 1/1; border-radius: 12px; background: #fff; border: 1.5px solid var(--border-soft); display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 4px;">
                                @if($p->foto && file_exists(storage_path('app/public/'.$p->foto)))
                                    <img src="{{ asset('storage/'.$p->foto) }}" alt="{{ $p->nama_produk }}" style="width: 100%; height: 100%; object-fit: contain;">
                                @else
                                    <i class="bi bi-bag-heart fs-1" style="color: var(--pink-soft);"></i>
                                @endif
                            </div>

                            <!-- Nama Produk -->
                            <div class="fw-bold text-truncate mb-1" style="font-size: 12.5px; color: var(--ink);" title="{{ $p->nama_produk }}">
                                {{ $p->nama_produk }}
                            </div>
                        </div>

                        <!-- Price & Stock Footer -->
                        <div class="pt-2" style="border-top: 1px dashed var(--border-soft);">
                            <div class="fw-bold mb-1" style="font-size: 13px; color: #52976D;">
                                Rp {{ number_format($p->harga_jual, 0, ',', '.') }}
                            </div>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                <small class="text-muted text-truncate" style="font-size: 10px; max-width: 90px;" title="{{ $p->kode_produk }}">{{ $p->kode_produk ?? '-' }}</small>
                                @if($p->stok == 0)
                                    <span class="badge bg-danger" style="font-size: 9px; padding: 3px 6px;">Habis</span>
                                @elseif($p->stok <= 3)
                                    <span class="badge bg-warning text-dark" style="font-size: 9px; padding: 3px 6px;">Stok: {{ $p->stok }}</span>
                                @else
                                    <span class="badge bg-success" style="font-size: 9px; padding: 3px 6px;">Stok: {{ $p->stok }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5 text-muted font-semibold">
                    <i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--border-soft);"></i>
                    Belum ada produk yang tersedia.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: KERANJANG & PEMBAYARAN KASIR -->
    <div class="col-12 col-lg-5">
        <div class="card-yb p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <!-- Header Keranjang -->
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 1.5px dashed var(--border-soft);">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="fw-bold mb-0" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                            <i class="bi bi-cart3 me-1" style="color: var(--pink-primary);"></i>Keranjang
                        </h6>
                        <span id="cartCountBadge" class="badge" style="background: var(--pink-primary); color: #fff; font-size: 10px;">0 Item</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 font-semibold" style="font-size: 11.5px;" onclick="resetKeranjang()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                </div>

                <!-- Daftar Item Keranjang -->
                <div id="keranjangList" style="max-height: 220px; overflow-y: auto; padding-right: 2px;" class="mb-3">
                    <div class="text-center py-4 text-muted font-semibold" id="emptyCart">
                        <i class="bi bi-basket fs-1 d-block mb-2" style="color: var(--border-soft);"></i>
                        Keranjang masih kosong 🌷
                    </div>
                </div>
            </div>

            <!-- Panel Rincian Pembayaran -->
            <div class="pt-3" style="border-top: 1.5px dashed var(--border-soft);">
                <!-- Subtotal -->
                <div class="d-flex justify-content-between font-semibold mb-2" style="font-size: 12.5px;">
                    <span class="text-muted">Subtotal</span>
                    <span id="subtotalText" class="fw-bold" style="color: var(--ink);">Rp 0</span>
                </div>

                <!-- Input Diskon & Preset Chips -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1.5 flex-wrap gap-1">
                        <label class="form-label font-semibold mb-0" style="font-size: 11.5px;">Diskon (Rp)</label>
                        <div class="d-flex gap-1">
                            <span class="discount-chip" onclick="setDiskon(5000)">+5k</span>
                            <span class="discount-chip" onclick="setDiskon(10000)">+10k</span>
                            <span class="discount-chip" onclick="setDiskon(15000)">+15k</span>
                            <span class="discount-chip text-danger" onclick="setDiskon(0)">Reset</span>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0" style="border-color: var(--border-soft); border-radius: 12px 0 0 12px; color: var(--ink-soft);">Rp</span>
                        <input type="text" id="diskonInput" class="form-control border-start-0 input-rupiah" data-type="rupiah" value="0" oninput="updateTotal()" style="border-radius: 0 12px 12px 0; border-color: var(--border-soft);">
                    </div>
                </div>

                <!-- Total Akhir Highlight Card -->
                <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-3" style="background: var(--pink-soft-2); border: 1px solid var(--border-soft);">
                    <span class="fw-bold text-muted" style="font-size: 12px; letter-spacing: 0.5px;">TOTAL BAYAR</span>
                    <span id="totalText" class="fw-bold fs-4" style="color: var(--pink-primary-dark); font-family: 'Quicksand', sans-serif;">Rp 0</span>
                </div>

                <!-- Opsi Metode Pembayaran Pill -->
                <label class="form-label font-semibold mb-1.5" style="font-size: 11.5px;">Metode Pembayaran</label>
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <input type="radio" class="payment-option d-none" name="metode" id="tunai" value="tunai" checked onchange="toggleBayarTunai(true)">
                        <label class="payment-pill-btn w-100" for="tunai">
                            <span class="fs-5">💵</span>
                            <span>Tunai</span>
                        </label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="payment-option d-none" name="metode" id="transfer" value="transfer" onchange="toggleBayarTunai(false)">
                        <label class="payment-pill-btn w-100" for="transfer">
                            <span class="fs-5">🏦</span>
                            <span>Transfer</span>
                        </label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="payment-option d-none" name="metode" id="qris" value="qris" onchange="toggleBayarTunai(false)">
                        <label class="payment-pill-btn w-100" for="qris">
                            <span class="fs-5">📱</span>
                            <span>QRIS</span>
                        </label>
                    </div>
                </div>

                <!-- Input Kalkulator Bayar Tunai & Kembalian Box -->
                <div id="boxBayarTunai" class="p-3 rounded-3 mb-3" style="background: var(--pink-soft-2); border: 1px solid var(--border-soft);">
                    <div class="row g-2 align-items-center">
                        <div class="col-7">
                            <label class="form-label font-semibold mb-1" style="font-size: 11px; color: var(--ink);">Uang Diterima (Rp)</label>
                            <input type="text" id="uangDiterimaInput" class="form-control form-control-sm input-rupiah" data-type="rupiah" placeholder=" " oninput="hitungKembalian()" style="border-radius: 10px; border-color: var(--border-soft);">
                        </div>
                        <div class="col-5 text-end">
                            <label class="form-label font-semibold mb-0 text-muted" style="font-size: 11px;">Kembalian</label>
                            <div id="kembalianText" class="fw-bold" style="color: #52976D; font-size: 14px;">Rp 0</div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Proses Transaksi -->
                <button class="btn-yb w-100 py-3 font-semibold fs-6" id="btnProses" onclick="prosesTransaksi()" disabled>
                    <i class="bi bi-check-circle me-1"></i> Proses Transaksi Kasir
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let keranjang = [];

function tambahKeranjang(id, nama, harga, stokMax) {
    const existing = keranjang.find(item => item.id_produk === id);
    if (existing) {
        if (existing.qty < stokMax) {
            existing.qty++;
        } else {
            alert('Stok maksimal produk [' + nama + '] telah tercapai.');
            return;
        }
    } else {
        keranjang.push({ id_produk: id, nama, harga, qty: 1, stokMax });
    }
    renderKeranjang();
}

function ubahQty(id, delta) {
    const item = keranjang.find(i => i.id_produk === id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) {
        keranjang = keranjang.filter(i => i.id_produk !== id);
    } else if (item.qty > item.stokMax) { 
        item.qty = item.stokMax; 
        alert('Stok maksimal tercapai.'); 
    }
    renderKeranjang();
}

function hapusItem(id) { 
    keranjang = keranjang.filter(i => i.id_produk !== id); 
    renderKeranjang(); 
}

function resetKeranjang() {
    if (keranjang.length === 0) return;
    if (confirm('Kosongkan semua barang di keranjang?')) {
        keranjang = [];
        document.getElementById('diskonInput').value = 0;
        document.getElementById('uangDiterimaInput').value = '';
        renderKeranjang();
    }
}

function getRawNumeric(elementId) {
    const el = document.getElementById(elementId);
    if (!el) return 0;
    const cleanStr = el.value.toString().replace(/\D/g, '');
    return parseFloat(cleanStr) || 0;
}

function setDiskon(nominal) {
    const subtotal = keranjang.reduce((sum, item) => sum + (item.harga * item.qty), 0);
    const diskonInput = document.getElementById('diskonInput');
    let currentDiskon = getRawNumeric('diskonInput');
    let newDiskon = currentDiskon + nominal;
    if (nominal === 0) newDiskon = 0;
    
    if (newDiskon > subtotal) newDiskon = subtotal;
    diskonInput.value = formatRupiahDisplay(newDiskon);
    updateTotal();
}

function formatRupiah(angka) { 
    return 'Rp ' + Math.max(0, angka).toLocaleString('id-ID'); 
}

function toggleBayarTunai(isTunai) {
    const box = document.getElementById('boxBayarTunai');
    if (box) box.style.display = isTunai ? 'block' : 'none';
}

function hitungKembalian() {
    const subtotal = keranjang.reduce((sum, item) => sum + (item.harga * item.qty), 0);
    const diskon = getRawNumeric('diskonInput');
    const totalNet = Math.max(0, subtotal - diskon);
    const uangDiterima = getRawNumeric('uangDiterimaInput');

    const kembalianText = document.getElementById('kembalianText');
    if (uangDiterima >= totalNet && totalNet > 0) {
        const kembalian = uangDiterima - totalNet;
        kembalianText.innerHTML = formatRupiah(kembalian);
        kembalianText.style.color = '#52976D';
    } else {
        kembalianText.innerHTML = 'Rp 0';
        kembalianText.style.color = '#AD5050';
    }
}

function renderKeranjang() {
    const list = document.getElementById('keranjangList');
    const badge = document.getElementById('cartCountBadge');
    
    const totalItemCount = keranjang.reduce((sum, item) => sum + item.qty, 0);
    badge.innerText = totalItemCount + ' Item';

    if (keranjang.length === 0) {
        list.innerHTML = `
            <div class="text-center py-4 text-muted font-semibold" id="emptyCart">
                <i class="bi bi-basket fs-1 d-block mb-2" style="color: var(--border-soft);"></i>
                Keranjang masih kosong 🌷
            </div>`;
        document.getElementById('btnProses').disabled = true;
    } else {
        list.innerHTML = keranjang.map(item => `
            <div class="p-2.5 rounded-3 mb-2" style="background: var(--pink-soft-2); border: 1px solid var(--border-soft);">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div class="fw-bold text-truncate" style="font-size: 12.5px; color: var(--ink); flex: 1;" title="${item.nama}">${item.nama}</div>
                    <button type="button" class="btn btn-sm text-danger border-0 p-0 ms-2" onclick="hapusItem(${item.id_produk})" title="Hapus item"><i class="bi bi-trash fs-6"></i></button>
                </div>
                <div class="d-flex justify-content-between align-items-center text-xs">
                    <span class="text-muted font-semibold">${formatRupiah(item.harga)} x ${item.qty} = <strong style="color: #52976D;">${formatRupiah(item.harga * item.qty)}</strong></span>
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="btn btn-sm btn-light border p-0 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; border-radius: 6px;" onclick="ubahQty(${item.id_produk}, -1)">−</button>
                        <span class="fw-bold px-1" style="font-size: 12px; min-width: 18px; text-align: center;">${item.qty}</span>
                        <button type="button" class="btn btn-sm btn-light border p-0 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; border-radius: 6px;" onclick="ubahQty(${item.id_produk}, 1)">+</button>
                    </div>
                </div>
            </div>
        `).join('');
        document.getElementById('btnProses').disabled = false;
    }
    updateTotal();
}

function updateTotal() {
    const subtotal = keranjang.reduce((sum, item) => sum + (item.harga * item.qty), 0);
    let diskon = getRawNumeric('diskonInput');
    
    if (diskon < 0) {
        diskon = 0;
        document.getElementById('diskonInput').value = '0';
    } else if (diskon > subtotal) {
        diskon = subtotal;
        document.getElementById('diskonInput').value = formatRupiahDisplay(subtotal);
    }

    document.getElementById('subtotalText').innerText = formatRupiah(subtotal);
    document.getElementById('totalText').innerText = formatRupiah(subtotal - diskon);
    hitungKembalian();
}

function filterStok(tipe, btnEl) {
    document.querySelectorAll('.btn-yb-outline').forEach(b => b.classList.remove('active'));
    if (btnEl) btnEl.classList.add('active');

    document.querySelectorAll('.produk-item').forEach(el => {
        const stok = parseInt(el.dataset.stok) || 0;
        if (tipe === 'ready') {
            el.style.display = stok > 3 ? '' : 'none';
        } else if (tipe === 'menipis') {
            el.style.display = (stok > 0 && stok <= 3) ? '' : 'none';
        } else {
            el.style.display = '';
        }
    });
}

function prosesTransaksi() {
    if (keranjang.length === 0) return;
    const metode = document.querySelector('input[name="metode"]:checked').value;
    const diskon = getRawNumeric('diskonInput');
    const items = keranjang.map(item => ({ id_produk: item.id_produk, qty: item.qty }));

    const btn = document.getElementById('btnProses');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses Transaksi...';

    fetch('{{ route("pos.proses") }}', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json', 
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ items, metode_bayar: metode, diskon })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) { 
            alert('✅ Transaksi Kasir Berhasil!\nKode Transaksi: ' + data.kode_transaksi); 
            window.location.href = '/transaksi/' + data.transaksi_id; 
        } else { 
            alert('❌ Gagal: ' + (data.message || 'Terjadi kesalahan sistem.')); 
            btn.disabled = false; 
            btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Proses Transaksi Kasir'; 
        }
    })
    .catch(() => { 
        alert('❌ Terjadi kesalahan koneksi ke server.'); 
        btn.disabled = false; 
        btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Proses Transaksi Kasir'; 
    });
}

document.getElementById('searchProduk').addEventListener('input', function() {
    const keyword = this.value.toLowerCase().trim();
    document.querySelectorAll('.produk-item').forEach(el => {
        const nama = el.dataset.nama || '';
        const kode = el.dataset.kode || '';
        el.style.display = (nama.includes(keyword) || kode.includes(keyword)) ? '' : 'none';
    });
});
</script>
@endpush