<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google" content="notranslate">
    <title>Official Catalog — Yourbaestyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-custom sticky-top">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ url('/') }}" class="text-decoration-none d-flex align-items-center gap-2">
                <div style="width: 42px; height: 42px; min-width: 42px; min-height: 42px; max-width: 42px; max-height: 42px; border-radius: 50%; overflow: hidden; flex-shrink: 0; box-shadow: 0 3px 10px rgba(236, 149, 168, 0.3); background: #FFDCED; display: flex; align-items: center; justify-content: center; border: 2px solid rgba(236, 149, 168, 0.4);">
                    <img src="{{ asset('images/logo.svg') }}" alt="Yourbaestyle Logo" style="width: 100%; height: 100%; max-width: 100%; max-height: 100%; object-fit: cover; display: block;" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark font-heading" style="letter-spacing: -0.5px;">Yourbaestyle</h5>
                    <small style="color: var(--brand-pink); font-size: 10.5px; font-weight: 800; letter-spacing: 1px;">CURATED BRAND</small>
                </div>
            </a>
            
            <a href="{{ route('login') }}" class="btn btn-sm rounded-pill fw-bold transition" style="background: var(--brand-light); color: var(--brand-pink); border: 1px solid var(--brand-border); padding: 8px 18px;">
                <i class="bi bi-person-circle me-1"></i> Log-in
            </a>
        </div>
    </nav>

    <!-- HERO & SEARCH -->
    <div class="container hero-section text-center">
        <span class="badge rounded-pill px-3 py-2 mb-3 fw-bold" style="background: #FFF0F3; color: var(--brand-pink); border: 1px solid var(--brand-border); font-size: 11px;">
             OFFICIAL SNEAK PEEK CATALOG 
        </span>
        <h2 class="fw-bold mb-2 font-heading display-6 text-dark">Temukan Style Terbaikmu</h2>
        <p class="text-muted mb-4 mx-auto small" style="max-width: 480px; line-height: 1.6;">
            Pantau koleksi pakaian pilihan dari Yourbaestyle. Siap-siap rebutan saat <strong>TikTok Live</strong> berlangsung dengan harga spesial!
        </p>
        
        <form action="{{ url('/') }}" method="GET" class="mx-auto" style="max-width: 480px;">
            <div class="search-box d-flex align-items-center">
                <i class="bi bi-search text-muted me-2"></i>
                <input type="text" name="search" class="search-input form-control shadow-none" placeholder="Cari nama pakaian, warna, atau jenis..." value="{{ request('search') }}">
                <button type="submit" class="btn rounded-pill fw-bold text-white px-4 py-2" style="background: linear-gradient(135deg, var(--brand-pink), #D97B90); border: none;">
                    Cari
                </button>
            </div>
        </form>
    </div>

    <!-- BAGIAN 1: PRODUK SEDANG LIVE (READY STOCK) -->
    <div class="container pb-4 mt-2">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="fw-bold text-dark font-heading mb-0 d-flex align-items-center gap-2">
                <span style="display:inline-block; width:10px; height:10px; background:#ff0050; border-radius:50%; box-shadow: 0 0 8px #ff0050;"></span>
                Sedang Tayang di Live (Siap Rebutan!)
            </h5>
            <span class="badge bg-light text-dark border">{{ $liveProduks->total() }} Produk</span>
        </div>

        <div class="row g-3 g-lg-4">
            @forelse($liveProduks as $p)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100 d-flex flex-column" style="border: 2px solid #ff0050;">
                    
                    <!-- Area Foto & Badge Live -->
                    <div class="product-image-wrapper position-relative">
                        <span class="status-badge status-live"><i class="bi bi-broadcast me-1"></i> LIVE NOW</span>

                        @if(!empty($p->kode_live))
                            <span class="position-absolute top-0 end-0 m-2 badge rounded-pill px-3 py-1.5 shadow-sm" style="background: linear-gradient(135deg, #E60044, #FF3366); color: #ffffff; font-size: 12.5px; font-weight: 800; z-index: 10; border: 2px solid #ffffff;">
                                <i class="bi bi-hash me-0.5"></i>No. Live: {{ $p->kode_live }}
                            </span>
                        @endif

                        @if($p->foto)
                            <img src="{{ asset('storage/'.$p->foto) }}" class="product-image" alt="{{ $p->nama_produk }}">
                        @else
                            <div class="placeholder-aesthetic">
                                <i class="bi bi-bag-heart fs-1 mb-1"></i>
                                <span style="font-size: 11px; font-weight: 700;">YOURBAESTYLE</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Info Produk -->
                    <div class="p-3 p-md-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge rounded-pill px-2.5 py-1" style="background: var(--brand-light); color: var(--brand-pink); border: 1px solid var(--brand-border); font-size: 10px; font-weight: 800;">
                                {{ strtoupper($p->jenisPakaian->nama ?? 'FASHION') }}
                            </span>
                            @if(!empty($p->kode_live))
                                <span class="badge rounded-pill px-2.5 py-1" style="background: #FFF3C4; color: #854D0E; border: 1.5px solid #FDE047; font-size: 11.5px; font-weight: 800;" title="Nomor Baju Saat Live Stream">
                                    <i class="bi bi-broadcast-pin me-1"></i> Kode: {{ $p->kode_live }}
                                </span>
                            @endif
                        </div>

                        <h6 class="fw-bold text-dark mb-3 text-clamp-2" style="font-size: 14.5px; line-height: 1.4;">
                            {{ $p->nama_produk }}
                        </h6>
                        
                        <div class="mt-auto pt-3 border-top" style="border-color: var(--brand-border) !important;">
                            <a href="https://tiktok.com/@ybstyle4" target="_blank" class="text-decoration-none btn-tiktok w-100" style="background: #ff0050; color: #fff;">
                                <i class="bi bi-tiktok fs-6"></i> Ambil di TikTok
                            </a>
                        </div>
                    </div>

                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 my-2">
                <div style="width: 70px; height: 70px; background: #FFF0F3; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; color: var(--brand-pink);">
                    <i class="bi bi-camera-video-off-fill fs-2"></i>
                </div>
                <h6 class="fw-bold text-dark font-heading">Belum Ada Sesi Live Aktif</h6>
                <p class="text-muted small mx-auto mb-0" style="max-width: 350px;">
                    Saat ini belum ada produk yang dimasukkan ke dalam sesi live. Silakan cek koleksi reguler di bawah!
                </p>
            </div>
            @endforelse
        </div>

        <!-- NAVIGASI NEXT / PREV (Khusus Produk Live) -->
        @if($liveProduks->hasPages())
        <div class="d-flex justify-content-center mt-5">
            {{ $liveProduks->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

    <!-- GARIS BATAS (DIVIDER) -->
    <div class="container my-4">
        <hr style="border-color: #F0CCD4; border-width: 2px; border-style: dashed; opacity: 0.6;">
    </div>

    <!-- BAGIAN 2: PRODUK NON-LIVE & HABIS (DI BAWAH NAVIGASI NEXT) -->
    <div class="container pb-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h5 class="fw-bold text-dark font-heading mb-1"><i class="bi bi-grid-fill me-2" style="color: var(--brand-pink);"></i>Koleksi Lainnya & History Live</h5>
                <small class="text-muted">Koleksi reguler serta produk yang Lainnya.</small>
            </div>
        </div>

        <div class="row g-3 g-lg-4">
            @forelse($otherProduks as $p)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100 d-flex flex-column {{ $p->stok <= 0 ? 'opacity-75' : '' }}">
                    
                    <!-- Area Foto & Badge -->
                    <div class="product-image-wrapper">
                        @if($p->stok > 0)
                            <span class="status-badge status-ready"><i class="bi bi-check-circle-fill me-1"></i> Ready</span>
                        @else
                            <span class="status-badge status-sold">Sold Out</span>
                        @endif

                        @if($p->foto)
                            <img src="{{ asset('storage/'.$p->foto) }}" class="product-image" alt="{{ $p->nama_produk }}">
                        @else
                            <div class="placeholder-aesthetic">
                                <i class="bi bi-bag-heart fs-1 mb-1"></i>
                                <span style="font-size: 11px; font-weight: 700;">YOURBAESTYLE</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Info Produk -->
                    <div class="p-3 p-md-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge rounded-pill px-2.5 py-1" style="background: var(--brand-light); color: var(--brand-pink); border: 1px solid var(--brand-border); font-size: 10px; font-weight: 800;">
                                {{ strtoupper($p->jenisPakaian->nama ?? 'FASHION') }}
                            </span>
                            @if($p->stok > 0)
                            @else
                                <small class="text-danger fw-bold" style="font-size: 11px;">Habis</small>
                            @endif
                        </div>

                        <h6 class="fw-bold text-dark mb-3 text-clamp-2" style="font-size: 14.5px; line-height: 1.4;">
                            {{ $p->nama_produk }}
                        </h6>
                        
                        <div class="mt-auto pt-3 border-top" style="border-color: var(--brand-border) !important;">
                            @if($p->stok > 0)
                                <a href="https://tiktok.com/@ybstyle4" target="_blank" class="text-decoration-none btn-tiktok w-100">
                                    <i class="bi bi-tiktok fs-6"></i> Pantau TikTok
                                </a>
                            @else
                                <button class="btn btn-light w-100 rounded-pill text-muted fw-bold" style="font-size: 12px; border: 1px solid var(--brand-border);" disabled>
                                    Sudah Terjual
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            @empty
            <div class="col-12 text-center py-4">
                <p class="text-muted small">Belum ada produk tambahan di katalog ini.</p>
            </div>
            @endforelse
        </div>
    </div>

</body>
</html>