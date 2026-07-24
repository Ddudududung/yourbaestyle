<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Koleksi — Yourbaestyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #FAF8F9; color: #4D3D43; }
        h1, h2, h3, h4, h5, .brand-text { font-family: 'Quicksand', sans-serif; }
        
        /* Navbar Kustom */
        .navbar-custom { background: #fff; box-shadow: 0 4px 20px rgba(236, 149, 168, 0.08); padding: 15px 0; }
        .brand-icon { width: 40px; height: 40px; background: #EC95A8; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 20px; transform: rotate(-5deg); }
        
        /* Kartu Produk */
        .product-card { background: #fff; border-radius: 20px; overflow: hidden; border: 1px solid #F7E5EA; transition: all 0.3s ease; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(236, 149, 168, 0.15); }
        .product-image-wrapper { width: 100%; padding-top: 100%; position: relative; background: #FFF0F3; overflow: hidden; }
        .product-image { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }
        
        /* Badge Status */
        .status-badge { position: absolute; top: 12px; right: 12px; z-index: 2; font-weight: 700; font-size: 11px; padding: 6px 12px; border-radius: 20px; backdrop-filter: blur(4px); }
        .status-ready { background: rgba(255, 255, 255, 0.9); color: #EC95A8; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .status-sold { background: rgba(77, 61, 67, 0.85); color: #fff; }

        /* Pencarian */
        .search-box { background: #fff; border: 2px solid #F7E5EA; border-radius: 50px; padding: 5px 5px 5px 20px; transition: border-color 0.2s; }
        .search-box:focus-within { border-color: #EC95A8; }
        .search-input { border: none; outline: none; background: transparent; width: 100%; font-weight: 600; color: #4D3D43; }
        .search-input::placeholder { color: #BBAFB2; }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-custom sticky-top">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ url('/') }}" class="text-decoration-none d-flex align-items-center gap-2">
                <div class="brand-icon"><i class="bi bi-bag-heart-fill"></i></div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark brand-text">Yourbaestyle</h5>
                    <small style="color: #EC95A8; font-size: 11px; font-weight: 700;">OFFICIAL CATALOG</small>
                </div>
            </a>
            <!-- Tombol Login untuk Admin -->
            <a href="{{ route('login') }}" class="btn btn-sm rounded-pill fw-bold" style="background: #FFF5F7; color: #EC95A8; border: 1px solid #F7E5EA; padding: 8px 16px;">
                <i class="bi bi-person-circle me-1"></i> Log-in
            </a>
        </div>
    </nav>

    <!-- HEADER & SEARCH -->
    <div class="container py-4 py-md-5 text-center">
        <h2 class="fw-bold mb-3">Sneak Peek Koleksi Kami ✨</h2>
        <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">Telusuri koleksi Thrift & Rebranding terbaru dari Yourbaestyle. Pantau terus Live Streaming kami untuk mendapatkan harga spesial!</p>
        
        <form action="{{ url('/') }}" method="GET" class="mx-auto" style="max-width: 500px;">
            <div class="search-box d-flex align-items-center">
                <i class="bi bi-search text-muted me-2"></i>
                <input type="text" name="search" class="search-input form-control shadow-none" placeholder="Cari nama koleksi..." value="{{ request('search') }}">
                <button type="submit" class="btn rounded-pill fw-bold text-white px-4" style="background: #EC95A8;">Cari</button>
            </div>
        </form>
    </div>

    <!-- GRID PRODUK -->
    <div class="container pb-5">
        <div class="row g-3 g-md-4">
            @forelse($produks as $p)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100 d-flex flex-column">
                    <!-- Area Foto & Badge -->
                    <div class="product-image-wrapper">
                        @if($p->stok > 0)
                            <span class="status-badge status-ready"><i class="bi bi-stars me-1"></i> Tersedia</span>
                        @else
                            <span class="status-badge status-sold">Habis Terjual</span>
                        @endif

                        @if($p->foto)
                            <img src="{{ asset('storage/'.$p->foto) }}" class="product-image" alt="{{ $p->nama_produk }}">
                        @else
                            <div class="product-image d-flex align-items-center justify-content-center">
                                <i class="bi bi-image text-muted fs-1 opacity-25"></i>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Info Produk -->
                    <div class="p-3 p-md-4 d-flex flex-column grow">
                        <span class="badge mb-2 align-self-start" style="background: #FFF5F7; color: #EC95A8; border: 1px solid #F7E5EA; font-size: 10px; font-weight: 700;">
                            {{ strtoupper($p->jenis) }}
                        </span>
                        <h6 class="fw-bold text-dark mb-1" style="line-height: 1.4; font-size: 15px;">{{ $p->nama_produk }}</h6>
                        
                        <div class="mt-auto pt-2 border-top" style="border-color: #F7E5EA !important;">
                            <a href="https://instagram.com/yourbaestyle" target="_blank" class="text-decoration-none fw-bold" style="color: #4D3D43; font-size: 12px;">
                                <i class="bi bi-instagram me-1 text-danger"></i> Pantau Live IG
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-box2-heart fs-1 mb-3 d-block" style="color: #F6C9D3;"></i>
                <h5 class="fw-bold text-dark">Koleksi Sedang Kosong</h5>
                <p class="text-muted">Nantikan update barang thrift dan rebranding kami selanjutnya!</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
            {{ $produks->links('pagination::bootstrap-5') }}
        </div>
    </div>

</body>
</html>