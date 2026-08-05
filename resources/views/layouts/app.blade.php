<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=no">
    <title>@yield('title', 'Dashboard') — Yourbaestyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
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

        /* ========== SIDEBAR ACCORDION STYLING SAJA ========== */
        .sidebar-wrap {
            position: fixed;
            top: 0; left: 0; bottom: 0;
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
            width: 42px; height: 42px;
            border-radius: 16px;
            background: var(--pink-primary);
            display: flex; align-items: center; justify-content: center;
            font-size: 19px; color: #fff;
            flex-shrink: 0; transform: rotate(-6deg);
        }

        .sidebar .brand .brand-text {
            font-family: 'Quicksand', sans-serif;
            font-weight: 700; font-size: 17px;
            color: #fff; line-height: 1.15;
        }

        .sidebar .brand .brand-sub {
            font-size: 10px; color: #E0BFC6; letter-spacing: 0.3px;
        }

        .sidebar nav { padding: 10px 16px; flex-grow: 1; overflow-y: auto; }

        .sidebar .nav-link, .sidebar .nav-parent {
            color: #E8D2D6; padding: 11px 16px;
            font-size: 13.5px; font-weight: 600;
            display: flex; align-items: center; justify-content: space-between;
            border-radius: 18px; margin-bottom: 5px;
            transition: all 0.2s ease; border: none;
            background: transparent; text-decoration: none;
            cursor: pointer; width: 100%;
        }

        .sidebar .nav-link-left { display: flex; align-items: center; gap: 12px; }
        .sidebar .nav-link i, .sidebar .nav-parent i { font-size: 16px; width: 18px; text-align: center; opacity: 0.8; }
        
        .sidebar .nav-parent .chevron-icon { font-size: 11px; transition: transform 0.25s ease; opacity: 0.6; }
        .sidebar .nav-parent[aria-expanded="true"] .chevron-icon { transform: rotate(90deg); opacity: 1; color: #fff; }

        .sidebar .nav-link:hover, .sidebar .nav-parent:hover { background: rgba(255, 255, 255, 0.10); color: #fff; }
        .sidebar .nav-parent[aria-expanded="true"] { background: rgba(0, 0, 0, 0.15); color: #fff; }

        .sidebar .nav-link.active {
            background: #fff; color: var(--pink-primary-dark);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15); font-weight: 700;
        }
        .sidebar .nav-link.active i { opacity: 1; color: var(--pink-primary-dark); }

        .sidebar .submenu-box {
            background: rgba(0, 0, 0, 0.12); border-radius: 14px;
            padding: 4px 6px; margin-top: -2px; margin-bottom: 6px;
            border-left: 2px solid var(--pink-primary); margin-left: 10px;
        }

        .sidebar .submenu-link {
            padding: 9px 12px 9px 32px; font-size: 13px; color: #D6C2C7;
            display: flex; align-items: center; text-decoration: none;
            border-radius: 12px; transition: 0.15s; position: relative; margin-bottom: 2px;
        }

        .sidebar .submenu-link::before {
            content: ''; position: absolute; left: 14px; width: 4px; height: 4px;
            border-radius: 50%; background: rgba(255, 255, 255, 0.4); transition: 0.2s;
        }

        .sidebar .submenu-link:hover { color: #fff; background: rgba(255, 255, 255, 0.08); }
        .sidebar .submenu-link:hover::before { background: #fff; transform: scale(1.3); }

        .sidebar .submenu-link.active {
            color: #fff; background: #fff; color: var(--pink-primary-dark);
            font-weight: 700; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .sidebar .submenu-link.active::before { background: var(--pink-primary-dark); width: 5px; height: 5px; }

        .sidebar .sidebar-footer { padding: 18px; flex-shrink: 0; border-top: 1px solid rgba(255, 255, 255, 0.1); }
        .btn-logout {
            background: rgba(255, 255, 255, 0.10); color: #F0DCE0; border: none;
            border-radius: 18px; padding: 11px; width: 100%; font-size: 13px;
            font-weight: 600; cursor: pointer; transition: 0.2s;
        }
        .btn-logout:hover { background: rgba(255, 255, 255, 0.18); color: #fff; }

        /* ========== KONTEN UTAMA TETAP SEPERTI SEMULA (TIDAK MENGUBAH STYLE HALAMAN LAIN) ========== */
        .sidebar-overlay {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.4); z-index: 1040; transition: opacity 0.3s ease;
        }
        .sidebar-overlay.show { display: block; opacity: 1; }

        .main-content {
            margin-left: 280px; padding: 24px 30px; min-height: 100vh; transition: margin-left 0.3s ease;
        }

        .topbar {
            background: var(--pink-card); border-radius: var(--radius-xl);
            padding: 16px 26px; margin-bottom: 24px; display: flex;
            justify-content: space-between; align-items: center;
            box-shadow: 0 8px 26px rgba(216, 150, 165, 0.12);
            position: sticky; top: 0; z-index: 100;
        }
        .topbar h5 { font-weight: 700; margin: 0; color: var(--ink); font-family: 'Quicksand', sans-serif; font-size: 18px; }
        .topbar-left { display: flex; align-items: center; gap: 12px; }

        .btn-menu-toggle {
            background: var(--pink-soft); border: none; color: var(--pink-primary-dark);
            border-radius: 14px; width: 40px; height: 40px; display: none;
            align-items: center; justify-content: center; cursor: pointer; font-size: 18px; transition: 0.2s;
        }
        .btn-menu-toggle:hover { background: var(--pink-primary-soft); color: var(--pink-primary-dark); }
        .btn-close-sidebar { display: none !important; }

        .badge-role {
            font-size: 10.5px; padding: 5px 14px; border-radius: 30px;
            font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;
        }
        .badge-owner { background: var(--amber-soft); color: #A07424; }
        .badge-admin { background: var(--pink-soft); color: var(--pink-primary-dark); }
        .badge-staf  { background: var(--sage-soft); color: #56794D; }

        .user-chip { display: flex; align-items: center; gap: 10px; }
        .user-avatar {
            width: 38px; height: 38px; border-radius: 14px; background: var(--pink-soft);
            display: flex; align-items: center; justify-content: center; font-weight: 700;
            color: var(--pink-primary-dark); font-size: 14px; transform: rotate(-4deg);
        }

        .card-yb { background: var(--pink-card); border: none; border-radius: var(--radius-xl); box-shadow: 0 8px 26px rgba(216, 150, 165, 0.10); }
        .stat-card {
            background: var(--pink-card); border-radius: var(--radius-xl); padding: 22px 24px;
            box-shadow: 0 8px 26px rgba(216, 150, 165, 0.10); position: relative; overflow: hidden; transition: 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 14px 32px rgba(216, 150, 165, 0.18); }
        .stat-card .stat-icon {
            width: 44px; height: 44px; border-radius: 16px; display: flex;
            align-items: center; justify-content: center; font-size: 19px; margin-bottom: 12px; transform: rotate(-5deg);
        }
        .stat-card .stat-label { font-size: 12.5px; color: var(--ink-soft); margin-bottom: 3px; font-weight: 600; }
        .stat-card .stat-value { font-size: 23px; font-weight: 700; font-family: 'Quicksand', sans-serif; color: var(--ink); }

        .icon-pink  { background: var(--pink-soft); color: var(--pink-primary-dark); }
        .icon-sage  { background: var(--sage-soft); color: #56794D; }
        .icon-amber { background: var(--amber-soft); color: #A07424; }

        .btn-yb { background: var(--pink-primary); color: #fff; border: none; border-radius: 30px; font-weight: 700; padding: 10px 22px; }
        .btn-yb:hover { background: var(--pink-primary-dark); color: #fff; transform: translateY(-1px); }
        .btn-yb-outline { background: #fff; color: var(--pink-primary-dark); border: 2px solid var(--pink-soft); border-radius: 30px; font-weight: 700; padding: 9px 20px; }
        .btn-yb-outline:hover { background: var(--pink-soft); border-color: var(--pink-primary); }
        .btn-yb-excel { background: #52976D; color: #fff; border: none; border-radius: 30px; font-weight: 700; padding: 10px 22px; transition: all 0.2s ease; }
        .btn-yb-excel:hover { background: #417B57; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(82, 151, 109, 0.25); }
        .btn, .btn-sm { border-radius: 16px !important; }

        .table-yb { border-collapse: separate; border-spacing: 0 6px; }
        .table-yb thead th {
            font-size: 11.5px; color: var(--ink-soft); text-transform: uppercase;
            letter-spacing: 0.4px; border: none; font-weight: 700; padding-bottom: 10px; background: transparent;
        }
        .table-yb tbody tr { background: var(--pink-soft-2); }
        .table-yb tbody tr:hover { background: var(--pink-soft); }
        .table-yb tbody td { font-size: 13.5px; padding: 13px 14px; border: none; color: var(--ink); }
        .table-yb tbody tr td:first-child { border-radius: 14px 0 0 14px; }
        .table-yb tbody tr td:last-child { border-radius: 0 14px 14px 0; }

        .form-control, .form-select {
            border: 2px solid var(--border-soft); border-radius: 16px; padding: 10px 16px; font-size: 13.5px; background: var(--pink-soft-2);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--pink-primary); box-shadow: 0 0 0 4px rgba(236, 149, 168, 0.15); background: #fff;
        }
        .form-label { font-size: 12.5px; font-weight: 700; color: var(--ink); margin-bottom: 6px; }

        .alert { border-radius: var(--radius-lg) !important; border: none !important; padding: 14px 20px; }
        .alert-success { background: var(--sage-soft); color: #56794D; }
        .alert-danger  { background: #FCEAEA; color: #AD5050; }
        .alert-info    { background: var(--pink-soft); color: var(--pink-primary-dark); }

        .badge { border-radius: 30px !important; padding: 5px 12px !important; font-weight: 600 !important; }
        .badge.bg-light { background: var(--pink-soft-2) !important; color: var(--ink-soft) !important; border: 1.5px solid var(--border-soft) !important; }
        .badge.bg-success { background: var(--sage-soft) !important; color: #56794D !important; }
        .badge.bg-danger  { background: #FCEAEA !important; color: #AD5050 !important; }
        .badge.bg-warning { background: var(--amber-soft) !important; color: #A07424 !important; }

        @media (max-width: 1024px) {
            .main-content { margin-left: 0; padding: 16px 20px; }
            .sidebar-wrap { transform: translateX(-100%); width: 260px; border-radius: 0; }
            .sidebar-wrap.show { transform: translateX(0); }
            .btn-menu-toggle { display: flex; }
            .btn-close-sidebar { display: block; }
            .sidebar-overlay.show { display: block; }
            .topbar { padding: 12px 16px; }
            .topbar h5 { font-size: 16px; }
            .user-chip { display: none; }
            .badge-role { font-size: 9px; padding: 4px 10px; }
        }
        /* FIX UKURAN TAMPILAN MOBILE AGAR TIDAK TERLALU BESAR */
    @media (max-width: 768px) {
        .main-content {
            padding: 12px 14px !important;
        }
        
        .topbar {
            padding: 12px 16px !important;
            margin-bottom: 16px !important;
        }
        
        .topbar h5 {
            font-size: 15px !important;
        }

        /* Mengecilkan ukuran stat-card di HP */
        .stat-card, div[style*="border-radius: 16px"] {
            padding: 16px !important;
        }
        
        .stat-card .stat-value, .stat-value {
            font-size: 22px !important;
        }
        
        /* Menyesuaikan ukuran ikon dalam card */
        .stat-card div[style*="font-size: 32px"] {
            font-size: 24px !important;
        }
    }

        .sidebar::-webkit-scrollbar, .sidebar nav::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track, .sidebar nav::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); }
        .sidebar::-webkit-scrollbar-thumb, .sidebar nav::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 4px; }
    </style>
    @stack('styles')
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ============================================================ -->
    <!-- SIDEBAR ACCORDION BARU -->
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
                @php 
                    $currentPath = '/' . request()->path(); 
                    $activeMenus = auth()->user()->menuAktif();
                    
                    // Struktur 6 Kelompok Menu Lipat (Accordion) yang rapi
                    $menuStructure = [
                        [
                            'type' => 'single',
                            'title' => 'Dashboard',
                            'icon' => 'bi-grid-1x2-fill',
                            'url' => '/dashboard'
                        ],
                        [
                            'type' => 'group',
                            'title' => 'Inventaris & Produk',
                            'icon' => 'bi-box-seam',
                            'id' => 'inventaris',
                            'urls' => ['/produk', '/pemasok', '/retur']
                        ],
                        [
                            'type' => 'group',
                            'title' => 'Kasir & Transaksi',
                            'icon' => 'bi-cart-check',
                            'id' => 'kasir',
                            'urls' => ['/pos', '/transaksi']
                        ],
                        [
                            'type' => 'group',
                            'title' => 'Online & Live Kode',
                            'icon' => 'bi-broadcast',
                            'id' => 'online',
                            'urls' => ['/pesanan', '/sesi-live']
                        ],
                        [
                            'type' => 'single',
                            'title' => 'Laporan ',
                            'icon' => 'bi-file-earmark-text',
                            'url' => '/laporan'
                        ],
                        [
                            'type' => 'group',
                            'title' => 'Pengaturan ',
                            'icon' => 'bi-shield-lock',
                            'id' => 'pengaturan',
                            'urls' => ['/pengaturan/role', '/pengaturan/user']
                        ]
                    ];
                @endphp

                @foreach($menuStructure as $item)
                    @if($item['type'] === 'single')
                        @php
                            $singleMenu = $activeMenus->firstWhere('target_url', $item['url']);
                        @endphp
                        @if($singleMenu)
                            <a href="{{ $singleMenu->target_url }}"
                               class="nav-link {{ str_starts_with($currentPath, $singleMenu->target_url) ? 'active' : '' }}"
                               onclick="closeSidebarOnMobile()">
                                <div class="nav-link-left">
                                    <i class="bi {{ $item['icon'] }}"></i>
                                    <span>{{ $item['title'] }}</span>
                                </div>
                            </a>
                        @endif

                    @elseif($item['type'] === 'group')
                        @php
                            $children = $activeMenus->filter(function($m) use ($item) {
                                return in_array($m->target_url, $item['urls']);
                            });

                            $isGroupActive = $children->contains(function($m) use ($currentPath) {
                                return str_starts_with($currentPath, $m->target_url);
                            });
                        @endphp

                        @if($children->isNotEmpty())
                            <div class="nav-parent" 
                                 data-bs-toggle="collapse" 
                                 data-bs-target="#collapse-{{ $item['id'] }}" 
                                 aria-expanded="{{ $isGroupActive ? 'true' : 'false' }}">
                                <div class="nav-link-left">
                                    <i class="bi {{ $item['icon'] }}"></i>
                                    <span>{{ $item['title'] }}</span>
                                </div>
                                <i class="bi bi-chevron-right chevron-icon"></i>
                            </div>

                            <div class="collapse {{ $isGroupActive ? 'show' : '' }}" id="collapse-{{ $item['id'] }}">
                                <div class="submenu-box">
                                    @foreach($children as $child)
                                        @php
                                            $labelChild = $child->nama_menu === 'Manajemen Barang' ? 'Manajemen Produk' : $child->nama_menu;
                                        @endphp
                                        <a href="{{ $child->target_url }}"
                                           class="submenu-link {{ str_starts_with($currentPath, $child->target_url) ? 'active' : '' }}"
                                           onclick="closeSidebarOnMobile()">
                                            <span>{{ $labelChild }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                @endforeach
            </nav>
            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    @method('POST')
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

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            
            if (sidebar.classList.contains('show')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        }

        function closeSidebarOnMobile() {
            if (window.innerWidth < 1024) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        }

        document.getElementById('sidebarOverlay').addEventListener('click', toggleSidebar);

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && window.innerWidth < 1024) {
                closeSidebarOnMobile();
            }
        });
    </script>

    @stack('scripts')
    <script src="{{ asset('js/notifications.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any())
            @foreach($errors->all() as $error)
                if (window.notify) notify.error('{!! addslashes($error) !!}', 'Validasi Error');
            @endforeach
        @endif

        @if(session('success'))
            if (window.notify) notify.success('{!! addslashes(session('success')) !!}', 'Berhasil!');
        @endif

        @if(session('error'))
            if (window.notify) notify.error('{!! addslashes(session('error')) !!}', 'Gagal!');
        @endif

        @if(session('warning'))
            if (window.notify) notify.warning('{!! addslashes(session('warning')) !!}', 'Perhatian!');
        @endif

        @if(session('info'))
            if (window.notify) notify.info('{!! addslashes(session('info')) !!}', 'Informasi');
        @endif
    });
</script>
</body>
</html>