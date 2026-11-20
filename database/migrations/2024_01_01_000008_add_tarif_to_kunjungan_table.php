<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kunjungan bisa berbayar (per latihan) atau gratis (member paket aktif).
     */
    public function up(): void
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->decimal('tarif', 12, 2)->default(0)->after('waktu_kunjungan')
                ->comment('0 = gratis karena punya paket aktif');
            $table->string('jenis', 20)->default('paket')->after('tarif')
                ->comment('paket = ikut langganan, latihan = bayar per kedatangan');
        });
    }

    public function down(): void
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->dropColumn(['tarif', 'jenis']);
        });
    }
};
