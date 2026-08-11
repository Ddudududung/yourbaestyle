@extends('layouts.app')

@section('title', 'Kelola Role & Hak Akses — Yourbaestyle')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    /* Styling khusus Select2 agar serasi dengan tema Yourbaestyle */
    .select2-container--bootstrap-5 .select2-selection {
        border-color: var(--border-soft) !important;
        border-radius: 14px !important;
        padding: 0.55rem 0.85rem !important;
        background-color: var(--pink-soft-2) !important;
        font-weight: 700 !important;
        color: var(--ink) !important;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: var(--pink-primary) !important;
        box-shadow: 0 0 0 0.25rem rgba(236, 149, 168, 0.25) !important;
    }
    .select2-dropdown {
        border-color: var(--border-soft) !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
        overflow: hidden !important;
    }
    .select2-search__field {
        border-radius: 10px !important;
        border: 1.5px solid var(--border-soft) !important;
        padding: 8px 12px !important;
    }
    .select2-search__field:focus {
        border-color: var(--pink-primary) !important;
        outline: none !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: var(--pink-primary) !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-2" style="max-width: 1200px; margin: 0 auto;">

    <!-- ============================================================ -->
    <!-- HEADER SECTION -->
    <!-- ============================================================ -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 p-3 rounded-4" style="background: #ffffff; border: 1px solid var(--border-soft); box-shadow: 0 4px 20px rgba(236,149,168,0.08);">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: var(--pink-soft); color: var(--pink-primary);">
                <i class="bi bi-shield-lock-fill fs-4"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-1" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">Kelola Role & Hak Akses</h5>
                <p class="text-muted small mb-0">Cari & pilih role terdaftar di bawah untuk menyesuaikan wewenang hak akses menu.</p>
            </div>
        </div>
        <div>
            <span class="badge rounded-pill px-3 py-2" style="background: var(--pink-soft-2); color: var(--ink); border: 1px solid var(--border-soft);">
                <i class="bi bi-shield-check me-1 text-pink" style="color: var(--pink-primary);"></i> Total {{ $roles->count() }} Role Terdaftar
            </span>
        </div>
    </div>


    <!-- ============================================================ -->
    <!-- CONTENT GRID -->
    <!-- ============================================================ -->
    <div class="row g-4">
        
        <!-- SIDEBAR KIRI: SELECT2 SEARCHABLE DROPDOWN & FORM EDIT PERMISSION -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" id="form_role_card" style="background: #ffffff; border: 1px solid var(--border-soft) !important;">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between" style="border-color: var(--border-soft) !important;">
                    <h6 class="fw-bold mb-0" id="form_card_title" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                        <i class="bi bi-sliders me-2" style="color: var(--pink-primary);"></i>Kelola Hak Akses Role
                    </h6>
                </div>
                <div class="card-body p-4">
                    
                    <!-- 1. SELECT2 SEARCHABLE DROPDOWN -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small" style="color: var(--ink);">
                            Pilih Role Terdaftar <span class="text-danger">*</span>
                        </label>
                        <select id="selectRoleSearch" class="form-select" style="width: 100%;">
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}" 
                                        data-name="{{ $r->nama_role }}" 
                                        data-active="{{ $r->is_active ? '1' : '0' }}" 
                                        data-menus="{{ $r->menus->pluck('id')->join(',') }}"
                                        {{ $loop->first ? 'selected' : '' }}>
                                    🛡️ {{ ucfirst($r->nama_role) }} — ({{ count($r->menus) }} menu diizinkan)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <hr class="my-3" style="border-color: var(--border-soft);">

                    <!-- 2. FORM HAK AKSES ROLE -->
                    <form id="role_update_form" action="" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- NAMA ROLE HIDDEN INPUT -->
                        <input type="hidden" name="nama_role" id="input_nama_role">
                        <!-- HAK AKSES MENU SELECTION -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold small mb-0" style="color: var(--ink);">Hak Akses Menu <span class="text-danger">*</span></label>
                                <div>
                                    <button type="button" class="btn btn-link p-0 text-decoration-none small me-2 fw-semibold" onclick="toggleAllMenus(true)" style="font-size: 11px; color: var(--pink-primary);">Pilih Semua</button>
                                    <button type="button" class="btn btn-link p-0 text-decoration-none small text-muted fw-semibold" onclick="toggleAllMenus(false)" style="font-size: 11px;">Reset</button>
                                </div>
                            </div>
                            <div class="p-3 rounded-4" style="max-height: 300px; overflow-y: auto; background: var(--pink-soft-2); border: 1px dashed var(--border-soft);">
                                @foreach($menus as $menu)
                                    <div class="form-check mb-2 p-2 rounded-3 hover-bg-white transition-all">
                                        <input type="checkbox" name="menu_ids[]" value="{{ $menu->id }}" id="menu_item_{{ $menu->id }}" class="form-check-input check-menu-item" style="cursor: pointer;">
                                        <label for="menu_item_{{ $menu->id }}" class="form-check-label small fw-semibold text-dark d-flex align-items-center gap-2" style="cursor: pointer;">
                                            <i class="bi bi-grid-fill text-pink" style="color: var(--pink-primary); font-size: 13px;"></i>
                                            {{ $menu->nama_menu }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" id="btn_submit_role" class="btn btn-yb w-100 rounded-pill py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-check-circle-fill"></i> Simpan Hak Akses Role
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: DAFTAR ROLE SISTEM -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: #ffffff; border: 1px solid var(--border-soft) !important;">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center" style="border-color: var(--border-soft) !important;">
                    <h6 class="fw-bold mb-0" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                        <i class="bi bi-shield-shaded me-2" style="color: var(--pink-primary);"></i>Daftar Role Sistem
                    </h6>
                </div>
                <div class="card-body p-4">

                    @foreach($roles as $role)
                        @php
                            $assignedMenuIds = $role->menus->pluck('id')->toArray();
                        @endphp
                        <div class="card mb-3 border-0 rounded-4 transition-all" style="background: #ffffff; border: 1px solid var(--border-soft) !important; box-shadow: 0 2px 12px rgba(0,0,0,0.02);">
                            
                            <!-- HEADER ROLE ITEM -->
                            <div class="card-header bg-white py-3 px-4 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2 rounded-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: var(--pink-soft-2); border: 1px solid var(--border-soft);">
                                        <i class="bi bi-person-badge fs-5" style="color: var(--pink-primary);"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 class="fw-bold text-capitalize mb-0" style="color: var(--ink); font-family: 'Quicksand', sans-serif; font-size: 16px;">
                                                {{ $role->nama_role }}
                                            </h6>
                                            @if($role->is_active)
                                                <span class="badge rounded-pill" style="background: var(--sage-soft); color: #388E3C; border: 1px solid #C8E6C9; font-size: 10px;">
                                                    <i class="bi bi-dot me-1"></i>Aktif
                                                </span>
                                            @else
                                                <span class="badge rounded-pill" style="background: #F5F5F5; color: #757575; border: 1px solid #E0E0E0; font-size: 10px;">
                                                    <i class="bi bi-dot me-1"></i>Nonaktif
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-muted" style="font-size: 12px;">
                                            <i class="bi bi-check-all me-1 text-pink" style="color: var(--pink-primary);"></i>{{ count($assignedMenuIds) }} dari {{ $menus->count() }} menu diizinkan
                                        </small>
                                    </div>
                                </div>

                                <div>
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-flex align-items-center gap-1" 
                                            type="button" 
                                            onclick="selectRoleInSelect2('{{ $role->id }}')">
                                        <i class="bi bi-pencil-square"></i> Edit Hak Akses
                                    </button>
                                </div>
                            </div>

                            <!-- RINGKASAN MENU BADGES -->
                            <div class="card-body px-4 pt-0 pb-3">
                                <div class="d-flex flex-wrap gap-1.5 mt-1">
                                    @forelse($role->menus as $m)
                                        <span class="badge rounded-pill fw-normal px-2.5 py-1.5" style="background: var(--pink-soft-2); color: var(--ink); border: 1px solid var(--border-soft); font-size: 11px;">
                                            <i class="bi bi-check-circle-fill me-1" style="color: var(--pink-primary);"></i>{{ $m->nama_menu }}
                                        </span>
                                    @empty
                                        <span class="text-muted italic small"><i class="bi bi-info-circle me-1"></i>Belum ada menu yang diizinkan untuk role ini.</span>
                                    @endforelse
                                </div>
                            </div>

                        </div>
                    @endforeach

                </div>
            </div>
        </div>

    </div>

</div>

<style>
    .transition-all { transition: all 0.25s ease-in-out; }
    .hover-bg-white:hover { background-color: #ffffff !important; }
    .form-check-input:checked { background-color: var(--pink-primary); border-color: var(--pink-primary); }
</style>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    const updateUrlBase = "{{ url('/pengaturan/role') }}";

    function toggleAllMenus(checked) {
        document.querySelectorAll('.check-menu-item').forEach(cb => cb.checked = checked);
    }

    function selectRoleInSelect2(roleId) {
        $('#selectRoleSearch').val(roleId).trigger('change');
        document.getElementById('form_role_card').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    $(document).ready(function() {
        // 1. Inisialisasi Select2 dengan Theme Bootstrap-5 & Search Box
        const $selectRole = $('#selectRoleSearch').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Ketik Nama / Pilih Role --',
            allowClear: false,
            width: '100%'
        });

        const form = document.getElementById('role_update_form');
        const inputNama = document.getElementById('input_nama_role');
        const inputActive = document.getElementById('input_is_active');
        const labelActive = document.getElementById('label_is_active');
        const cardTitle = document.getElementById('form_card_title');
        const btnSubmit = document.getElementById('btn_submit_role');

        function loadRoleData() {
            const roleId = $selectRole.val();
            if (!roleId) return;

            const selectedOption = $selectRole.find(':selected');
            const roleName = selectedOption.data('name') || '';
            const isActive = selectedOption.data('active') == '1';
            const menusArr = (selectedOption.data('menus') || '').toString().split(',').map(s => s.trim());

            // Set Form Action
            form.action = `${updateUrlBase}/${roleId}`;
            inputNama.value = roleName;
            inputActive.checked = isActive;
            if (labelActive) labelActive.textContent = isActive ? 'Role Aktif' : 'Role Nonaktif';

            cardTitle.innerHTML = `<i class="bi bi-pencil-square me-2" style="color: var(--pink-primary);"></i>Kelola Hak Akses: ${roleName.toUpperCase()}`;
            btnSubmit.innerHTML = `<i class="bi bi-check-circle-fill"></i> Simpan Hak Akses ${roleName}`;

            // Centang menu yang sesuai
            document.querySelectorAll('.check-menu-item').forEach(cb => {
                cb.checked = menusArr.includes(cb.value);
            });
        }

        // Listener saat Select2 berubah
        $selectRole.on('change', function() {
            loadRoleData();
        });

        // Trigger load awal
        loadRoleData();

        if (inputActive) {
            inputActive.addEventListener('change', function() {
                if (labelActive) labelActive.textContent = this.checked ? 'Role Aktif' : 'Role Nonaktif';
            });
        }
    });
</script>
@endpush
@endsection
