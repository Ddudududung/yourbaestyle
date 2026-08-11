@extends('layouts.app')

@section('title', 'Kelola Pengguna — Yourbaestyle')

@section('content')
<div class="container-fluid py-2" style="max-width: 1200px; margin: 0 auto;">

    <!-- ============================================================ -->
    <!-- HEADER SECTION -->
    <!-- ============================================================ -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 p-3 rounded-4" style="background: #ffffff; border: 1px solid var(--border-soft); box-shadow: 0 4px 20px rgba(236,149,168,0.08);">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: var(--pink-soft); color: var(--pink-primary);">
                <i class="bi bi-people-fill fs-4"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-1" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">Kelola Akun Pengguna</h5>
                <p class="text-muted small mb-0">Kelola akun pengguna, penetapan role, dan kata sandi akses aplikasi Yourbaestyle.</p>
            </div>
        </div>
        <div>
            <span class="badge rounded-pill px-3 py-2" style="background: var(--pink-soft-2); color: var(--ink); border: 1px solid var(--border-soft);">
                <i class="bi bi-person-check me-1" style="color: var(--pink-primary);"></i> Total {{ $users->count() }} Pengguna Terdaftar
            </span>
        </div>
    </div>


    <!-- ============================================================ -->
    <!-- CONTENT GRID -->
    <!-- ============================================================ -->
    <div class="row g-4">
        
        <!-- SIDEBAR KIRI: FORM TAMBAH AKUN PENGGUNA -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: #ffffff; border: 1px solid var(--border-soft) !important;">
                <div class="card-header bg-white py-3 px-4 border-bottom" style="border-color: var(--border-soft) !important;">
                    <h6 class="fw-bold mb-0" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                        <i class="bi bi-person-plus-fill me-2" style="color: var(--pink-primary);"></i>Tambah Akun Pengguna
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('pengaturan.user.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small" style="color: var(--ink);">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; border-color: var(--border-soft);">
                                    <i class="bi bi-person text-muted"></i>
                                </span>
                                <input type="text" name="nama" class="form-control bg-light" style="border-radius: 0 12px 12px 0; border-color: var(--border-soft);" placeholder="Contoh: Siska Amelia" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small" style="color: var(--ink);">Alamat Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; border-color: var(--border-soft);">
                                    <i class="bi bi-envelope text-muted"></i>
                                </span>
                                <input type="email" name="email" class="form-control bg-light" style="border-radius: 0 12px 12px 0; border-color: var(--border-soft);" placeholder="nama@yourbaestyle.com" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small" style="color: var(--ink);">Role / Hak Akses <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; border-color: var(--border-soft);">
                                    <i class="bi bi-shield-check text-muted"></i>
                                </span>
                                <select name="id_role" class="form-select bg-light" style="border-radius: 0 12px 12px 0; border-color: var(--border-soft);" required>
                                    <option value="">-- Pilih Role --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ ucfirst($role->nama_role) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small" style="color: var(--ink);">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; border-color: var(--border-soft);">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                                <input type="password" name="password" class="form-control bg-light" style="border-radius: 0 12px 12px 0; border-color: var(--border-soft);" placeholder="Minimal 8 karakter" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small" style="color: var(--ink);">Konfirmasi Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; border-color: var(--border-soft);">
                                    <i class="bi bi-key text-muted"></i>
                                </span>
                                <input type="password" name="password_confirmation" class="form-control bg-light" style="border-radius: 0 12px 12px 0; border-color: var(--border-soft);" placeholder="Ulangi password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-yb w-100 rounded-pill py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: TABEL DAFTAR PENGGUNA -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: #ffffff; border: 1px solid var(--border-soft) !important;">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center" style="border-color: var(--border-soft) !important;">
                    <h6 class="fw-bold mb-0" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                        <i class="bi bi-person-lines-fill me-2" style="color: var(--pink-primary);"></i>Daftar Pengguna Sistem
                    </h6>
                </div>
                <div class="card-body p-0">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="border-color: var(--border-soft);">
                            <thead style="background: var(--pink-soft-2);">
                                <tr>
                                    <th class="ps-4 py-3 text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Pengguna</th>
                                    <th class="py-3 text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Email</th>
                                    <th class="py-3 text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Role</th>
                                    <th class="pe-4 py-3 text-end text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    @php
                                        $initial = strtoupper(substr($user->nama, 0, 1));
                                        $roleName = strtolower($user->role->nama_role ?? 'staff');
                                        
                                        // Dynamic badge style per role
                                        $badgeBg = 'var(--pink-soft)';
                                        $badgeColor = 'var(--ink)';
                                        if (in_array($roleName, ['admin', 'owner'])) {
                                            $badgeBg = 'var(--pink-soft-2)';
                                            $badgeColor = 'var(--pink-primary-dark)';
                                        } elseif (in_array($roleName, ['kasir'])) {
                                            $badgeBg = 'var(--sage-soft)';
                                            $badgeColor = '#2E7D32';
                                        } elseif (in_array($roleName, ['gudang', 'kurir'])) {
                                            $badgeBg = 'var(--amber-soft)';
                                            $badgeColor = '#E65100';
                                        }
                                    @endphp
                                    <tr class="transition-all">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" 
                                                     style="width: 38px; height: 38px; background: var(--pink-primary); color: #ffffff; font-family: 'Quicksand', sans-serif; font-size: 15px; flex-shrink: 0;">
                                                    {{ $initial }}
                                                </div>
                                                <div>
                                                    <span class="fw-bold d-block text-dark" style="font-family: 'Quicksand', sans-serif; font-size: 14.5px;">
                                                        {{ $user->nama }}
                                                    </span>
                                                    @if(auth()->id() == $user->id)
                                                        <span class="badge rounded-pill bg-light text-muted fw-normal" style="font-size: 9.5px;">Akun Anda</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 text-muted small">
                                            <i class="bi bi-envelope me-1 text-pink" style="color: var(--pink-primary);"></i>{{ $user->email }}
                                        </td>
                                        <td class="py-3">
                                            <span class="badge rounded-pill px-3 py-1.5 fw-bold text-capitalize" 
                                                  style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; border: 1px solid var(--border-soft); font-size: 11px;">
                                                <i class="bi bi-shield-check me-1"></i>{{ ucfirst($user->role->nama_role ?? '-') }}
                                            </span>
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#edit-user-{{ $user->id }}"
                                                    aria-expanded="false">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- COLLAPSIBLE FORM EDIT USER -->
                                    <tr class="collapse" id="edit-user-{{ $user->id }}">
                                        <td colspan="4" class="p-0 border-0">
                                            <div class="p-4 m-3 rounded-4 shadow-sm" style="background: var(--pink-soft-2); border: 1px dashed var(--pink-primary);">
                                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: var(--border-soft) !important;">
                                                    <h6 class="fw-bold mb-0" style="color: var(--ink); font-family: 'Quicksand', sans-serif;">
                                                        <i class="bi bi-pencil-square me-2" style="color: var(--pink-primary);"></i>Edit Akun: {{ $user->nama }}
                                                    </h6>
                                                    <button type="button" class="btn-close text-xs" data-bs-toggle="collapse" data-bs-target="#edit-user-{{ $user->id }}"></button>
                                                </div>

                                                <form action="{{ route('pengaturan.user.update', $user->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold small" style="color: var(--ink);">Nama Lengkap</label>
                                                            <input type="text" name="nama" class="form-control bg-white" value="{{ old('nama', $user->nama) }}" required style="border-radius: 12px; border-color: var(--border-soft);">
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold small" style="color: var(--ink);">Email</label>
                                                            <input type="email" name="email" class="form-control bg-white" value="{{ old('email', $user->email) }}" required style="border-radius: 12px; border-color: var(--border-soft);">
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold small" style="color: var(--ink);">Role / Hak Akses</label>
                                                            <select name="id_role" class="form-select bg-white" required style="border-radius: 12px; border-color: var(--border-soft);">
                                                                @foreach($roles as $role)
                                                                    <option value="{{ $role->id }}" {{ (old('id_role', $user->id_role) == $role->id) ? 'selected' : '' }}>
                                                                        {{ ucfirst($role->nama_role) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold small" style="color: var(--ink);">Password Baru <span class="text-muted fw-normal">(Opsional)</span></label>
                                                            <input type="password" name="password" class="form-control bg-white" placeholder="Biarkan kosong jika tidak diubah" style="border-radius: 12px; border-color: var(--border-soft);">
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold small" style="color: var(--ink);">Konfirmasi Password Baru</label>
                                                            <input type="password" name="password_confirmation" class="form-control bg-white" placeholder="Ulangi password baru" style="border-radius: 12px; border-color: var(--border-soft);">
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center pt-2">
                                                        <small class="text-muted" style="font-size: 11px;">
                                                            💡 <i>Password hanya diperbarui jika kolom 'Password Baru' diisi.</i>
                                                        </small>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="collapse" data-bs-target="#edit-user-{{ $user->id }}">
                                                                Batal
                                                            </button>
                                                            <button type="submit" class="btn btn-sm btn-yb rounded-pill px-4 fw-bold shadow-sm">
                                                                <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                                                            </button>
                                                        </div>
                                                    </div>

                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

<style>
    .transition-all { transition: all 0.2s ease-in-out; }
    .table-hover tbody tr:hover { background-color: var(--pink-soft-2) !important; }
</style>
@endsection
