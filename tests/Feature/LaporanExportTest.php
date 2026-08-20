<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\TransaksiPos;
use App\Models\PesananOnline;
use App\Models\Retur;
use Illuminate\Support\Facades\DB;

class LaporanExportTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('ms_role')->insert([
            ['id' => 1, 'nama_role' => 'Owner', 'is_active' => 1],
        ]);

        DB::table('ms_menu')->insert([
            ['id' => 1, 'nama_menu' => 'Laporan', 'target_url' => 'laporan', 'is_active' => 1],
        ]);

        DB::table('role_menu')->insert([
            ['id_role' => 1, 'id_menu' => 1],
        ]);

        $this->owner = User::create([
            'nama' => 'Owner Laporan',
            'email' => 'laporan@yourbaestyle.com',
            'password' => bcrypt('password'),
            'id_role' => 1,
        ]);
    }

    public function test_can_view_laporan_index_with_date_filter(): void
    {
        $this->actingAs($this->owner);

        // Seed POS transaction
        TransaksiPos::create([
            'kode_transaksi' => 'TRX-LAP-001',
            'id_user' => $this->owner->id,
            'tanggal' => date('Y-m-d H:i:s'),
            'total_harga' => 200000,
            'total_hpp' => 120000,
            'diskon' => 0,
            'metode_bayar' => 'qris',
        ]);

        $response = $this->get(route('laporan.index', [
            'dari' => date('Y-m-d'),
            'sampai' => date('Y-m-d'),
        ]));

        $response->assertStatus(200);
    }

    public function test_can_download_laporan_pdf(): void
    {
        $this->actingAs($this->owner);

        $response = $this->get(route('laporan.unduh', [
            'dari' => date('Y-m-d'),
            'sampai' => date('Y-m-d'),
        ]));

        $response->assertStatus(200);
    }

    public function test_can_download_laporan_excel(): void
    {
        $this->actingAs($this->owner);

        $response = $this->get(route('laporan.unduh_excel', [
            'dari' => date('Y-m-d'),
            'sampai' => date('Y-m-d'),
        ]));

        $response->assertStatus(200);
    }
}
