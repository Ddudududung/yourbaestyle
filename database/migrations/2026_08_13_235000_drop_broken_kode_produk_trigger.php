<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS generate_kode_produk_v2');
        DB::unprepared('DROP TRIGGER IF EXISTS generate_kode_produk');
    }

    public function down(): void
    {
    }
};
