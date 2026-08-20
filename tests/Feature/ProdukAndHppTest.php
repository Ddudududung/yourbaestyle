<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Produk;
use App\Models\MsWarna;
use App\Models\MsJenisPakaian;
use App\Models\MsModel;
use App\Models\Pemasok;
use Illuminate\Support\Facades\DB;

class ProdukAndHppTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private MsWarna $warna;
    private MsJenisPakaian $jenis;
    private MsModel $model;
    private Pemasok $pemasok;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('ms_role')->insert([
            ['id' => 1, 'nama_role' => 'Owner', 'is_active' => 1],
            ['id' => 2, 'nama_role' => 'Kasir', 'is_active' => 1],
        ]);

        DB::table('ms_menu')->insert([
            ['id' => 1, 'nama_menu' => 'Produk', 'target_url' => 'produk', 'is_active' => 1],
            ['id' => 2, 'nama_menu' => 'HPP Produk', 'target_url' => 'produk/hpp', 'is_active' => 1],
        ]);

        DB::table('role_menu')->insert([
            ['id_role' => 1, 'id_menu' => 1],
            ['id_role' => 1, 'id_menu' => 2],
        ]);

        $this->owner = User::create([
            'nama' => 'Owner Admin',
            'email' => 'owner@yourbaestyle.com',
            'password' => bcrypt('password'),
            'id_role' => 1,
        ]);

        $this->warna = MsWarna::create(['nama' => 'Hitam', 'nama_warna' => 'Hitam', 'kode' => 'BLK']);
        $this->jenis = MsJenisPakaian::create(['nama' => 'Cardigan', 'nama_jenis' => 'Cardigan', 'kode' => 'CDG']);
        $this->model = MsModel::create(['nama' => 'Polos', 'nama_model' => 'Polos', 'kode' => 'PLS']);
        $this->pemasok = Pemasok::create(['nama_pemasok' => 'Pemasok Utama', 'no_telepon' => '08123456789']);
    }

    public function test_can_view_produk_index(): void
    {
        $this->actingAs($this->owner);

        $response = $this->get(route('produk.index'));
        $response->assertStatus(200);
    }

    public function test_can_store_new_produk(): void
    {
        $this->actingAs($this->owner);

        $response = $this->post(route('produk.store'), [
            'nama_produk' => 'Cardigan Rajut Baru',
            'id_jenis_pakaian' => $this->jenis->id,
            'id_model' => $this->model->id,
            'id_warna' => $this->warna->id,
            'id_pemasok' => $this->pemasok->id,
            'harga_jual' => 120000,
            'jumlah' => 25,
            'harga_beli_per_unit' => 80000,
            'status' => 'aktif',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('produk', [
            'id_jenis_pakaian' => $this->jenis->id,
            'harga_jual' => 120000,
            'stok' => 25,
        ]);
    }

    public function test_can_view_and_update_hpp(): void
    {
        $this->actingAs($this->owner);

        $produk = Produk::create([
            'kode_produk' => 'CDG-PLS-BLK-001',
            'nama_produk' => 'Cardigan Polos Hitam',
            'id_jenis_pakaian' => $this->jenis->id,
            'id_model' => $this->model->id,
            'id_warna' => $this->warna->id,
            'id_pemasok' => $this->pemasok->id,
            'harga_jual' => 150000,
            'hpp_otomatis' => 90000,
            'stok' => 10,
            'status' => 'aktif',
        ]);

        $responseHppPage = $this->get(route('produk.hpp'));
        $responseHppPage->assertStatus(200);

        // Update HPP realisasi
        $responseUpdate = $this->put(route('produk.hpp.update', $produk->id), [
            'hpp_realisasi' => 95000,
        ]);

        $responseUpdate->assertRedirect(route('produk.hpp'));
        $this->assertDatabaseHas('produk', [
            'id' => $produk->id,
            'hpp_realisasi' => 95000,
        ]);

        // Access accessor attribute hpp_aktif
        $this->assertEquals(95000, $produk->fresh()->hpp_aktif);
    }

    public function test_can_delete_produk(): void
    {
        $this->actingAs($this->owner);

        $produk = Produk::create([
            'kode_produk' => 'CDG-PLS-BLK-002',
            'nama_produk' => 'Cardigan Sample',
            'id_jenis_pakaian' => $this->jenis->id,
            'id_model' => $this->model->id,
            'id_warna' => $this->warna->id,
            'id_pemasok' => $this->pemasok->id,
            'harga_jual' => 100000,
            'hpp_otomatis' => 50000,
            'stok' => 5,
            'status' => 'aktif',
        ]);

        $response = $this->delete(route('produk.destroy', $produk->id));
        $response->assertRedirect(route('produk.index'));
        $this->assertDatabaseMissing('produk', ['id' => $produk->id]);
    }
}
