<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\MsUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthAndRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic roles
        DB::table('ms_role')->insert([
            ['id' => 1, 'nama_role' => 'Owner', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama_role' => 'Kasir', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Seed basic menus for menu.access middleware
        DB::table('ms_menu')->insert([
            ['id' => 1, 'nama_menu' => 'Dashboard', 'target_url' => 'dashboard', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama_menu' => 'POS Kasir', 'target_url' => 'pos', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Assign access permissions
        DB::table('role_menu')->insert([
            ['id_role' => 1, 'id_menu' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_role' => 1, 'id_menu' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_role' => 2, 'id_menu' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_role' => 2, 'id_menu' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function test_guest_is_redirected_to_login_page(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = MsUser::create([
            'nama' => 'Owner User',
            'email' => 'owner@example.com',
            'password' => Hash::make('password123'),
            'id_role' => 1,
        ]);

        $response = $this->post('/login', [
            'email' => 'owner@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        MsUser::create([
            'nama' => 'Kasir User',
            'email' => 'kasir@example.com',
            'password' => Hash::make('password123'),
            'id_role' => 2,
        ]);

        $response = $this->post('/login', [
            'email' => 'kasir@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = MsUser::create([
            'nama' => 'User Test',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'id_role' => 1,
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_non_owner_cannot_access_owner_only_settings(): void
    {
        $kasirUser = MsUser::create([
            'nama' => 'Kasir User',
            'email' => 'kasir@example.com',
            'password' => Hash::make('password123'),
            'id_role' => 2, // Non-owner
        ]);

        $this->actingAs($kasirUser);

        $response = $this->get('/pengaturan/role');
        $response->assertStatus(403);
    }

    public function test_owner_can_access_owner_settings(): void
    {
        $ownerUser = MsUser::create([
            'nama' => 'Owner User',
            'email' => 'owner@example.com',
            'password' => Hash::make('password123'),
            'id_role' => 1, // Owner
        ]);

        $this->actingAs($ownerUser);

        $response = $this->get('/pengaturan/role');
        $response->assertStatus(200);
    }
}
