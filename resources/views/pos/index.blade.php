@extends('layouts.app')

@section('title', 'Point of Sale ')

@section('content')

<style>
    .produk-card {
        border:2px solid var(--border-soft); border-radius:20px; padding:12px; text-align:center;
        cursor:pointer; transition:.18s; background:#fff; height:100%;
    }
    .produk-card:hover { border-color:var(--pink-primary); box-shadow:0 10px 22px rgba(236,149,168,.22); transform:translateY(-3px); }
    .produk-card.disabled { opacity:.4; cursor:not-allowed; }
    .produk-thumb {
        background:var(--pink-soft-2); border-radius:16px; height:68px; margin-bottom:9px;
        display:flex; align-items:center; justify-content:center; overflow:hidden;
    }
    .cart-item { display:flex; justify-content:space-between; align-items:center; padding:10px 12px; border-radius:16px; background:var(--pink-soft-2); margin-bottom:8px; }
    .qty-btn { width:28px; height:28px; border-radius:10px; border:2px solid var(--border-soft); background:#fff; color:var(--ink); font-weight:700; }
    .qty-btn:hover { background:var(--pink-soft); border-color:var(--pink-primary); }
    .payment-option { display:none; }
    .payment-label {
        border:2px solid var(--border-soft); border-radius:16px; padding:10px; text-align:center; font-size:12.5px;
        font-weight:700; color:var(--ink-soft); cursor:pointer; transition:.15s;
    }
    .payment-option:checked + .payment-label { background:var(--pink-primary); color:#fff; border-color:var(--pink-primary); }
</style>

<div class="row g-3">
    <div class="col-md-7">
        <div class="card-yb p-4">
            <h6 class="fw-bold mb-3" style="font-family:'Quicksand',sans-serif">Daftar Produk</h6>
            <input type="text" id="searchProduk" class="form-control mb-3" placeholder="Cari nama atau kode produk...">
            <div class="row g-2" id="gridProduk">
                @foreach($produk as $p)
                <div class="col-4 produk-item" data-nama="{{ strtolower($p->nama_produk) }}" data-kode="{{ strtolower($p->kode_produk) }}">
                    <div class="produk-card {{ $p->stok==0?'disabled':'' }}"
                         onclick="{{ $p->stok > 0 ? 'tambahKeranjang('.$p->id.', \''.addslashes($p->nama_produk).'\', '.$p->harga_jual.', '.$p->stok.')' : '' }}">
                        <div class="produk-thumb">
                            @if($p->foto)
                                <img src="{{ asset('storage/'.$p->foto) }}" style="max-height:68px;max-width:100%;object-fit:cover">
                            @else
                                <i class="bi bi-bag-heart" style="font-size:24px;color:#E8B4BE"></i>
                            @endif
                        </div>
                        <div style="font-size:11.5px;font-weight:700" class="text-truncate">{{ $p->nama_produk }}</div>
                        <div style="font-size:11.5px;color:var(--pink-primary-dark);font-weight:700">Rp {{ number_format($p->harga_jual,0,',','.') }}</div>
                        <div style="font-size:10px;font-weight:600" class="{{ $p->stok <= 3 ? 'text-danger' : '' }}" style="{{ $p->stok > 3 ? 'color:#56794D' : '' }}">
                            Stok: {{ $p->stok }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card-yb p-4">
            <h6 class="fw-bold mb-3" style="font-family:'Quicksand',sans-serif">Keranjang Transaksi</h6>
            <div id="keranjangList" style="max-height:260px;overflow-y:auto">
                <p class="text-secondary small text-center py-4 fw-semibold" id="emptyCart">Keranjang masih kosong</p>
            </div>

            <hr style="border-color:var(--border-soft)">
            <div class="d-flex justify-content-between small mb-2 fw-semibold">
                <span class="text-secondary">Subtotal</span><span id="subtotalText">Rp 0</span>
            </div>
            <div class="mb-3">
                <label class="form-label">Diskon (Rp)</label>
                <input type="number" id="diskonInput" class="form-control" value="0" min="0" oninput="updateTotal()">
            </div>
            <div class="d-flex justify-content-between fw-bold mb-3" style="font-size:18px;color:var(--pink-primary-dark);font-family:'Quicksand',sans-serif">
                <span>Total</span><span id="totalText">Rp 0</span>
            </div>

            <p class="small text-secondary fw-bold mb-2">Metode Pembayaran</p>
            <div class="row g-2 mb-3">
                <div class="col-4">
                    <input type="radio" class="payment-option" name="metode" id="tunai" value="tunai" checked>
                    <label class="payment-label w-100" for="tunai">💵<br>Tunai</label>
                </div>
                <div class="col-4">
                    <input type="radio" class="payment-option" name="metode" id="transfer" value="transfer">
                    <label class="payment-label w-100" for="transfer">🏦<br>Transfer</label>
                </div>
                <div class="col-4">
                    <input type="radio" class="payment-option" name="metode" id="qris" value="qris">
                    <label class="payment-label w-100" for="qris">📱<br>QRIS</label>
                </div>
            </div>

            <button class="btn-yb w-100 py-2" id="btnProses" onclick="prosesTransaksi()" disabled>
                <i class="bi bi-check-circle"></i> Proses Transaksi
            </button>
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
        if (existing.qty < stokMax) existing.qty++;
        else { alert('Stok tidak cukup untuk menambah jumlah ini.'); return; }
    } else {
        keranjang.push({ id_produk: id, nama, harga, qty: 1, stokMax });
    }
    renderKeranjang();
}
function ubahQty(id, delta) {
    const item = keranjang.find(i => i.id_produk === id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) keranjang = keranjang.filter(i => i.id_produk !== id);
    else if (item.qty > item.stokMax) { item.qty = item.stokMax; alert('Stok maksimal tercapai.'); }
    renderKeranjang();
}
function hapusItem(id) { keranjang = keranjang.filter(i => i.id_produk !== id); renderKeranjang(); }
function formatRupiah(angka) { return 'Rp ' + angka.toLocaleString('id-ID'); }

function renderKeranjang() {
    const list = document.getElementById('keranjangList');
    if (keranjang.length === 0) {
        list.innerHTML = '<p class="text-secondary small text-center py-4 fw-semibold">Keranjang masih kosong 🌷</p>';
        document.getElementById('btnProses').disabled = true;
    } else {
        list.innerHTML = keranjang.map(item => `
            <div class="cart-item">
                <div class="flex-grow-1">
                    <div style="font-size:12.5px;font-weight:700">${item.nama}</div>
                    <div style="font-size:11px" class="text-secondary fw-semibold">${formatRupiah(item.harga)} x ${item.qty}</div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button class="qty-btn" onclick="ubahQty(${item.id_produk}, -1)">−</button>
                    <span style="font-size:12.5px;min-width:22px;text-align:center;font-weight:700">${item.qty}</span>
                    <button class="qty-btn" onclick="ubahQty(${item.id_produk}, 1)">+</button>
                    <button class="qty-btn text-danger" onclick="hapusItem(${item.id_produk})"><i class="bi bi-trash" style="font-size:11px"></i></button>
                </div>
            </div>
        `).join('');
        document.getElementById('btnProses').disabled = false;
    }
    updateTotal();
}
function updateTotal() {
    const subtotal = keranjang.reduce((sum, item) => sum + (item.harga * item.qty), 0);
    const diskon = parseFloat(document.getElementById('diskonInput').value) || 0;
    document.getElementById('subtotalText').innerText = formatRupiah(subtotal);
    document.getElementById('totalText').innerText = formatRupiah(Math.max(subtotal - diskon, 0));
}
function prosesTransaksi() {
    if (keranjang.length === 0) return;
    const metode = document.querySelector('input[name="metode"]:checked').value;
    const diskon = parseFloat(document.getElementById('diskonInput').value) || 0;
    const items = keranjang.map(item => ({ id_produk: item.id_produk, qty: item.qty }));

    document.getElementById('btnProses').disabled = true;
    document.getElementById('btnProses').innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';

    fetch('{{ route("pos.proses") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ items, metode_bayar: metode, diskon })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) { alert('Transaksi berhasil! Kode: ' + data.kode_transaksi); window.location.href = '/transaksi/' + data.transaksi_id; }
        else { alert(data.message); document.getElementById('btnProses').disabled = false; document.getElementById('btnProses').innerHTML = '<i class="bi bi-check-circle"></i> Proses Transaksi'; }
    })
    .catch(() => { alert('Terjadi kesalahan koneksi.'); document.getElementById('btnProses').disabled = false; document.getElementById('btnProses').innerHTML = '<i class="bi bi-check-circle"></i> Proses Transaksi'; });
}
document.getElementById('searchProduk').addEventListener('input', function() {
    const keyword = this.value.toLowerCase();
    document.querySelectorAll('.produk-item').forEach(el => {
        el.style.display = (el.dataset.nama.includes(keyword) || el.dataset.kode.includes(keyword)) ? '' : 'none';
    });
});
</script>
@endpush
