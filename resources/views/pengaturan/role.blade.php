@extends('layouts.app')

@section('title', 'Kelola Role & Hak Akses ')

@section('content')

<div class="row g-3">
    <div class="col-md-4">
        <div class="card-yb p-4">
            <h6 class="fw-bold mb-3" style="font-family:'Quicksand',sans-serif">Tambah Role Baru</h6>
            <form action="{{ route('pengaturan.role.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nama Role</label>
                    <input type="text" name="nama_role" class="form-control" placeholder="contoh: kurir" required>
                </div>
                <label class="form-label">Hak Akses Menu</label>
                <div class="mb-3" style="max-height:220px;overflow-y:auto;background:var(--pink-soft-2);border-radius:14px;padding:12px">
                    @foreach($menus as $menu)
                    <div class="form-check">
                        <input type="checkbox" name="menu_ids[]" value="{{ $menu->id }}" class="form-check-input">
                        <label class="form-check-label small fw-semibold">{{ $menu->nama_menu }}</label>
                    </div>
                    @endforeach
                </div>
                <button class="btn btn-yb w-100">Simpan Role Baru</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card-yb p-4">
            <h6 class="fw-bold mb-3" style="font-family:'Quicksand',sans-serif">Daftar Role</h6>
            @foreach($roles as $role)
            <div style="background:var(--pink-soft-2);border-radius:18px;padding:16px;margin-bottom:12px">
                <form action="{{ route('pengaturan.role.update',$role->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <input type="text" name="nama_role" class="form-control form-control-sm w-auto" value="{{ $role->nama_role }}" style="max-width:160px">
                        <div class="d-flex align-items-center gap-2">
                            <label class="small fw-semibold">Aktif</label>
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ $role->is_active ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="row g-1 mb-3">
                        @foreach($menus as $menu)
                        <div class="col-4">
                            <div class="form-check">
                                <input type="checkbox" name="menu_ids[]" value="{{ $menu->id }}" class="form-check-input"
                                    {{ $role->menus->contains($menu->id) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" style="font-size:11px">{{ $menu->nama_menu }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button class="btn btn-yb-outline btn-sm">Simpan Perubahan</button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
