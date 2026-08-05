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
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
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
                    <div class="brand-sub">manage with love </div>
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

    // AUTO-FORMATTER RUPIAH DENGAN TITIK (TANPA KOMA & BEBAS BUG DESIMAL FLOAT)
    function formatRupiahDisplay(val) {
        if (val === null || val === undefined || val === '') return '';
        let valStr = val.toString().trim();

        // Tangani float desimal dari PHP/Database (misal 70000.00 atau 123123.00)
        if (valStr.includes('.')) {
            let parts = valStr.split('.');
            if (parts.length === 2 && parts[1].length <= 2) {
                valStr = parts[0];
            }
        }
        if (valStr.includes(',')) {
            let parts = valStr.split(',');
            if (parts.length === 2 && parts[1].length <= 2) {
                valStr = parts[0];
            }
        }

        let cleanDigits = valStr.replace(/\D/g, '');
        if (!cleanDigits) return '';
        return new Intl.NumberFormat('id-ID').format(parseInt(cleanDigits, 10));
    }

    function initRupiahInputs() {
        document.querySelectorAll('.input-rupiah, input[data-type="rupiah"]').forEach(function(el) {
            if (el.value) {
                el.value = formatRupiahDisplay(el.value);
            }
            el.addEventListener('input', function() {
                this.value = formatRupiahDisplay(this.value);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initRupiahInputs();
    });
</script>
</body>
</html>