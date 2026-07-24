<?php

namespace App\Http\Controllers;

use App\Models\MsRole;
use App\Models\MsMenu;
use App\Models\MsUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PengaturanController extends Controller
{
    // ============================================================
    // ROLE INDEX — Halaman kelola role & hak akses
    // ============================================================
    public function roleIndex()
    {
        $roles = MsRole::with('menus')->get();
        $menus = MsMenu::orderBy('order_menu')->get();
        return view('pengaturan.role', compact('roles', 'menus'));
    }

    // ============================================================
    // ROLE STORE — Tambah role baru
    // ============================================================
    public function roleStore(Request $request)
    {
        $request->validate([
            'nama_role'  => 'required|string|max:100|unique:ms_role,nama_role',
            'menu_ids'   => 'nullable|array',
            'menu_ids.*' => 'exists:ms_menu,id',
        ], [
            'nama_role.unique' => 'Nama role sudah digunakan, gunakan nama lain.',
        ]);

        DB::beginTransaction();
        try {
            $role = MsRole::create([
                'nama_role' => strtolower($request->nama_role),
                'is_active' => true,
            ]);

            // Simpan hak akses menu
            if ($request->filled('menu_ids')) {
                foreach ($request->menu_ids as $menuId) {
                    DB::table('role_menu')->insert([
                        'id_role'    => $role->id,
                        'id_menu'    => $menuId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', "Role '{$role->nama_role}' berhasil ditambahkan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ============================================================
    // ROLE UPDATE — Edit nama role atau hak akses menu
    // ============================================================
    public function roleUpdate(Request $request, string $id)
    {
        $request->validate([
            'nama_role'  => "required|string|max:100|unique:ms_role,nama_role,{$id}",
            'is_active'  => 'required|boolean',
            'menu_ids'   => 'nullable|array',
            'menu_ids.*' => 'exists:ms_menu,id',
        ]);

        DB::beginTransaction();
        try {
            $role = MsRole::findOrFail($id);
            $role->update([
                'nama_role' => strtolower($request->nama_role),
                'is_active' => $request->is_active,
            ]);

            // Reset dan simpan ulang hak akses menu
            DB::table('role_menu')->where('id_role', $id)->delete();
            if ($request->filled('menu_ids')) {
                foreach ($request->menu_ids as $menuId) {
                    DB::table('role_menu')->insert([
                        'id_role'    => $role->id,
                        'id_menu'    => $menuId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', "Role berhasil diperbarui.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ============================================================
    // USER INDEX — Halaman kelola pengguna
    // ============================================================
    public function userIndex()
    {
        $users = MsUser::with('role')->orderBy('nama')->get();
        $roles = MsRole::where('is_active', true)->get();
        return view('pengaturan.user', compact('users', 'roles'));
    }

    // ============================================================
    // USER STORE — Tambah akun pengguna baru
    // ============================================================
    public function userStore(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|unique:ms_user,email',
            'password' => 'required|string|min:8|confirmed',
            'id_role'  => 'required|exists:ms_role,id',
        ]);

        MsUser::create([
            'id_role'  => $request->id_role,
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', "Akun {$request->nama} berhasil ditambahkan.");
    }

    // ============================================================
    // USER UPDATE — Edit atau nonaktifkan akun
    // ============================================================
    public function userUpdate(Request $request, string $id)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => "required|email|unique:ms_user,email,{$id}",
            'id_role'  => 'required|exists:ms_role,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = MsUser::findOrFail($id);

        $data = [
            'nama'    => $request->nama,
            'email'   => $request->email,
            'id_role' => $request->id_role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', "Akun {$user->nama} berhasil diperbarui.");
    }
}
