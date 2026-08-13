<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Yourbaestyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>
<body class="login-body">
    <div class="deco deco-1"></div>
    <div class="deco deco-2"></div>
    <div class="deco deco-3"></div>
    <div class="deco deco-4"></div>

    <div class="login-card">
        <div class="mb-2">
            <a href="{{ route('katalog.publik') }}" class="text-decoration-none font-semibold text-secondary small d-inline-flex align-items-center gap-1" style="transition: all 0.2s ease;">
                <i class="bi bi-arrow-left" style="color: #EC95A8; font-size: 14px;"></i> <span>Kembali ke Katalog</span>
            </a>
        </div>

        <div class="text-center mb-4">
            <div class="brand-logo-wrapper mx-auto mb-3" style="width: 72px; height: 72px; border-radius: 50%; overflow: hidden; box-shadow: 0 6px 16px rgba(0,0,0,0.12); border: 3px solid #FFDCED;">
                <img src="{{ asset('images/logo.svg') }}" alt="Yourbaestyle Logo" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
            </div>
            <div class="brand">
                <h3>Yourbaestyle</h3>
                <p>manage with love 🌸</p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                       placeholder="" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label small text-secondary fw-semibold" for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>
        
        <div class="text-center mt-3 pt-2 border-top">
            <a href="{{ route('katalog.publik') }}" class="text-decoration-none fw-bold" style="color: #EC95A8; font-size: 12px;">
                <i class="bi bi-shop me-1"></i> Salah Pencet? Kembali Lihat Katalog
            </a>
        </div>
        <p class="text-center text-secondary mt-2 mb-0 fw-semibold" style="font-size: 11px;">
            Hak akses ditentukan berdasarkan peran yang ditetapkan Owner
        </p>
    </div>
</body>
</html>