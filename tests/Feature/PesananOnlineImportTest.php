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
use App\Models\PesananOnline;
use App\Models\ReviewMappingManual;
use Illuminate\Support\Facades\DB;

class PesananOnlineImportTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private Produk $produk;
    private SesiLive $sesi;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('ms_role')->insert([
            ['id' => 1, 'nama_role' => 'Owner', 'is_active' => 1],
        ]);

        DB::table('ms_menu')->insert([
            ['id' => 1, 'nama_menu' => 'Pesanan Online', 'target_url' => 'pesanan', 'is_active' => 1],
        ]);

        DB::table('role_menu')->insert([
            ['id_role' => 1, 'id_menu' => 1],
        ]);

        $this->owner = User::create([
            'nama' => 'Owner Online',
            'email' => 'pesanan@yourbaestyle.com',
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
            'harga_jual' => 150000,
            'hpp_otomatis' => 90000,
            'stok' => 20,
            'status' => 'aktif',
        ]);

        $this->sesi = SesiLive::create([
            'tanggal_live' => date('Y-m-d'),
            'nama_sesi' => 'Sesi Utama',
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'status' => 'ongoing',
        ]);

        KatalogLive::create([
            'kode_live' => 'IYB 5',
            'tanggal_live' => date('Y-m-d'),
            'id_sesi_live' => $this->sesi->id,
            'id_produk' => $this->produk->id,
            'harga_live' => 150000,
        ]);
    }

    public function test_import_proses_auto_mapping_success(): void
    {
        $this->actingAs($this->owner);

        $payload = [
            'pesanan' => [
                [
                    'no_pesanan' => 'ORD-SHOPEE-1001',
                    'nama_pembeli' => 'Budi Santoso',
                    'tanggal' => date('Y-m-d H:i:s'),
                    'variasi' => 'IYB 5',
                    'qty' => 2,
                    'harga_satuan' => 150000,
                    'total_harga' => 300000,
                ]
            ]
        ];

        $response = $this->post(route('pesanan.import.proses'), $payload);
        $response->assertRedirect(route('pesanan.index'));

        // Check product stock reduced by 2 (from 20 to 18)
        $this->assertEquals(18, $this->produk->fresh()->stok);

        // Check order recorded
        $this->assertDatabaseHas('pesanan_online', [
            'no_pesanan' => 'ORD-SHOPEE-1001',
            'status' => 'diproses',
            'status_mapping' => 'sukses',
        ]);
    }

    public function test_import_proses_unmapped_creates_manual_review(): void
    {
        $this->actingAs($this->owner);

        $payload = [
            'pesanan' => [
                [
                    'no_pesanan' => 'ORD-TIKTOK-9999',
                    'nama_pembeli' => 'Siti Aminah',
                    'tanggal' => date('Y-m-d H:i:s'),
                    'variasi' => 'KODE_TIDAK_ADA',
                    'qty' => 1,
                    'harga_satuan' => 100000,
                    'total_harga' => 100000,
                ]
            ]
        ];

        $response = $this->post(route('pesanan.import.proses'), $payload);
        $response->assertRedirect(route('pesanan.index'));

        // Stock remains 20
        $this->assertEquals(20, $this->produk->fresh()->stok);

        // Created ReviewMappingManual entry
        $this->assertDatabaseHas('review_mapping_manual', [
            'no_pesanan' => 'ORD-TIKTOK-9999',
            'kode_live_raw' => 'KODE_TIDAK_ADA',
            'status_resolusi' => 'pending',
        ]);
    }

    public function test_resolve_manual_mapping_success(): void
    {
        $this->actingAs($this->owner);

        $pesanan = PesananOnline::create([
            'no_pesanan' => 'ORD-TIKTOK-9999',
            'platform' => 'tiktok',
            'status' => 'draft',
            'total_harga' => 150000,
            'total_hpp' => 0,
        ]);

        $review = ReviewMappingManual::create([
            'no_pesanan' => 'ORD-TIKTOK-9999',
            'kode_live_raw' => 'UNMAPPED_CODE',
            'tanggal_live' => date('Y-m-d'),
            'qty_order' => 3,
            'status_resolusi' => 'pending',
        ]);

        $response = $this->patch(route('pesanan.resolve_manual', $review->id), [
            'action' => 'mapped',
            'id_produk_mapping' => $this->produk->id,
        ]);

        $response->assertSessionHas('success');

        // Review resolved
        $this->assertDatabaseHas('review_mapping_manual', [
            'id' => $review->id,
            'status_resolusi' => 'mapped',
            'id_produk_mapping' => $this->produk->id,
        ]);

        // Stock reduced by 3 (20 -> 17)
        $this->assertEquals(17, $this->produk->fresh()->stok);
    }
}
