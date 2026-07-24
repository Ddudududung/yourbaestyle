@extends('layouts.app')

@section('title', 'Kelola Pengguna ')

@section('content')

<div class="row g-3">
    <div class="col-md-4">
        <div class="card-yb p-4">
            <h6 class="fw-bold mb-3" style="font-family:'Quicksand',sans-serif">Tambah Akun Pengguna</h6>
            <form action="{{ route('pengaturan.user.store') }}" method="POST">
                @csrf
                <div class="mb-2">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control form-control-sm" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control form-control-sm" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Role</label>
                    <select name="id_role" class="form-select form-select-sm" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->nama_role) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control form-control-sm" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control form-control-sm" required>
                </div>
                <button class="btn btn-yb w-100">Tambah Pengguna</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card-yb p-4">
            <h6 class="fw-bold mb-3" style="font-family:'Quicksand',sans-serif">Daftar Pengguna</h6>
            <table class="table table-yb align-middle">
                <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="fw-semibold">{{ $user->nama }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge bg-light">{{ ucfirst($user->role->nama_role) }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#edit-{{ $user->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="collapse" id="edit-{{ $user->id }}">
                        <td colspan="4">
                            <form action="{{ route('pengaturan.user.update',$user->id) }}" method="POST" class="row g-2 p-3" style="background:var(--pink-soft-2);border-radius:16px">
                                @csrf @method('PUT')
                                <div class="col-3"><input type="text" name="nama" class="form-control form-control-sm" value="{{ $user->nama }}"></div>
                                <div class="col-3"><input type="email" name="email" class="form-control form-control-sm" value="{{ $user->email }}"></div>
                                <div class="col-2">
                                    <select name="id_role" class="form-select form-select-sm">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ $user->id_role==$role->id?'selected':'' }}>{{ ucfirst($role->nama_role) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2"><input type="password" name="password" class="form-control form-control-sm" placeholder="Password baru"></div>
                                <div class="col-2"><input type="password" name="password_confirmation" class="form-control form-control-sm" placeholder="Konfirmasi"></div>
                                <div class="col-12 mt-2"><button class="btn btn-sm btn-yb">Simpan</button></div>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
