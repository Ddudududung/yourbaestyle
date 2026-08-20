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
use App\Models\SesiLive;
use App\Models\KatalogLive;
use Illuminate\Support\Facades\DB;

class SesiLiveTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private Produk $produk;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('ms_role')->insert([
            ['id' => 1, 'nama_role' => 'Owner', 'is_active' => 1],
        ]);

        DB::table('ms_menu')->insert([
            ['id' => 1, 'nama_menu' => 'Sesi Live', 'target_url' => 'sesi-live', 'is_active' => 1],
        ]);

        DB::table('role_menu')->insert([
            ['id_role' => 1, 'id_menu' => 1],
        ]);

        $this->owner = User::create([
            'nama' => 'Owner Live',
            'email' => 'live@yourbaestyle.com',
            'password' => bcrypt('password'),
            'id_role' => 1,
        ]);

        $warna = MsWarna::create(['nama' => 'Hitam', 'nama_warna' => 'Hitam', 'kode' => 'BLK']);
        $jenis = MsJenisPakaian::create(['nama' => 'Cardigan', 'nama_jenis' => 'Cardigan', 'kode' => 'CDG']);
        $model = MsModel::create(['nama' => 'Polos', 'nama_model' => 'Polos', 'kode' => 'PLS']);
        $pemasok = Pemasok::create(['nama_pemasok' => 'Pemasok Utama', 'no_telepon' => '08123456789']);

        $this->produk = Produk::create([
            'kode_produk' => 'CDG-PLS-BLK-001',
            'nama_produk' => 'Cardigan Polos Hitam',
            'id_jenis_pakaian' => $jenis->id,
            'id_model' => $model->id,
            'id_warna' => $warna->id,
            'id_pemasok' => $pemasok->id,
            'harga_jual' => 120000,
            'hpp_otomatis' => 70000,
            'stok' => 15,
            'status' => 'aktif',
        ]);
    }

    public function test_can_view_sesi_live_index(): void
    {
        $this->actingAs($this->owner);

        $response = $this->get(route('sesi_live.index'));
        $response->assertStatus(200);
    }

    public function test_can_store_jadwal_and_katalog_live(): void
    {
        $this->actingAs($this->owner);

        // Store Sesi Live Schedule
        $responseJadwal = $this->post(route('sesi_live.store_jadwal'), [
            'nama_sesi' => 'Sesi Malam TikTok',
            'platform' => 'shopee',
            'tanggal_live' => date('Y-m-d'),
            'jam_mulai' => '19:00',
            'jam_selesai' => '22:00',
            'status' => 'ongoing',
        ]);
        $responseJadwal->assertSessionHas('success');

        $sesi = SesiLive::first();
        $this->assertNotNull($sesi);

        // Store Mapping Kode Live (Co-Host)
        $responseStore = $this->post(route('sesi_live.store'), [
            'tanggal_live' => date('Y-m-d'),
            'id_sesi_live' => $sesi->id,
            'kode_live' => 'IYB 1',
            'id_produk' => $this->produk->id,
            'harga_live' => '110.000',
        ]);

        $responseStore->assertSessionHas('success');
        $this->assertDatabaseHas('katalog_live', [
            'kode_live' => 'IYB 1',
            'id_produk' => $this->produk->id,
            'id_sesi_live' => $sesi->id,
        ]);
    }

    public function test_can_delete_katalog_live_mapping(): void
    {
        $this->actingAs($this->owner);

        $sesi = SesiLive::create([
            'tanggal_live' => date('Y-m-d'),
            'nama_sesi' => 'Sesi Siang',
            'jam_mulai' => '12:00',
            'jam_selesai' => '15:00',
            'status' => 'selesai',
        ]);

        $katalog = KatalogLive::create([
            'kode_live' => 'FIX 2',
            'tanggal_live' => date('Y-m-d'),
            'id_sesi_live' => $sesi->id,
            'id_produk' => $this->produk->id,
            'harga_live' => 120000,
        ]);

        $response = $this->delete(route('sesi_live.destroy', $katalog->id));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('katalog_live', ['id' => $katalog->id]);
    }
}
