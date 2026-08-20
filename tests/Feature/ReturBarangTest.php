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
use App\Models\PesananOnline;
use App\Models\Retur;
use Illuminate\Support\Facades\DB;

class ReturBarangTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private Produk $produk;
    private PesananOnline $pesanan;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('ms_role')->insert([
            ['id' => 1, 'nama_role' => 'Owner', 'is_active' => 1],
        ]);

        DB::table('ms_menu')->insert([
            ['id' => 1, 'nama_menu' => 'Retur Barang', 'target_url' => 'retur', 'is_active' => 1],
        ]);

        DB::table('role_menu')->insert([
            ['id_role' => 1, 'id_menu' => 1],
        ]);

        $this->owner = User::create([
            'nama' => 'Owner Retur',
            'email' => 'retur@yourbaestyle.com',
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
            'hpp_otomatis' => 100000,
            'stok' => 10,
            'status' => 'aktif',
        ]);

        $this->pesanan = PesananOnline::create([
            'no_pesanan' => 'ORD-RETUR-001',
            'id_user' => $this->owner->id,
            'platform' => 'shopee',
            'status' => 'diproses',
            'total_harga' => 150000,
            'total_hpp' => 100000,
        ]);
    }

    public function test_retur_layak_jual_increments_stock(): void
    {
        $this->actingAs($this->owner);

        $response = $this->post(route('retur.store'), [
            'transaksi_type' => 'online',
            'transaksi_id' => $this->pesanan->id,
            'id_produk' => $this->produk->id,
            'tanggal' => date('Y-m-d'),
            'alasan' => 'Salah ukuran',
            'kondisi_barang' => 'layak_jual',
            'qty' => 2,
            'ongkir_retur' => 15000,
        ]);

        $response->assertRedirect(route('retur.index'))
                 ->assertSessionHas('success');

        // Stock increased from 10 to 12
        $this->assertEquals(12, $this->produk->fresh()->stok);

        // Record retur saved
        $this->assertDatabaseHas('retur', [
            'id_pesanan_online' => $this->pesanan->id,
            'id_produk' => $this->produk->id,
            'kondisi_barang' => 'layak_jual',
            'qty' => 2,
            'nilai_kerugian' => 0,
        ]);
    }

    public function test_retur_tidak_layak_calculates_financial_loss_without_stock_increment(): void
    {
        $this->actingAs($this->owner);

        // HPP = 100.000, Qty = 2, Ongkir = 20.000 -> Nilai Kerugian = (100.000 * 2) + 20.000 = 220.000
        $response = $this->post(route('retur.store'), [
            'transaksi_type' => 'online',
            'transaksi_id' => $this->pesanan->id,
            'id_produk' => $this->produk->id,
            'tanggal' => date('Y-m-d'),
            'alasan' => 'Baju robek/cacat produksi',
            'kondisi_barang' => 'tidak_layak',
            'qty' => 2,
            'ongkir_retur' => 20000,
        ]);

        $response->assertRedirect(route('retur.index'))
                 ->assertSessionHas('success');

        // Stock stays 10
        $this->assertEquals(10, $this->produk->fresh()->stok);

        $this->assertDatabaseHas('retur', [
            'id_pesanan_online' => $this->pesanan->id,
            'id_produk' => $this->produk->id,
            'kondisi_barang' => 'tidak_layak',
            'qty' => 2,
            'nilai_kerugian' => 220000,
        ]);
    }

    public function test_retur_search_barcode_ajax(): void
    {
        $this->actingAs($this->owner);

        $response = $this->get('/retur/search-barcode?barcode=CDG-PLS-BLK-001');
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'produk' => [
                         'kode_produk' => 'CDG-PLS-BLK-001',
                         'nama_produk' => 'Cardigan Polos Hitam',
                     ]
                 ]);
    }
}
