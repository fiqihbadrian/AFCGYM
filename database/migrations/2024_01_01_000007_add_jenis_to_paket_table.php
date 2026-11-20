<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Paket dapat berjenis:
     * - paket   : berdurasi (mingguan, bulanan, dst)
     * - latihan : bayar per kedatangan, berlaku sampai gym tutup
     */
    public function up(): void
    {
        Schema::table('paket', function (Blueprint $table) {
            $table->string('jenis', 20)->default('paket')->after('nama')
                ->comment('paket = berdurasi, latihan = bayar per kedatangan');
        });

        // Paket latihan tidak punya durasi (0 = tidak terikat hari)
        Schema::table('paket', function (Blueprint $table) {
            $table->integer('durasi_hari')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('paket', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }
};
