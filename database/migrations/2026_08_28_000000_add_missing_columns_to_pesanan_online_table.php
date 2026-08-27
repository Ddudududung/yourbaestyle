<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan_online', function (Blueprint $table) {
            if (!Schema::hasColumn('pesanan_online', 'id_user')) {
                $table->unsignedBigInteger('id_user')->nullable()->after('no_pesanan');
            }
            if (!Schema::hasColumn('pesanan_online', 'no_resi')) {
                $table->string('no_resi', 50)->nullable()->after('platform');
            }
            if (!Schema::hasColumn('pesanan_online', 'ongkir')) {
                $table->decimal('ongkir', 15, 2)->default(0)->after('total_hpp');
            }
            if (!Schema::hasColumn('pesanan_online', 'format_csv')) {
                $table->string('format_csv', 50)->nullable()->after('platform');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pesanan_online', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('pesanan_online', 'id_user')) $cols[] = 'id_user';
            if (Schema::hasColumn('pesanan_online', 'no_resi')) $cols[] = 'no_resi';
            if (Schema::hasColumn('pesanan_online', 'ongkir')) $cols[] = 'ongkir';
            if (Schema::hasColumn('pesanan_online', 'format_csv')) $cols[] = 'format_csv';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
