<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Yourbaestyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Nunito', sans-serif; }
        
        body {
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            background: linear-gradient(135deg, #FDE3E9 0%, #FDF1F4 50%, #FCF1E3 100%);
            position: relative; 
            overflow: hidden;
            padding: 20px 16px; /* Memberi jarak aman di semua ukuran layar */
        }
        
        .deco { position: absolute; border-radius: 50%; opacity: .55; pointer-events: none; }
        .deco-1 { width: 300px; height: 300px; background: #F6C9D3; top: -100px; left: -90px; }
        .deco-2 { width: 240px; height: 240px; background: #EAD7A8; bottom: -80px; right: -70px; }
        .deco-3 { width: 120px; height: 120px; background: #C7DBC1; bottom: 60px; left: 80px; opacity: .4; }
        .deco-4 { width: 60px; height: 60px; background: #EC95A8; top: 80px; right: 120px; opacity: .3; }

        .login-card {
            background: #fff; 
            border-radius: 34px; 
            padding: 46px 40px; 
            width: 100%; 
            max-width: 400px;
            box-shadow: 0 24px 60px rgba(216, 150, 165, .3); 
            position: relative; 
            z-index: 2;
        }

        .brand-icon {
            width: 62px; 
            height: 62px; 
            border-radius: 22px; 
            background: #EC95A8;
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 28px; 
            color: #fff;
            margin: 0 auto 16px; 
            box-shadow: 0 10px 24px rgba(236, 149, 168, .4); 
            transform: rotate(-6deg);
        }
        
        .brand h3 { font-family: 'Quicksand', sans-serif; font-weight: 700; color: #4D3D43; margin-bottom: 2px; }
        .brand p { color: #9A8A8E; font-size: 13px; font-weight: 600; }

        .form-control { 
            border: 2px solid #F7E5EA; 
            border-radius: 18px; 
            padding: 11px 16px; 
            font-size: 13.5px; 
            background: #FFF5F7; 
        }
        .form-control:focus { 
            border-color: #EC95A8; 
            box-shadow: 0 0 0 4px rgba(236, 149, 168, .15); 
            background: #fff; 
        }
        .form-label { font-size: 12.5px; font-weight: 700; color: #4D3D43; }

        .btn-login {
            background: #EC95A8; 
            color: #fff; 
            border-radius: 30px; 
            padding: 12px; 
            font-weight: 700; 
            width: 100%;
            font-family: 'Quicksand', sans-serif; 
            font-size: 14.5px; 
            border: none;
            transition: all 0.2s ease;
        }
        .btn-login:hover { background: #DD7991; color: #fff; transform: translateY(-1px); }
        
        .alert-danger { 
            background: #FCEAEA; 
            color: #AD5050; 
            border: none; 
            border-radius: 16px; 
            font-size: 13px; 
            font-weight: 600; 
        }

        /* --- OPTIMASI KHUSUS LAYAR HP (MOBILE RESPONSIVE) --- */
        @media (max-width: 480px) {
            body {
                padding: 16px 12px; /* Jarak tepi layar lebih rapat di HP */
            }
            .login-card {
                padding: 32px 22px; /* Padding dalam dikurangi agar form lebih lebar dan lega */
                border-radius: 28px;
            }
            .brand-icon {
                width: 54px; 
                height: 54px; 
                font-size: 24px;
                border-radius: 18px;
                margin-bottom: 12px;
            }
            .brand h3 { font-size: 22px; }
            .brand p { font-size: 12px; }
            .form-control { font-size: 14px; padding: 10px 14px; } /* Font 14px mencegah auto-zoom di iPhone */
        }
    </style>
</head>
<body>
    <div class="deco deco-1"></div>
    <div class="deco deco-2"></div>
    <div class="deco deco-3"></div>
    <div class="deco deco-4"></div>

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-icon"><i class="bi bi-bag-heart-fill"></i></div>
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
                       placeholder="nama@yourbaestyle.com" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label small text-secondary fw-semibold" for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>
        <p class="text-center text-secondary mt-3 mb-0 fw-semibold" style="font-size: 11px;">
            Hak akses ditentukan berdasarkan peran yang ditetapkan Owner
        </p>
    </div>
</body>
</html>