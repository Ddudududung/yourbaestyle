<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $cdgId = DB::table('ms_jenis_pakaian')->where('kode', 'CDG')->value('id');

        if ($cdgId) {
            $carRows = DB::table('ms_jenis_pakaian')
                ->where('kode', 'CAR')
                ->orWhere(function($q) {
                    $q->where('nama', 'Cardigan')->where('kode', '!=', 'CDG');
                })
                ->get();

            foreach ($carRows as $car) {
                DB::table('produk')->where('id_jenis_pakaian', $car->id)->update(['id_jenis_pakaian' => $cdgId]);
                DB::table('ms_jenis_pakaian')->where('id', $car->id)->delete();
            }
        }
    }

    public function down(): void
    {
    }
};
