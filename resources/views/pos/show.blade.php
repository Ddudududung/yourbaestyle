@extends('layouts.app')

@section('title', 'Detail Transaksi ')

@section('content')

<div class="d-flex justify-content-center">
    <div style="max-width:320px;width:100%">
        <div class="card-yb p-4" id="strukArea" style="border-radius:24px">
            <div class="text-center mb-2">
                <h5 class="mb-0 fw-bold" style="font-family:'Quicksand',sans-serif">Yourbaestyle</h5>
                <p class="text-secondary mb-0 fw-semibold" style="font-size:11px">Jl. Aster No.61, Rancamanyar, Kec. Baleendah, Kabupaten Bandung, Jawa Barat 40375<br>yourbaestyle.com</p>
            </div>
            <hr style="border-style:dashed;border-color:var(--border-soft)">

            <div style="font-size:12px" class="fw-semibold">
                <div class="d-flex justify-content-between"><span class="text-secondary">No. Transaksi</span><span>{{ $transaksi->kode_transaksi }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-secondary">Tanggal</span><span>{{ $transaksi->tanggal->format('d M Y, H:i') }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-secondary">Kasir</span><span>{{ $transaksi->user->nama }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-secondary">Metode Bayar</span><span>{{ ucfirst($transaksi->metode_bayar) }}</span></div>
            </div>

            <hr style="border-style:dashed;border-color:var(--border-soft)">

            <div style="font-size:12px">
                @foreach($transaksi->detail as $d)
                <div class="d-flex justify-content-between mb-1 fw-semibold">
                    <span class="grow">{{ $d->produk->nama_produk }} x{{ $d->qty }}</span>
                    <span>Rp {{ number_format($d->subtotal,0,',','.') }}</span>
                </div>
                @endforeach
            </div>

            <hr style="border-style:dashed;border-color:var(--border-soft)">

            <div style="font-size:12px" class="fw-semibold">
                <div class="d-flex justify-content-between"><span class="text-secondary">Subtotal</span><span>Rp {{ number_format($transaksi->total_harga + $transaksi->diskon,0,',','.') }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-secondary">Diskon</span><span>Rp {{ number_format($transaksi->diskon,0,',','.') }}</span></div>
                <div class="d-flex justify-content-between fw-bold mt-1 pt-1" style="font-size:15px;color:var(--pink-primary-dark);border-top:2px dashed var(--border-soft)">
                    <span>Total</span><span>Rp {{ number_format($transaksi->total_harga,0,',','.') }}</span>
                </div>
            </div>

            <p class="text-center text-secondary mt-3 mb-0 fw-semibold" style="font-size:10px">
                Terima kasih telah berbelanja! <br>Barang yang sudah dibeli tidak dapat dikembalikan
            </p>
        </div>

        <div class="d-flex gap-2 mt-3">
            <a href="{{ route('transaksi.index') }}" class="btn btn-yb-outline w-100">Kembali</a>
            <button onclick="window.print()" class="btn btn-yb w-100"><i class="bi bi-printer"></i> Cetak</button>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
@media print {
    .sidebar-wrap, .topbar, .btn { display:none !important; }
    .main-content { margin-left:0 !important; }
}
</style>
@endpush
