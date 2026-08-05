<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Catalog — Yourbaestyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-pink: #EC95A8;
            --brand-dark: #4D3D43;
            --brand-light: #FFF5F7;
            --brand-border: #F7E5EA;
        }
        body { 
            font-family: 'Nunito', sans-serif; 
            background-color: #FAF8F9; 
            color: var(--brand-dark); 
        }
        h1, h2, h3, h4, h5, .font-heading { 
            font-family: 'Quicksand', sans-serif; 
        }
        
        /* Navbar Kustom */
        .navbar-custom { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--brand-border);
            padding: 14px 0; 
        }
        .brand-icon { 
            width: 42px; 
            height: 42px; 
            background: linear-gradient(135deg, var(--brand-pink), #D97B90); 
            border-radius: 14px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: #fff; 
            font-size: 20px; 
            box-shadow: 0 4px 12px rgba(236, 149, 168, 0.3);
        }
        
        /* Hero & Search Section */
        .hero-section {
            padding: 40px 0 30px;
        }
        .search-box { 
            background: #fff; 
            border: 2px solid var(--brand-border); 
            border-radius: 50px; 
            padding: 6px 6px 6px 24px; 
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(236, 149, 168, 0.06);
        }
        .search-box:focus-within { 
            border-color: var(--brand-pink); 
            box-shadow: 0 10px 25px rgba(236, 149, 168, 0.15);
        }
        .search-input { 
            border: none; 
            outline: none; 
            background: transparent; 
            width: 100%; 
            font-weight: 600; 
            color: var(--brand-dark);
            font-size: 14.5px;
        }
        .search-input::placeholder { color: #BBAFB2; }

        /* Kartu Produk Aesthetic */
        .product-card { 
            background: #fff; 
            border-radius: 24px; 
            overflow: hidden; 
            border: 1px solid var(--brand-border); 
            transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        .product-card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 20px 35px rgba(236, 149, 168, 0.18); 
            border-color: #F0CCD4;
        }
        .product-image-wrapper { 
            width: 100%; 
            padding-top: 100%; 
            position: relative; 
            background: var(--brand-light); 
            overflow: hidden; 
        }
        .product-image { 
            position: absolute; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            transition: transform 0.6s ease;
        }
        .product-card:hover .product-image {
            transform: scale(1.08);
        }
        
        .placeholder-aesthetic {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #FFF5F7 0%, #FDE8ED 100%);
            color: #E8B4C0;
        }
        
        /* Badge Status */
        .status-badge { 
            position: absolute; 
            top: 14px; 
            right: 14px; 
            z-index: 2; 
            font-weight: 800; 
            font-size: 10.5px; 
            padding: 6px 14px; 
            border-radius: 30px; 
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .status-ready { 
            background: rgba(255, 255, 255, 0.92); 
            color: #2D7A4D; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: 1px solid rgba(45, 122, 77, 0.15);
        }
        .status-live { 
            background: #ff0050; 
            color: #fff; 
            box-shadow: 0 4px 12px rgba(255,0,80,0.35);
            animation: pulse-live 1.5s infinite;
        }
        .status-sold { 
            background: rgba(77, 61, 67, 0.9); 
            color: #fff; 
        }
        @keyframes pulse-live {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Tombol TikTok Live Action */
        .btn-tiktok {
            background: #FFF0F3;
            color: var(--brand-pink);
            border: 1px solid var(--brand-border);
            font-weight: 700;
            font-size: 12px;
            padding: 8px 12px;
            border-radius: 14px;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .product-card:hover .btn-tiktok {
            background: #111111;
            color: #fff;
            border-color: #111111;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25), 2px 2px 0px #ff0050, -2px -2px 0px #00f2ea;
        }

        .text-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 42px;
        }

        /* Pagination Kustom */
        .pagination { gap: 6px; }
        .page-link {
            color: var(--brand-dark);
            background-color: #fff;
            border: 1px solid var(--brand-border);
            border-radius: 12px !important;
            padding: 8px 16px;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }
        .page-link:hover {
            color: var(--brand-pink);
            background-color: var(--brand-light);
            border-color: var(--brand-pink);
        }
        .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background: linear-gradient(135deg, var(--brand-pink), #D97B90);
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(236, 149, 168, 0.35);
        }
        .page-item.disabled .page-link {
            color: #BBAFB2;
            background-color: #FAF8F9;
            border-color: var(--brand-border);
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-custom sticky-top">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ url('/') }}" class="text-decoration-none d-flex align-items-center gap-2">
                <div class="brand-icon"><i class="bi bi-bag-heart-fill"></i></div>
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
            ✨ OFFICIAL SNEAK PEEK CATALOG ✨
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
                    <div class="product-image-wrapper">
                        <span class="status-badge status-live"><i class="bi bi-broadcast me-1"></i> LIVE NOW</span>

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
                <small class="text-muted">Koleksi reguler serta produk yang sudah habis terjual (Sold Out).</small>
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