<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=no">
    <title>@yield('title', 'Dashboard') — Yourbaestyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --pink-bg:      #FDF1F4;
            --pink-card:    #FFFFFF;
            --pink-primary: #EC95A8;
            --pink-primary-dark: #DD7991;
            --pink-soft:    #FDE3E9;
            --pink-soft-2:  #FFF5F7;
            --sage:         #ACC9A4;
            --sage-soft:    #EDF5EA;
            --amber:        #F0C285;
            --amber-soft:   #FCF1E3;
            --ink:          #4D3D43;
            --ink-soft:     #9A8A8E;
            --border-soft:  #F7E5EA;
            --radius-xl: 28px;
            --radius-lg: 22px;
            --radius-md: 16px;
            --radius-sm: 12px;
        }
        
        * { font-family: 'Nunito', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-display { font-family: 'Quicksand', sans-serif; }
        
        html, body { 
            height: 100%; 
            margin: 0; 
            padding: 0;
            overflow-x: hidden;
        }
        body { background: var(--pink-bg); color: var(--ink); }

        /* ========== SIDEBAR — Desktop & Mobile Responsive ========== */
        .sidebar-wrap {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
            z-index: 1050;
            transition: transform 0.3s ease;
        }

        .sidebar {
            height: 100%;
            background: linear-gradient(165deg, #6B4750 0%, #4A3138 100%);
            display: flex;
            flex-direction: column;
            box-shadow: 0 18px 45px rgba(187, 109, 128, 0.28);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar .brand {
            padding: 28px 22px 18px;
            display: flex;
            align-items: center;
            gap: 11px;
            flex-shrink: 0;
        }

        .sidebar .brand .brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            background: var(--pink-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            color: #fff;
            flex-shrink: 0;
            transform: rotate(-6deg);
        }

        .sidebar .brand .brand-text {
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
            font-size: 17px;
            color: #fff;
            line-height: 1.15;
        }

        .sidebar .brand .brand-sub {
            font-size: 10px;
            color: #E0BFC6;
            letter-spacing: 0.3px;
        }

        .sidebar nav {
            padding: 6px 16px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar .nav-link {
            color: #E8D2D6;
            padding: 11px 16px;
            font-size: 13.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 18px;
            margin-bottom: 5px;
            transition: 0.18s;
            border: none;
            background: transparent;
            text-decoration: none;
            cursor: pointer;
        }

        .sidebar .nav-link i {
            font-size: 16px;
            width: 18px;
            text-align: center;
            opacity: 0.8;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.10);
            color: #fff;
            transform: translateX(2px);
        }

        .sidebar .nav-link.active {
            background: #fff;
            color: var(--pink-primary-dark);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        .sidebar .nav-link.active i {
            opacity: 1;
        }

        .sidebar .sidebar-footer {
            padding: 18px;
            flex-shrink: 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.10);
            color: #F0DCE0;
            border: none;
            border-radius: 18px;
            padding: 11px;
            width: 100%;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
        }

        /* ========== MOBILE OVERLAY ========== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1040;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            margin-left: 280px;
            padding: 24px 30px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .topbar {
            background: var(--pink-card);
            border-radius: var(--radius-xl);
            padding: 16px 26px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 26px rgba(216, 150, 165, 0.12);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar h5 {
            font-weight: 700;
            margin: 0;
            color: var(--ink);
            font-family: 'Quicksand', sans-serif;
            font-size: 18px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-menu-toggle {
            background: var(--pink-soft);
            border: none;
            color: var(--pink-primary-dark);
            border-radius: 14px;
            width: 40px;
            height: 40px;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            transition: 0.2s;
        }

        .btn-menu-toggle:hover {
            background: var(--pink-primary-soft);
            color: var(--pink-primary-dark);
        }

        .btn-close-sidebar, 
.sidebar-close, 
#close-sidebar-btn {
    display: none !important;
}

        .badge-role {
            font-size: 10.5px;
            padding: 5px 14px;
            border-radius: 30px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-owner { background: var(--amber-soft); color: #A07424; }
        .badge-admin { background: var(--pink-soft); color: var(--pink-primary-dark); }
        .badge-staf  { background: var(--sage-soft); color: #56794D; }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            background: var(--pink-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--pink-primary-dark);
            font-size: 14px;
            transform: rotate(-4deg);
        }

        /* ========== CARDS ========== */
        .card-yb {
            background: var(--pink-card);
            border: none;
            border-radius: var(--radius-xl);
            box-shadow: 0 8px 26px rgba(216, 150, 165, 0.10);
        }

        .stat-card {
            background: var(--pink-card);
            border-radius: var(--radius-xl);
            padding: 22px 24px;
            box-shadow: 0 8px 26px rgba(216, 150, 165, 0.10);
            position: relative;
            overflow: hidden;
            transition: 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 32px rgba(216, 150, 165, 0.18);
        }

        .stat-card .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            margin-bottom: 12px;
            transform: rotate(-5deg);
        }

        .stat-card .stat-label {
            font-size: 12.5px;
            color: var(--ink-soft);
            margin-bottom: 3px;
            font-weight: 600;
        }

        .stat-card .stat-value {
            font-size: 23px;
            font-weight: 700;
            font-family: 'Quicksand', sans-serif;
            color: var(--ink);
        }

        .icon-pink  { background: var(--pink-soft); color: var(--pink-primary-dark); }
        .icon-sage  { background: var(--sage-soft); color: #56794D; }
        .icon-amber { background: var(--amber-soft); color: #A07424; }

        /* ========== BUTTONS ========== */
        .btn-yb {
            background: var(--pink-primary);
            color: #fff;
            border: none;
            border-radius: 30px;
            font-weight: 700;
            padding: 10px 22px;
        }

        .btn-yb:hover {
            background: var(--pink-primary-dark);
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-yb-outline {
            background: #fff;
            color: var(--pink-primary-dark);
            border: 2px solid var(--pink-soft);
            border-radius: 30px;
            font-weight: 700;
            padding: 9px 20px;
        }

        .btn-yb-outline:hover {
            background: var(--pink-soft);
            border-color: var(--pink-primary);
        }

        .btn, .btn-sm {
            border-radius: 16px !important;
        }

        /* ========== TABLE ========== */
        .table-yb {
            border-collapse: separate;
            border-spacing: 0 6px;
        }

        .table-yb thead th {
            font-size: 11.5px;
            color: var(--ink-soft);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border: none;
            font-weight: 700;
            padding-bottom: 10px;
            background: transparent;
        }

        .table-yb tbody tr {
            background: var(--pink-soft-2);
        }

        .table-yb tbody tr:hover {
            background: var(--pink-soft);
        }

        .table-yb tbody td {
            font-size: 13.5px;
            padding: 13px 14px;
            border: none;
            color: var(--ink);
        }

        .table-yb tbody tr td:first-child {
            border-radius: 14px 0 0 14px;
        }

        .table-yb tbody tr td:last-child {
            border-radius: 0 14px 14px 0;
        }

        /* ========== FORM ========== */
        .form-control, .form-select {
            border: 2px solid var(--border-soft);
            border-radius: 16px;
            padding: 10px 16px;
            font-size: 13.5px;
            background: var(--pink-soft-2);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--pink-primary);
            box-shadow: 0 0 0 4px rgba(236, 149, 168, 0.15);
            background: #fff;
        }

        .form-label {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 6px;
        }

        /* ========== ALERTS ========== */
        .alert {
            border-radius: var(--radius-lg) !important;
            border: none !important;
            padding: 14px 20px;
        }

        .alert-success { background: var(--sage-soft); color: #56794D; }
        .alert-danger  { background: #FCEAEA; color: #AD5050; }
        .alert-info    { background: var(--pink-soft); color: var(--pink-primary-dark); }

        .badge {
            border-radius: 30px !important;
            padding: 5px 12px !important;
            font-weight: 600 !important;
        }

        .badge.bg-light { background: var(--pink-soft-2) !important; color: var(--ink-soft) !important; border: 1.5px solid var(--border-soft) !important; }
        .badge.bg-success { background: var(--sage-soft) !important; color: #56794D !important; }
        .badge.bg-danger  { background: #FCEAEA !important; color: #AD5050 !important; }
        .badge.bg-warning { background: var(--amber-soft) !important; color: #A07424 !important; }

        /* ========== MOBILE RESPONSIVE ========== */
        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
                padding: 16px 20px;
            }

            .sidebar-wrap {
                transform: translateX(-100%);
                width: 260px;
                border-radius: 0;
            }

            .sidebar-wrap.show {
                transform: translateX(0);
            }

            .btn-menu-toggle {
                display: flex;
            }

            .btn-close-sidebar {
                display: block;
            }

            .sidebar-overlay.show {
                display: block;
            }

            .topbar {
                padding: 12px 16px;
            }

            .topbar h5 {
                font-size: 16px;
            }

            .user-chip {
                display: none;
            }

            .badge-role {
                font-size: 9px;
                padding: 4px 10px;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 12px 16px;
            }

            .topbar {
                margin-bottom: 16px;
                gap: 12px;
            }

            .topbar-left {
                flex: 1;
                gap: 8px;
            }

            .topbar h5 {
                font-size: 14px;
                margin: 0;
            }

            .user-chip {
                display: none;
            }

            .badge-role {
                font-size: 9px;
                padding: 3px 8px;
            }

            .user-avatar {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }

            .stat-card {
                padding: 16px 18px;
                margin-bottom: 12px;
            }

            .stat-card .stat-value {
                font-size: 20px;
            }

            .stat-card .stat-label {
                font-size: 11.5px;
            }

            .table-yb {
                font-size: 12px;
            }

            .table-yb tbody td {
                padding: 10px 8px;
                font-size: 12px;
            }

            .card-yb {
                border-radius: 16px;
            }

            .btn, .btn-sm {
                border-radius: 12px !important;
                font-size: 12px;
                padding: 8px 14px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 10px 12px;
            }

            .topbar {
                padding: 10px 12px;
                margin-bottom: 12px;
                flex-wrap: wrap;
                gap: 8px;
            }

            .topbar-left {
                width: 100%;
                gap: 6px;
            }

            .topbar h5 {
                font-size: 12px;
            }

            .btn-menu-toggle {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }

            .stat-card {
                padding: 12px 14px;
                margin-bottom: 10px;
            }

            .stat-card .stat-icon {
                width: 38px;
                height: 38px;
                font-size: 16px;
            }

            .stat-card .stat-value {
                font-size: 18px;
            }

            .stat-card .stat-label {
                font-size: 11px;
            }

            .table-yb {
                font-size: 11px;
            }

            .table-yb thead th {
                font-size: 9.5px;
            }

            .table-yb tbody td {
                padding: 8px 6px;
                font-size: 11px;
            }
        }

        /* ========== SCROLLBAR ========== */
        .sidebar::-webkit-scrollbar,
        .sidebar nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track,
        .sidebar nav::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar::-webkit-scrollbar-thumb,
        .sidebar nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover,
        .sidebar nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- ============================================================ -->
    <!-- SIDEBAR MOBILE OVERLAY -->
    <!-- ============================================================ -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ============================================================ -->
    <!-- SIDEBAR -->
    <!-- ============================================================ -->
    <div class="sidebar-wrap" id="sidebar">
        <button class="btn-close-sidebar" onclick="toggleSidebar()" title="Close menu">
            <i class="bi bi-x"></i>
        </button>
        <div class="sidebar">
            <div class="brand">
                <div class="brand-icon"><i class="bi bi-bag-heart-fill"></i></div>
                <div>
                    <div class="brand-text">Yourbaestyle</div>
                    <div class="brand-sub">manage with love 🌸</div>
                </div>
            </div>
            <nav>
                @php $currentPath = '/' . request()->path(); @endphp
                @foreach(auth()->user()->menuAktif() as $menu)
                    <a href="{{ $menu->target_url }}"
                       class="nav-link {{ str_starts_with($currentPath, $menu->target_url) ? 'active' : '' }}"
                       onclick="closeSidebarOnMobile()">
                        <i class="bi {{ $menu->icon_url ?? 'bi-circle' }}"></i>
                        {{ $menu->nama_menu }}
                    </a>
                @endforeach
            </nav>
            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-logout"><i class="bi bi-box-arrow-left"></i> Logout</button>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MAIN CONTENT -->
    <!-- ============================================================ -->
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <button class="btn-menu-toggle" onclick="toggleSidebar()" title="Menu">
                    <i class="bi bi-list"></i>
                </button>
                <h5>@yield('title', 'Dashboard')</h5>
            </div>
            <div class="d-flex align-items-center gap-3" style="flex-shrink: 0;">
                <span class="badge-role badge-{{ auth()->user()->role->nama_role }}">
                    {{ auth()->user()->role->nama_role }}
                </span>
                <div class="user-chip">
                    <div class="user-avatar">{{ substr(auth()->user()->nama, 0, 1) }}</div>
                    <span style="font-size: 13.5px; font-weight: 700;">{{ auth()->user()->nama }}</span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ====== SIDEBAR TOGGLE ======
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            
            // Prevent body scroll when sidebar is open on mobile
            if (sidebar.classList.contains('show')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        }

        function closeSidebarOnMobile() {
            // Only close on small screens
            if (window.innerWidth < 1024) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        }

        // Close sidebar when clicking overlay
        document.getElementById('sidebarOverlay').addEventListener('click', toggleSidebar);

        // Close sidebar on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        });

        // Close sidebar on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && window.innerWidth < 1024) {
                closeSidebarOnMobile();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
