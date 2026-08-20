<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\MsWarna;
use App\Models\MsJenisPakaian;
use App\Models\MsModel;
use App\Models\Produk;
use App\Models\Pemasok;
use Illuminate\Support\Facades\DB;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('ms_role')->insert(['id' => 1, 'nama_role' => 'Owner', 'is_active' => 1]);
        
        $this->owner = User::create([
            'nama' => 'Owner Admin',
            'email' => 'owner@yourbaestyle.com',
            'password' => bcrypt('password'),
            'id_role' => 1,
        ]);
    }

    public function test_store_and_update_and_delete_warna(): void
    {
        $this->actingAs($this->owner);

        // 1. Store new Warna
        $response = $this->post(route('master_data.warna.store'), [
            'nama' => 'Misty Blue',
            'kode' => 'MBL',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('ms_warna', ['nama' => 'Misty Blue', 'kode' => 'MBL']);

        $warna = MsWarna::where('kode', 'MBL')->first();

        // 2. Anomaly: Duplicate name store
        $responseDup = $this->post(route('master_data.warna.store'), [
            'nama' => 'Misty Blue',
        ]);
        $responseDup->assertSessionHas('error');

        // 3. Update Warna
        $responseUpdate = $this->put(route('master_data.warna.update', $warna->id), [
            'nama' => 'Misty Blue Ocean',
        ]);
        $responseUpdate->assertSessionHas('success');
        $this->assertDatabaseHas('ms_warna', ['id' => $warna->id, 'nama' => 'Misty Blue Ocean']);

        // 4. Delete Warna when not used
        $responseDel = $this->delete(route('master_data.warna.destroy', $warna->id));
        $responseDel->assertSessionHas('success');
        $this->assertDatabaseMissing('ms_warna', ['id' => $warna->id]);
    }

    public function test_cannot_delete_warna_when_used_by_product(): void
    {
        $this->actingAs($this->owner);

        $warna = MsWarna::create(['nama' => 'Merah Cabai', 'nama_warna' => 'Merah Cabai', 'kode' => 'MRC']);
        $jenis = MsJenisPakaian::create(['nama' => 'Kemeja', 'nama_jenis' => 'Kemeja', 'kode' => 'KMJ']);
        $model = MsModel::create(['nama' => 'Slimfit', 'nama_model' => 'Slimfit', 'kode' => 'SLM']);
        $pemasok = Pemasok::create(['nama_pemasok' => 'Supplier A', 'no_telepon' => '0812345']);

        Produk::create([
            'kode_produk' => 'KMJ-SLM-MRC-001',
            'nama_produk' => 'Kemeja Slimfit Merah',
            'id_jenis' => $jenis->id,
            'id_model' => $model->id,
            'id_warna' => $warna->id,
            'id_pemasok' => $pemasok->id,
            'harga_jual' => 150000,
            'hpp_otomatis' => 100000,
            'stok' => 10,
            'status' => 'aktif',
        ]);

        $response = $this->delete(route('master_data.warna.destroy', $warna->id));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('ms_warna', ['id' => $warna->id]);
    }

    public function test_store_and_delete_jenis_pakaian(): void
    {
        $this->actingAs($this->owner);

        $response = $this->post(route('master_data.jenis.store'), [
            'nama' => 'Jaket Leather',
            'kode' => 'JKL',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('ms_jenis_pakaian', ['nama' => 'Jaket Leather']);

        $jenis = MsJenisPakaian::where('kode', 'JKL')->first();

        $responseDel = $this->delete(route('master_data.jenis.destroy', $jenis->id));
        $responseDel->assertSessionHas('success');
        $this->assertDatabaseMissing('ms_jenis_pakaian', ['id' => $jenis->id]);
    }

    public function test_store_and_delete_model(): void
    {
        $this->actingAs($this->owner);

        $response = $this->post(route('master_data.model.store'), [
            'nama' => 'Batik Modern',
            'kode' => 'BMD',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('ms_model', ['nama' => 'Batik Modern']);

        $model = MsModel::where('kode', 'BMD')->first();

        $responseDel = $this->delete(route('master_data.model.destroy', $model->id));
        $responseDel->assertSessionHas('success');
        $this->assertDatabaseMissing('ms_model', ['id' => $model->id]);
    }
}
