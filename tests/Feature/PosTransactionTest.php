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
use App\Models\TransaksiPos;
use Illuminate\Support\Facades\DB;

class PosTransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir;
    private Produk $produk;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('ms_role')->insert([
            ['id' => 1, 'nama_role' => 'Owner', 'is_active' => 1],
            ['id' => 2, 'nama_role' => 'Kasir', 'is_active' => 1],
        ]);

        DB::table('ms_menu')->insert([
            ['id' => 1, 'nama_menu' => 'POS Kasir', 'target_url' => 'pos', 'is_active' => 1],
        ]);

        DB::table('role_menu')->insert([
            ['id_role' => 2, 'id_menu' => 1],
        ]);

        $this->kasir = User::create([
            'nama' => 'Kasir 1',
            'email' => 'kasir1@yourbaestyle.com',
            'password' => bcrypt('password'),
            'id_role' => 2,
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
            'harga_jual' => 100000,
            'hpp_otomatis' => 60000,
            'stok' => 5,
            'status' => 'aktif',
        ]);
    }

    public function test_pos_checkout_successful(): void
    {
        $this->actingAs($this->kasir);

        $response = $this->postJson(route('pos.proses'), [
            'items' => [
                [
                    'id_produk' => $this->produk->id,
                    'qty' => 2,
                ]
            ],
            'metode_bayar' => 'tunai',
            'diskon' => 10000,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        // Assert stock reduced from 5 to 3
        $this->assertEquals(3, $this->produk->fresh()->stok);

        // Assert total harga = (100.000 * 2) - 10.000 = 190.000
        $this->assertDatabaseHas('transaksi_pos', [
            'id_user' => $this->kasir->id,
            'total_harga' => 190000,
            'diskon' => 10000,
            'metode_bayar' => 'tunai',
        ]);
    }

    public function test_anomaly_pos_checkout_overselling_rejected(): void
    {
        $this->actingAs($this->kasir);

        // Requesting 10 items when stock is only 5
        $response = $this->postJson(route('pos.proses'), [
            'items' => [
                [
                    'id_produk' => $this->produk->id,
                    'qty' => 10,
                ]
            ],
            'metode_bayar' => 'qris',
            'diskon' => 0,
        ]);

        $response->assertStatus(422)
                 ->assertJson(['success' => false]);

        // Stock must remain unchanged
        $this->assertEquals(5, $this->produk->fresh()->stok);
    }

    public function test_anomaly_pos_checkout_discount_exceeds_total(): void
    {
        $this->actingAs($this->kasir);

        // Subtotal = 100.000, diskon = 150.000
        $response = $this->postJson(route('pos.proses'), [
            'items' => [
                [
                    'id_produk' => $this->produk->id,
                    'qty' => 1,
                ]
            ],
            'metode_bayar' => 'transfer',
            'diskon' => 150000,
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Diskon tidak boleh melebihi total harga transaksi.',
                 ]);

        // Stock must remain unchanged
        $this->assertEquals(5, $this->produk->fresh()->stok);
    }

    public function test_view_pos_history_and_detail(): void
    {
        $this->actingAs($this->kasir);

        $transaksi = TransaksiPos::create([
            'kode_transaksi' => 'TRX-TEST-001',
            'id_user' => $this->kasir->id,
            'tanggal' => now(),
            'total_harga' => 100000,
            'total_hpp' => 60000,
            'diskon' => 0,
            'metode_bayar' => 'tunai',
        ]);

        $responseIndex = $this->get(route('transaksi.index'));
        $responseIndex->assertStatus(200);

        $responseShow = $this->get(route('transaksi.show', $transaksi->id));
        $responseShow->assertStatus(200);
    }
}
